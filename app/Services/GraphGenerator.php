<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

/**
 * GraphGenerator Service
 *
 * Responsible for generating line graph images from numeric data
 *
 * @package App\Services
 */
class GraphGenerator
{
    private const WIDTH = 800;
    private const HEIGHT = 600;
    private const PADDING = 60;
    private const BACKGROUND_COLOR = '#ffffff';
    private const GRID_COLOR = '#e0e0e0';
    private const AXIS_COLOR = '#333333';
    private const LINE_COLOR = '#2563eb';
    private const POINT_COLOR = '#dc2626';

    /**
     * Generate a line graph from numeric data and save it as an image
     *
     * @param array $data Array of numeric values to plot
     * @param string $outputPath Path where the image should be saved
     * @param string $title Optional title for the graph
     * @return bool True if successful, false otherwise
     */
    public function generateGraph(array $data, string $outputPath, string $title = 'Numeric Data Visualization'): bool
    {
        // Validate input
        if (empty($data)) {
            return false;
        }

        // Validate output directory
        $directory = dirname($outputPath);
        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            return false;
        }

        try {
            $manager = new ImageManager(new Driver());
            $image = $manager->create(self::WIDTH, self::HEIGHT);

            // Fill background
            $image = $image->fill(self::BACKGROUND_COLOR);

            // Draw the graph
            $this->drawGrid($image);
            $this->drawAxes($image);
            $this->drawTitle($image, $title);
            $this->drawData($image, $data);
            $this->drawLabels($image, $data);

            // Save the image
            $image->save($outputPath);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Draw grid lines on the graph
     */
    private function drawGrid($image): void
    {
        $plotWidth = self::WIDTH - (2 * self::PADDING);
        $plotHeight = self::HEIGHT - (2 * self::PADDING);

        // Vertical grid lines
        for ($i = 0; $i <= 10; $i++) {
            $x = self::PADDING + ($plotWidth / 10) * $i;
            $image->drawLine(function ($line) use ($x, $plotHeight) {
                $line->from((int)$x, self::PADDING);
                $line->to((int)$x, self::PADDING + $plotHeight);
                $line->color(self::GRID_COLOR);
                $line->width(1);
            });
        }

        // Horizontal grid lines
        for ($i = 0; $i <= 10; $i++) {
            $y = self::PADDING + ($plotHeight / 10) * $i;
            $image->drawLine(function ($line) use ($y, $plotWidth) {
                $line->from(self::PADDING, (int)$y);
                $line->to(self::PADDING + $plotWidth, (int)$y);
                $line->color(self::GRID_COLOR);
                $line->width(1);
            });
        }
    }

    /**
     * Draw X and Y axes
     */
    private function drawAxes($image): void
    {
        $plotWidth = self::WIDTH - (2 * self::PADDING);
        $plotHeight = self::HEIGHT - (2 * self::PADDING);

        // Y-axis
        $image->drawLine(function ($line) use ($plotHeight) {
            $line->from(self::PADDING, self::PADDING);
            $line->to(self::PADDING, self::PADDING + $plotHeight);
            $line->color(self::AXIS_COLOR);
            $line->width(2);
        });

        // X-axis
        $image->drawLine(function ($line) use ($plotWidth, $plotHeight) {
            $line->from(self::PADDING, self::PADDING + $plotHeight);
            $line->to(self::PADDING + $plotWidth, self::PADDING + $plotHeight);
            $line->color(self::AXIS_COLOR);
            $line->width(2);
        });
    }

    /**
     * Draw the title of the graph
     */
    private function drawTitle($image, string $title): void
    {
        // Use built-in font instead of external font file
        $image->text($title, self::WIDTH / 2, 30, function ($font) {
            $font->size(20);
            $font->color(self::AXIS_COLOR);
            $font->align('center');
            $font->valign('top');
        });
    }

    /**
     * Draw the data points and lines
     */
    private function drawData($image, array $data): void
    {
        $plotWidth = self::WIDTH - (2 * self::PADDING);
        $plotHeight = self::HEIGHT - (2 * self::PADDING);

        $count = count($data);
        $min = min($data);
        $max = max($data);
        $range = $max - $min;

        if ($range == 0) {
            $range = 1; // Prevent division by zero
        }

        $points = [];

        // Calculate points
        for ($i = 0; $i < $count; $i++) {
            $x = self::PADDING + ($plotWidth / max(1, $count - 1)) * $i;
            $normalizedValue = ($data[$i] - $min) / $range;
            $y = self::PADDING + $plotHeight - ($normalizedValue * $plotHeight);
            $points[] = ['x' => $x, 'y' => $y];
        }

        // Draw lines between points
        for ($i = 0; $i < count($points) - 1; $i++) {
            $image->drawLine(function ($line) use ($points, $i) {
                $line->from((int)$points[$i]['x'], (int)$points[$i]['y']);
                $line->to((int)$points[$i + 1]['x'], (int)$points[$i + 1]['y']);
                $line->color(self::LINE_COLOR);
                $line->width(3);
            });
        }

        // Draw points
        foreach ($points as $point) {
            $image->drawCircle((int)$point['x'], (int)$point['y'], function ($circle) {
                $circle->radius(5);
                $circle->background(self::POINT_COLOR);
                $circle->border(self::BACKGROUND_COLOR, 2);
            });
        }
    }

    /**
     * Draw axis labels
     */
    private function drawLabels($image, array $data): void
    {
        $plotHeight = self::HEIGHT - (2 * self::PADDING);
        $min = min($data);
        $max = max($data);

        // Y-axis labels
        for ($i = 0; $i <= 5; $i++) {
            $value = $min + (($max - $min) / 5) * (5 - $i);
            $y = self::PADDING + ($plotHeight / 5) * $i;

            $image->text(number_format($value, 2), self::PADDING - 10, (int)$y, function ($font) {
                $font->size(12);
                $font->color(self::AXIS_COLOR);
                $font->align('right');
                $font->valign('middle');
            });
        }

        // X-axis labels
        $count = count($data);
        $labelCount = min(10, $count);
        $step = max(1, floor($count / $labelCount));

        for ($i = 0; $i < $count; $i += $step) {
            $x = self::PADDING + ((self::WIDTH - 2 * self::PADDING) / max(1, $count - 1)) * $i;
            $image->text((string)($i + 1), (int)$x, self::HEIGHT - self::PADDING + 20, function ($font) {
                $font->size(12);
                $font->color(self::AXIS_COLOR);
                $font->align('center');
                $font->valign('top');
            });
        }
    }
}
