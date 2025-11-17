<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardDesign extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'user_id',
        'design_name',
        'design_type', // 'template' or 'pdf'
        'template_name', // for template designs
        'pdf_file_path', // for PDF designs
        'qr_position_x',
        'qr_position_y',
        'qr_size',
        'qr_color',
        'qr_background_color',
        'text_content', // JSON field for template text content
        'is_active',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'text_content' => 'array',
        'is_active' => 'boolean',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Get the design file path
    public function getDesignFilePath()
    {
        if ($this->design_type === 'pdf' && $this->pdf_file_path) {
            return storage_path('app/' . $this->pdf_file_path);
        }
        return null;
    }

    // Get the design file URL
    public function getDesignFileUrl()
    {
        if ($this->design_type === 'pdf' && $this->pdf_file_path) {
            return asset('storage/' . $this->pdf_file_path);
        }
        return null;
    }

    // Check if this is a template design
    public function isTemplate()
    {
        return $this->design_type === 'template';
    }

    // Check if this is a PDF design
    public function isPdf()
    {
        return $this->design_type === 'pdf';
    }

    // Get QR code position as array
    public function getQrPosition()
    {
        return [
            'x' => $this->qr_position_x,
            'y' => $this->qr_position_y
        ];
    }

    // Get QR code styling
    public function getQrStyling()
    {
        return [
            'size' => $this->qr_size,
            'color' => $this->qr_color,
            'background_color' => $this->qr_background_color
        ];
    }
}
