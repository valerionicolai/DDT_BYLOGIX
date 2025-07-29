<?php

namespace App\Services;

use App\Models\Barcode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Picqer\Barcode\BarcodeGeneratorSVG;
use Picqer\Barcode\BarcodeGeneratorJPG;
use Picqer\Barcode\BarcodeGeneratorHTML;
use Picqer\Barcode\Types\TypeCode128;
use Picqer\Barcode\Types\TypeCode39;
use Picqer\Barcode\Types\TypeEan13;
use Picqer\Barcode\Types\TypeEan8;

class BarcodeService
{
    /**
     * Supported barcode types mapping.
     */
    private const TYPE_MAPPING = [
        'CODE128' => TypeCode128::class,
        'CODE39' => TypeCode39::class,
        'EAN13' => TypeEan13::class,
        'EAN8' => TypeEan8::class,
    ];

    /**
     * Generate a barcode for a given model.
     */
    public function generateForModel(
        Model $model,
        string $type = 'CODE128',
        string $format = 'png',
        array $options = []
    ): Barcode {
        // Create barcode record
        $barcode = Barcode::createForModel($model, $type, $format, $options);
        
        // Generate and save barcode image
        $this->generateBarcodeImage($barcode, $options);
        
        return $barcode;
    }

    /**
     * Generate barcode image and save to storage.
     */
    public function generateBarcodeImage(Barcode $barcode, array $options = []): string
    {
        $generator = $this->getGenerator($barcode->format);
        $barcodeType = $this->getBarcodeType($barcode->type);
        
        // Default options
        $width = $options['width'] ?? 2;
        $height = $options['height'] ?? 30;
        $color = $options['color'] ?? [0, 0, 0]; // Black
        
        // Generate barcode
        $barcodeData = $generator->getBarcode($barcode->code, $barcodeType, $width, $height, $color);
        
        // Save to storage
        $path = $barcode->getImagePath();
        Storage::disk('public')->put($path, $barcodeData);
        
        return $path;
    }

    /**
     * Get barcode generator based on format.
     */
    private function getGenerator(string $format)
    {
        return match($format) {
            'png' => new BarcodeGeneratorPNG(),
            'svg' => new BarcodeGeneratorSVG(),
            'jpg' => new BarcodeGeneratorJPG(),
            'html' => new BarcodeGeneratorHTML(),
            default => new BarcodeGeneratorPNG(),
        };
    }

    /**
     * Get barcode type class.
     */
    private function getBarcodeType(string $type)
    {
        $typeClass = self::TYPE_MAPPING[$type] ?? TypeCode128::class;
        return new $typeClass();
    }

    /**
     * Regenerate barcode image.
     */
    public function regenerateImage(Barcode $barcode, array $options = []): string
    {
        // Delete old image if exists
        $oldPath = $barcode->getImagePath();
        if (Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }
        
        // Generate new image
        return $this->generateBarcodeImage($barcode, $options);
    }

    /**
     * Get barcode image URL.
     */
    public function getBarcodeImageUrl(Barcode $barcode): string
    {
        $path = $barcode->getImagePath();
        
        if (Storage::disk('public')->exists($path)) {
            return asset('storage/' . $path);
        }
        
        // Generate image if it doesn't exist
        $this->generateBarcodeImage($barcode);
        return asset('storage/' . $path);
    }

    /**
     * Validate barcode data.
     */
    public function validateBarcodeData(string $data, string $type): bool
    {
        try {
            $generator = new BarcodeGeneratorPNG();
            $barcodeType = $this->getBarcodeType($type);
            $generator->getBarcode($data, $barcodeType);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get barcode as base64 string.
     */
    public function getBarcodeAsBase64(Barcode $barcode, array $options = []): string
    {
        $generator = $this->getGenerator($barcode->format);
        $barcodeType = $this->getBarcodeType($barcode->type);
        
        $width = $options['width'] ?? 2;
        $height = $options['height'] ?? 30;
        $color = $options['color'] ?? [0, 0, 0];
        
        $barcodeData = $generator->getBarcode($barcode->code, $barcodeType, $width, $height, $color);
        
        return base64_encode($barcodeData);
    }

    /**
     * Delete barcode and its image.
     */
    public function deleteBarcode(Barcode $barcode): bool
    {
        // Delete image file
        $path = $barcode->getImagePath();
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
        
        // Delete barcode record
        return $barcode->delete();
    }

    /**
     * Deactivate all barcodes for a model and create a new active one.
     */
    public function replaceBarcode(Model $model, string $type = 'CODE128', string $format = 'png', array $options = []): Barcode
    {
        // Deactivate existing barcodes
        $model->barcodes()->update(['is_active' => false]);
        
        // Create new active barcode
        return $this->generateForModel($model, $type, $format, $options);
    }
}