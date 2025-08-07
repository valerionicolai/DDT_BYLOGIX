<?php

namespace App\Services;

use Picqer\Barcode\BarcodeGeneratorSVG;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Illuminate\Support\Facades\Log;
use Exception;

class BarcodeService
{
    private BarcodeGeneratorSVG $svgGenerator;
    private BarcodeGeneratorPNG $pngGenerator;

    public function __construct()
    {
        $this->svgGenerator = new BarcodeGeneratorSVG();
        $this->pngGenerator = new BarcodeGeneratorPNG();
    }

    /**
     * Genera un barcode EAN13 per un materiale
     */
    public function generateMaterialBarcode(int $materialId): string
    {
        // Genera un codice EAN13 basato sull'ID del materiale
        // Formato: 200 (prefisso materiali) + 9 cifre (ID padded) + check digit
        $prefix = '200';
        $paddedId = str_pad($materialId, 9, '0', STR_PAD_LEFT);
        $baseCode = $prefix . $paddedId;
        
        // Calcola check digit EAN13
        $checkDigit = $this->calculateEAN13CheckDigit($baseCode);
        
        return $baseCode . $checkDigit;
    }

    /**
     * Genera un barcode EAN13 per un documento
     */
    public function generateDocumentBarcode(int $documentId): string
    {
        // Genera un codice EAN13 basato sull'ID del documento
        // Formato: 100 (prefisso documenti) + 9 cifre (ID padded) + check digit
        $prefix = '100';
        $paddedId = str_pad($documentId, 9, '0', STR_PAD_LEFT);
        $baseCode = $prefix . $paddedId;
        
        // Calcola check digit EAN13
        $checkDigit = $this->calculateEAN13CheckDigit($baseCode);
        
        return $baseCode . $checkDigit;
    }

    /**
     * Genera SVG del barcode
     */
    public function generateBarcodeSVG(string $code, int $widthFactor = 2, int $height = 30): string
    {
        try {
            return $this->svgGenerator->getBarcode($code, BarcodeGeneratorSVG::TYPE_EAN_13, $widthFactor, $height);
        } catch (Exception $e) {
            Log::error('Errore generazione barcode SVG: ' . $e->getMessage());
            throw new Exception('Impossibile generare il barcode SVG');
        }
    }

    /**
     * Genera PNG del barcode (per export/stampa)
     */
    public function generateBarcodePNG(string $code, int $widthFactor = 2, int $height = 30): string
    {
        try {
            return base64_encode($this->pngGenerator->getBarcode($code, BarcodeGeneratorPNG::TYPE_EAN_13, $widthFactor, $height));
        } catch (Exception $e) {
            Log::error('Errore generazione barcode PNG: ' . $e->getMessage());
            throw new Exception('Impossibile generare il barcode PNG');
        }
    }

    /**
     * Decodifica un barcode per identificare tipo e ID
     */
    public function decodeBarcode(string $barcode): array
    {
        if (strlen($barcode) !== 13) {
            throw new Exception('Barcode non valido: deve essere di 13 cifre');
        }

        $prefix = substr($barcode, 0, 3);
        $id = (int) ltrim(substr($barcode, 3, 9), '0');

        switch ($prefix) {
            case '100':
                return ['type' => 'document', 'id' => $id];
            case '200':
                return ['type' => 'material', 'id' => $id];
            default:
                throw new Exception('Prefisso barcode non riconosciuto');
        }
    }

    /**
     * Verifica se un barcode è valido (check digit)
     */
    public function validateBarcode(string $barcode): bool
    {
        if (strlen($barcode) !== 13) {
            return false;
        }

        $baseCode = substr($barcode, 0, 12);
        $checkDigit = substr($barcode, 12, 1);
        
        return $this->calculateEAN13CheckDigit($baseCode) === $checkDigit;
    }

    /**
     * Calcola il check digit per EAN13
     */
    private function calculateEAN13CheckDigit(string $code): string
    {
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $digit = (int) $code[$i];
            $sum += ($i % 2 === 0) ? $digit : $digit * 3;
        }
        
        $checkDigit = (10 - ($sum % 10)) % 10;
        return (string) $checkDigit;
    }

    /**
     * Genera barcode per batch di materiali
     */
    public function generateBatchMaterialBarcodes(array $materialIds): array
    {
        $barcodes = [];
        foreach ($materialIds as $id) {
            $barcodes[$id] = $this->generateMaterialBarcode($id);
        }
        return $barcodes;
    }

    /**
     * Genera barcode per batch di documenti
     */
    public function generateBatchDocumentBarcodes(array $documentIds): array
    {
        $barcodes = [];
        foreach ($documentIds as $id) {
            $barcodes[$id] = $this->generateDocumentBarcode($id);
        }
        return $barcodes;
    }

    /**
     * Ottieni statistiche sui barcode
     */
    public function getBarcodeStats(): array
    {
        return [
            'total_materials' => \App\Models\Material::count(),
            'total_documents' => \App\Models\Document::count(),
            'materials_with_barcode' => \App\Models\Material::whereNotNull('barcode')->count(),
            'documents_with_barcode' => \App\Models\Document::whereNotNull('barcode')->count(),
        ];
    }
}