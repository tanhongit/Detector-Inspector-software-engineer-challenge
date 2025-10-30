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
     *
     * @throws \Exception
     */
    public function generateGraph(string $url): array
    {
        $html = $this->scraper->fetchPage($url);
        $tables = $this->scraper->extractTables($html);

        if (empty($tables)) {
            throw new \Exception('No tables found on the Wikipedia page.');
        }

        $selectedTable = null;
        $numericColumns = [];

        foreach ($tables as $table) {
            $columns = $this->extractor->identifyNumericColumns($table);
            if (! empty($columns)) {
                $selectedTable = $table;
                $numericColumns = $columns;

                break;
            }
        }

        if ($selectedTable === null || empty($numericColumns)) {
            throw new \Exception('No tables with numeric columns found on the Wikipedia page.');
        }

        $outputPath = 'graphs/'.uniqid('graph_', true).'.png';
        $fullPath = storage_path('app/public/'.$outputPath);

        $directory = dirname($fullPath);
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $columnIndex = $numericColumns[0];
        $values = $this->extractor->extractColumnValues($selectedTable, $columnIndex);
        $columnName = $selectedTable[0][$columnIndex] ?? 'Numeric Column';
        $title = 'Wikipedia Data: '.$columnName;

        // Use column name as Y-axis label and "Record #" as X-axis label
        $this->generator->generateGraph(
            $values,
            $fullPath,
            $title,
            'Record Number',
            $columnName
        );

        return [
            'path' => $outputPath,
            'url' => Storage::url($outputPath),
            'full_path' => $fullPath,
            'columns' => $numericColumns,
            'table_info' => [
                'rows' => count($selectedTable),
                'columns' => count($selectedTable[0] ?? []),
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
