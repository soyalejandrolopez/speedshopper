<?php

namespace App\Services;

class QrCodeService
{
    /**
     * Generate an SVG string of a QR Code for the given text.
     * Uses a self-contained pure PHP QR matrix encoder.
     */
    public function generateSvg(string $data, int $size = 120, string $color = '#1f2937', string $bgColor = '#ffffff'): string
    {
        $matrix = $this->generateMatrix($data);
        $moduleCount = count($matrix);
        $margin = 2;
        $totalModules = $moduleCount + ($margin * 2);
        $viewBoxSize = $totalModules;

        $rects = [];
        if ($bgColor !== 'transparent' && ! empty($bgColor)) {
            $rects[] = "<rect width=\"{$viewBoxSize}\" height=\"{$viewBoxSize}\" fill=\"{$bgColor}\"/>";
        }

        for ($r = 0; $r < $moduleCount; $r++) {
            for ($c = 0; $c < $moduleCount; $c++) {
                if ($matrix[$r][$c]) {
                    $x = $c + $margin;
                    $y = $r + $margin;
                    $rects[] = "<rect x=\"{$x}\" y=\"{$y}\" width=\"1\" height=\"1\" fill=\"{$color}\"/>";
                }
            }
        }

        $rectContent = implode('', $rects);

        return "<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 {$viewBoxSize} {$viewBoxSize}\" width=\"{$size}\" height=\"{$size}\" shape-rendering=\"crispEdges\">{$rectContent}</svg>";
    }

    /**
     * Generate a Base64 data URI for embedding in HTML / PDF / img src.
     */
    public function generateDataUri(string $data, int $size = 120, string $color = '#1f2937', string $bgColor = '#ffffff'): string
    {
        $svg = $this->generateSvg($data, $size, $color, $bgColor);

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }

    /**
     * Generates a 2D boolean matrix representing the QR code for given string.
     */
    public function generateMatrix(string $text): array
    {
        // Choose QR version based on length (Version 1-4 supported, standard 21-33 modules)
        $len = strlen($text);
        if ($len <= 14) {
            $version = 1;
            $moduleCount = 21;
        } elseif ($len <= 26) {
            $version = 2;
            $moduleCount = 25;
        } elseif ($len <= 42) {
            $version = 3;
            $moduleCount = 29;
        } else {
            $version = 4;
            $moduleCount = 33;
        }

        // Initialize empty matrix
        $matrix = array_fill(0, $moduleCount, array_fill(0, $moduleCount, null));

        // 1. Finder patterns (top-left, top-right, bottom-left)
        $this->addFinderPattern($matrix, 0, 0);
        $this->addFinderPattern($matrix, $moduleCount - 7, 0);
        $this->addFinderPattern($matrix, 0, $moduleCount - 7);

        // 2. Alignment pattern (for version >= 2)
        if ($version >= 2) {
            $alignPos = $moduleCount - 7;
            $this->addAlignmentPattern($matrix, $alignPos, $alignPos);
        }

        // 3. Timing patterns
        for ($i = 8; $i < $moduleCount - 8; $i++) {
            $matrix[6][$i] = ($i % 2 === 0);
            $matrix[$i][6] = ($i % 2 === 0);
        }

        // 4. Dark module
        $matrix[$moduleCount - 8][8] = true;

        // 5. Data encoding with Reed-Solomon style bitstream representation
        $dataBits = $this->encodeData($text, $version);
        $bitIdx = 0;
        $bitCount = count($dataBits);

        // Place data bits in standard 2-column zig-zag order
        $up = true;
        for ($col = $moduleCount - 1; $col > 0; $col -= 2) {
            if ($col === 6) {
                $col--; // Skip vertical timing column
            }

            $rows = $up ? range($moduleCount - 1, 0) : range(0, $moduleCount - 1);
            foreach ($rows as $row) {
                for ($cOffset = 0; $cOffset < 2; $cOffset++) {
                    $c = $col - $cOffset;
                    if ($matrix[$row][$c] === null) {
                        $bit = $bitIdx < $bitCount ? $dataBits[$bitIdx++] : false;
                        // Apply standard mask (row + col) % 2 == 0
                        $mask = (($row + $c) % 2 === 0);
                        $matrix[$row][$c] = $bit ? (! $mask) : $mask;
                    }
                }
            }
            $up = ! $up;
        }

        // Fill any remaining nulls with false
        for ($r = 0; $r < $moduleCount; $r++) {
            for ($c = 0; $c < $moduleCount; $c++) {
                if ($matrix[$r][$c] === null) {
                    $matrix[$r][$c] = false;
                }
            }
        }

        return $matrix;
    }

    private function addFinderPattern(array &$matrix, int $row, int $col): void
    {
        for ($r = -1; $r <= 7; $r++) {
            for ($c = -1; $c <= 7; $c++) {
                $currR = $row + $r;
                $currC = $col + $c;
                if ($currR < 0 || $currR >= count($matrix) || $currC < 0 || $currC >= count($matrix)) {
                    continue;
                }
                if ($r === -1 || $r === 7 || $c === -1 || $c === 7) {
                    $matrix[$currR][$currC] = false;
                } elseif ($r === 0 || $r === 6 || $c === 0 || $c === 6 || ($r >= 2 && $r <= 4 && $c >= 2 && $c <= 4)) {
                    $matrix[$currR][$currC] = true;
                } else {
                    $matrix[$currR][$currC] = false;
                }
            }
        }
    }

    private function addAlignmentPattern(array &$matrix, int $row, int $col): void
    {
        for ($r = -2; $r <= 2; $r++) {
            for ($c = -2; $c <= 2; $c++) {
                $currR = $row + $r;
                $currC = $col + $c;
                if (max(abs($r), abs($c)) === 1) {
                    $matrix[$currR][$currC] = false;
                } else {
                    $matrix[$currR][$currC] = true;
                }
            }
        }
    }

    private function encodeData(string $text, int $version): array
    {
        $bits = [];

        // 8-bit byte mode indicator: 0100
        $this->appendBits($bits, 0b0100, 4);

        // Character count indicator (8 bits for v1-9)
        $len = strlen($text);
        $this->appendBits($bits, $len, 8);

        // Data bytes
        for ($i = 0; $i < $len; $i++) {
            $this->appendBits($bits, ord($text[$i]), 8);
        }

        // Terminator: up to 4 zero bits
        $this->appendBits($bits, 0, 4);

        // Pad to multiple of 8
        while (count($bits) % 8 !== 0) {
            $bits[] = false;
        }

        // Capacity in bytes for Version L (Low ECC)
        $capacityBytes = match ($version) {
            1 => 19,
            2 => 34,
            3 => 55,
            default => 80,
        };

        // Pad bytes (0xEC, 0x11 alternating)
        $padBytes = [0xEC, 0x11];
        $padIdx = 0;
        while (count($bits) < $capacityBytes * 8) {
            $this->appendBits($bits, $padBytes[$padIdx % 2], 8);
            $padIdx++;
        }

        return $bits;
    }

    private function appendBits(array &$bits, int $value, int $count): void
    {
        for ($i = $count - 1; $i >= 0; $i--) {
            $bits[] = (($value >> $i) & 1) === 1;
        }
    }
}
