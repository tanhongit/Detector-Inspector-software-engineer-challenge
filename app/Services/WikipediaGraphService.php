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

    public function generateGraph(string $url): array
    {
        $tables = $this->scraper->fetchTables($url);

        if (empty($tables)) {
            throw new \Exception('No tables found on the Wikipedia page.');
        }

        $firstTable = $tables[0];
        $numericColumns = $this->extractor->extract($firstTable);

        if (empty($numericColumns)) {
            throw new \Exception('No numeric columns found in the first table.');
        }

        $outputPath = 'graphs/'.uniqid('graph_', true).'.png';
        $fullPath = storage_path('app/public/'.$outputPath);

        $directory = dirname($fullPath);
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $this->generator->generate($firstTable, $numericColumns, $fullPath);

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
