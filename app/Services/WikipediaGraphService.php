<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class WikipediaGraphService
{
    public function __construct(
        public WikipediaScraper $scraper,
        public NumericColumnExtractor $extractor,
        public GraphGenerator $generator
    ) {}

    /**
     * @return array{path: string, url: string, full_path: string, columns: array<int, int>, table_info: array{rows: int, columns: int}}
     */
    public function generateGraph(string $url): array
    {
        $html = $this->scraper->fetchPage($url);
        $tables = $this->scraper->extractTables($html);

        if (empty($tables)) {
            throw new \Exception('No tables found on the Wikipedia page.');
        }

        $firstTable = $tables[0];
        $numericColumns = $this->extractor->identifyNumericColumns($firstTable);

        if (empty($numericColumns)) {
            throw new \Exception('No numeric columns found in the first table.');
        }

        $outputPath = 'graphs/'.uniqid('graph_', true).'.png';
        $fullPath = storage_path('app/public/'.$outputPath);

        $directory = dirname($fullPath);
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $columnIndex = array_key_first($numericColumns);
        if ($columnIndex === null) {
            throw new \Exception('No numeric columns available.');
        }
        
        $values = $this->extractor->extractColumnValues($firstTable, $columnIndex);
        $title = $firstTable[0][$columnIndex] ?? 'Graph';
        
        $this->generator->generateGraph($values, $fullPath, $title);

        return [
            'path' => $outputPath,
            'url' => Storage::url($outputPath),
            'full_path' => $fullPath,
            'columns' => $numericColumns,
            'table_info' => [
                'rows' => count($firstTable),
                'columns' => count($firstTable[0] ?? []),
            ],
        ];
    }

    /**
     * @return array<int, array{path: string, url: string, created_at: int}>
     */
    public function getAllGraphs(): array
    {
        $files = Storage::disk('public')->files('graphs');

        return array_map(function ($file) {
            return [
                'path' => $file,
                'url' => Storage::url($file),
                'created_at' => Storage::disk('public')->lastModified($file),
            ];
        }, $files);
    }
}
