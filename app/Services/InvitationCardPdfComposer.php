<?php

namespace App\Services;

use App\Models\CardDesign;
use App\Models\Invitee;
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Output\QRGdImagePNG;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use setasign\Fpdi\Fpdi;

class InvitationCardPdfComposer
{
    /**
     * Build a single-page PDF: user's template + invitee's QR at the saved layout.
     *
     * @return string Absolute path to a temp .pdf file (caller should delete after send)
     */
    public function compose(CardDesign $design, Invitee $invitee): string
    {
        if ($design->design_type !== 'pdf' || ! $design->pdf_file_path) {
            throw new RuntimeException('No PDF card design is configured for this event.');
        }

        $sourcePath = Storage::disk('public')->path($design->pdf_file_path);
        if (! is_readable($sourcePath)) {
            throw new RuntimeException('The invitation PDF file is missing from storage.');
        }

        $qrUrl = route('invitee.show', ['code' => $invitee->invitation_code], true);
        $tmpQr = $this->renderQrPng($qrUrl);

        try {
            $pdf = new Fpdi();
            $pdf->setSourceFile($sourcePath);
            $tplId = $pdf->importPage(1);
            $size = $pdf->getTemplateSize($tplId);

            $wMm = $size['width'] * 25.4 / 72;
            $hMm = $size['height'] * 25.4 / 72;
            $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';

            $pdf->AddPage($orientation, [$wMm, $hMm]);
            $pdf->useTemplate($tplId, 0, 0, $wMm, $hMm);

            [$nx, $ny, $nw] = $this->normalizedLayout($design);

            $qrWmm = max(5, $nw * $wMm);
            $xMm = $nx * $wMm;
            $yMm = $ny * $hMm;

            $pdf->Image($tmpQr, $xMm, $yMm, $qrWmm, $qrWmm, 'PNG');

            $out = tempnam(sys_get_temp_dir(), 'inv_card_').'.pdf';
            $pdf->Output('F', $out);

            return $out;
        } finally {
            if (is_file($tmpQr)) {
                @unlink($tmpQr);
            }
        }
    }

    /**
     * @return array{0: float, 1: float, 2: float} nx, ny, nw in 0..1 (relative to first page)
     */
    private function normalizedLayout(CardDesign $design): array
    {
        $layout = $design->qr_layout;
        if (is_array($layout)
            && isset($layout['nx'], $layout['ny'], $layout['nw'])
            && is_numeric($layout['nx'])
            && is_numeric($layout['ny'])
            && is_numeric($layout['nw'])
        ) {
            return [
                max(0, min(1, (float) $layout['nx'])),
                max(0, min(1, (float) $layout['ny'])),
                max(0.02, min(1, (float) $layout['nw'])),
            ];
        }

        // Legacy fallback: positions were captured against an unknown box — approximate center
        return [0.72, 0.05, 0.18];
    }

    private function renderQrPng(string $data): string
    {
        if (! extension_loaded('gd')) {
            throw new RuntimeException('PHP GD extension is required to embed QR codes. Enable gd in php.ini.');
        }

        $tmp = tempnam(sys_get_temp_dir(), 'qr_').'.png';

        $options = new QROptions([
            'outputInterface' => QRGdImagePNG::class,
            'eccLevel' => EccLevel::M,
            'scale' => 6,
            'imageTransparent' => false,
        ]);

        (new QRCode($options))->render($data, $tmp);

        if (! is_readable($tmp) || filesize($tmp) < 32) {
            @unlink($tmp);
            throw new RuntimeException('Failed to generate QR code image.');
        }

        return $tmp;
    }
}
