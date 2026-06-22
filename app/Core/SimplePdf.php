<?php

class SimplePdf
{
    private float $width = 595.28;
    private float $height = 841.89;
    private array $pages = [];
    private int $pageIndex = -1;

    public function __construct()
    {
        $this->addPage();
    }

    public function addPage(): void
    {
        $this->pages[] = '';
        $this->pageIndex++;
    }

    public function text(float $x, float $y, string $text, int $size = 10, array $color = [0, 0, 0]): void
    {
        [$r, $g, $b] = $color;
        $text = $this->escape($this->normalizeText($text));
        $pdfY = $this->height - $y;
        $this->write(sprintf("%.3f %.3f %.3f rg BT /F1 %d Tf %.2f %.2f Td (%s) Tj ET\n", $r, $g, $b, $size, $x, $pdfY, $text));
    }

    public function line(float $x1, float $y1, float $x2, float $y2, array $color = [0, 0, 0], float $width = 1): void
    {
        [$r, $g, $b] = $color;
        $this->write(sprintf("%.3f %.3f %.3f RG %.2f w %.2f %.2f m %.2f %.2f l S\n", $r, $g, $b, $width, $x1, $this->height - $y1, $x2, $this->height - $y2));
    }

    public function rect(float $x, float $y, float $w, float $h, array $color = [0, 0, 0]): void
    {
        [$r, $g, $b] = $color;
        $pdfY = $this->height - $y - $h;
        $this->write(sprintf("%.3f %.3f %.3f rg %.2f %.2f %.2f %.2f re f\n", $r, $g, $b, $x, $pdfY, $w, $h));
    }

    public function output(): string
    {
        $objects = [];
        $objects[] = '<< /Type /Catalog /Pages 2 0 R >>';
        $objects[] = '';

        $pageIds = [];
        foreach ($this->pages as $content) {
            $contentId = count($objects) + 1;
            $objects[] = "<< /Length " . strlen($content) . " >>\nstream\n{$content}endstream";

            $pageId = count($objects) + 1;
            $pageIds[] = $pageId;
            $objects[] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 {$this->width} {$this->height}] /Resources << /Font << /F1 << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> >> >> /Contents {$contentId} 0 R >>";
        }

        $kids = implode(' ', array_map(fn($id) => "{$id} 0 R", $pageIds));
        $objects[1] = "<< /Type /Pages /Kids [{$kids}] /Count " . count($pageIds) . " >>";

        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $index => $object) {
            $objectId = $index + 1;
            $offsets[$objectId] = strlen($pdf);
            $pdf .= "{$objectId} 0 obj\n{$object}\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";

        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }

        $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\nstartxref\n{$xrefOffset}\n%%EOF";

        return $pdf;
    }

    private function write(string $command): void
    {
        $this->pages[$this->pageIndex] .= $command;
    }

    private function escape(string $text): string
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }

    private function normalizeText(string $text): string
    {
        $map = [
            'ğ' => 'g', 'Ğ' => 'G',
            'ü' => 'u', 'Ü' => 'U',
            'ş' => 's', 'Ş' => 'S',
            'ı' => 'i', 'İ' => 'I',
            'ö' => 'o', 'Ö' => 'O',
            'ç' => 'c', 'Ç' => 'C',
            '₺' => 'TL',
        ];

        return strtr($text, $map);
    }
}
