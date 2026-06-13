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
     * Build invitation card as PNG from the uploaded PDF template image + invitee QR.
     *
     * @return string Absolute path to a temp .png file (caller should delete after send)
     */
    public function composeImage(CardDesign $design, Invitee $invitee, int $dpi = 150): string
    {
        if ($design->template_image_path) {
            return $this->composeImageFromTemplate($design, $invitee);
        }

        // Legacy designs saved before template PNG export: rasterize composed PDF.
        $invitee->loadMissing('event');
        $pdfPath = $this->compose($design, $invitee);

        try {
            return $this->pdfFirstPageToPng($pdfPath, $dpi, $invitee);
        } finally {
            if (is_file($pdfPath)) {
                @unlink($pdfPath);
            }
        }
    }

    /**
     * Composite invitee QR onto the PNG exported from the uploaded PDF (browser PDF.js).
     */
    private function composeImageFromTemplate(CardDesign $design, Invitee $invitee): string
    {
        if (! extension_loaded('gd')) {
            throw new RuntimeException('PHP GD extension is required to build invitation images.');
        }

        $sourcePath = Storage::disk('public')->path($design->template_image_path);
        if (! is_readable($sourcePath)) {
            throw new RuntimeException(
                'Template image is missing. Open Design Card, re-upload your PDF, and save again.'
            );
        }

        $base = $this->loadImage($sourcePath);
        $w = imagesx($base);
        $h = imagesy($base);

        [$nx, $ny, $nw] = $this->normalizedLayout($design);
        $qrW = max(32, (int) round($nw * $w));
        $x = (int) round($nx * $w);
        $y = (int) round($ny * $h);

        $qrUrl = route('invitee.show', ['code' => $invitee->invitation_code], true);
        $tmpQr = $this->renderQrPng($qrUrl);
        $qr = imagecreatefrompng($tmpQr);
        $qrSrcW = imagesx($qr);

        $dest = imagecreatetruecolor($w, $h);
        imagealphablending($dest, false);
        imagesavealpha($dest, true);
        $transparent = imagecolorallocatealpha($dest, 0, 0, 0, 127);
        imagefilledrectangle($dest, 0, 0, $w, $h, $transparent);
        imagealphablending($dest, true);

        imagecopy($dest, $base, 0, 0, 0, 0, $w, $h);
        imagecopyresampled($dest, $qr, $x, $y, 0, 0, $qrW, $qrW, $qrSrcW, $qrSrcW);

        $out = tempnam(sys_get_temp_dir(), 'inv_img_').'.png';
        imagepng($dest, $out, 6);

        imagedestroy($base);
        imagedestroy($qr);
        imagedestroy($dest);
        @unlink($tmpQr);

        if (! is_readable($out) || filesize($out) < 32) {
            @unlink($out);
            throw new RuntimeException('Failed to generate invitation image.');
        }

        return $out;
    }

    /**
     * @return \GdImage|resource
     */
    private function loadImage(string $path)
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        $image = match ($ext) {
            'png' => imagecreatefrompng($path),
            'jpg', 'jpeg' => imagecreatefromjpeg($path),
            'webp' => function_exists('imagecreatefromwebp') ? imagecreatefromwebp($path) : false,
            default => false,
        };

        if ($image === false) {
            throw new RuntimeException('Could not read template image (expected PNG).');
        }

        return $image;
    }

    private function pdfFirstPageToPng(string $pdfPath, int $dpi, Invitee $invitee): string
    {
        $out = tempnam(sys_get_temp_dir(), 'inv_img_').'.png';

        if (extension_loaded('imagick')) {
            $im = new \Imagick();
            $im->setResolution($dpi, $dpi);
            $im->readImage($pdfPath.'[0]');
            $im->setImageFormat('png');
            $im->writeImage($out);
            $im->clear();
            $im->destroy();

            if (is_readable($out) && filesize($out) > 32) {
                return $out;
            }
            @unlink($out);
        }

        $gsCommands = [
            sprintf(
                'gswin64c -dSAFER -dBATCH -dNOPAUSE -sDEVICE=png16m -r%d -dFirstPage=1 -dLastPage=1 -sOutputFile=%s %s',
                $dpi,
                escapeshellarg($out),
                escapeshellarg($pdfPath)
            ),
            sprintf(
                'gs -dSAFER -dBATCH -dNOPAUSE -sDEVICE=png16m -r%d -dFirstPage=1 -dLastPage=1 -sOutputFile=%s %s',
                $dpi,
                escapeshellarg($out),
                escapeshellarg($pdfPath)
            ),
        ];

        foreach ($gsCommands as $cmd) {
            @exec($cmd.' 2>&1', $output, $code);

            if ($code === 0 && is_readable($out) && filesize($out) > 32) {
                return $out;
            }
        }

        @unlink($out);

        throw new RuntimeException(
            'No template image for this design. Open Design Card, upload your PDF again, and save — the PDF is converted to PNG automatically.'
        );
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
            'scale' => 8,
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
