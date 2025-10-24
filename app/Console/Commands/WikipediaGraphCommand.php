<?php

namespace App\Console\Commands;

use App\Services\GraphGenerator;
use App\Services\NumericColumnExtractor;
use App\Services\WikipediaScraper;
use Illuminate\Console\Command;

/**
 * Wikipedia Graph Generator Command
 *
 * Main entry point for the application that orchestrates the entire workflow:
 * 1. Fetch Wikipedia page
 * 2. Extract tables
 * 3. Find numeric columns
 * 4. Generate graph
 *
 * Usage: php artisan wikipedia:graph <url> [--output=path]
 */
class WikipediaGraphCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'wikipedia:graph 
                            {url : The Wikipedia page URL to process}
                            {--output= : Output path for the graph image (default: storage/app/graph.png)}
                            {--column= : Specific column index to plot (optional)}';

    /**
     * The console command description.
     */
    protected $description = 'Generate a graph from numeric data in Wikipedia tables';

    private WikipediaScraper $scraper;

    private NumericColumnExtractor $extractor;

    private GraphGenerator $generator;

    /**
     * Create a new command instance
     */
    public function __construct(
        WikipediaScraper $scraper,
        NumericColumnExtractor $extractor,
        GraphGenerator $generator
    ) {
        parent::__construct();
        $this->scraper = $scraper;
        $this->extractor = $extractor;
        $this->generator = $generator;
    }

    /**
     * Execute the console command
     */
    public function handle(): int
    {
        $url = $this->argument('url');
        if (! is_string($url)) {
            $this->error('URL argument must be a string');
            return self::FAILURE;
        }

        $outputPath = $this->option('output');
        $outputPath = is_string($outputPath) ? $outputPath : storage_path('app/graph.png');
        
        $specificColumn = $this->option('column');

        $this->info("Fetching Wikipedia page: {$url}");

        try {
            // Step 1: Fetch the page
            $html = $this->scraper->fetchPage($url);
            $this->info('✓ Page fetched successfully');

            // Step 2: Extract tables
            $tables = $this->scraper->extractTables($html);

            if (empty($tables)) {
                $this->error('No tables found on the page');

                return self::FAILURE;
            }

            $this->info('✓ Found '.count($tables).' table(s)');

            // Step 3: Process each table to find numeric columns
            $graphGenerated = false;

            foreach ($tables as $index => $table) {
                $this->info('Processing table '.($index + 1).'...');

                // Find numeric columns
                $numericColumns = $this->extractor->identifyNumericColumns($table);

                if (empty($numericColumns)) {
                    $this->warn('  No numeric columns found in table '.($index + 1));

                    continue;
                }

                $this->info('  Found '.count($numericColumns).' numeric column(s)');

                // Use specific column or first numeric column
                $columnToPlot = $specificColumn !== null
                    ? (int) $specificColumn
                    : $numericColumns[0];

                if (! in_array($columnToPlot, $numericColumns)) {
                    $this->warn("  Column {$columnToPlot} is not numeric, using column {$numericColumns[0]}");
                    $columnToPlot = $numericColumns[0];
                }

                // Extract values
                $values = $this->extractor->extractColumnValues($table, $columnToPlot);

                if (empty($values)) {
                    $this->warn("  No numeric values found in column {$columnToPlot}");

                    continue;
                }

                $this->info('  Extracted '.count($values).' data points');

                // Show some statistics
                $this->info('  Min: '.number_format(min($values), 2));
                $this->info('  Max: '.number_format(max($values), 2));
                $this->info('  Average: '.number_format(array_sum($values) / count($values), 2));

                // Step 4: Generate graph
                $this->info('Generating graph...');

                $title = isset($table[0][$columnToPlot])
                    ? $table[0][$columnToPlot]
                    : 'Numeric Data Visualization';

                $success = $this->generator->generateGraph($values, $outputPath, $title);

                if ($success) {
                    $this->info('✓ Graph generated successfully!');
                    $this->info("Output saved to: {$outputPath}");
                    $graphGenerated = true;

                    break; // Use first table with numeric data
                } else {
                    $this->error('Failed to generate graph');
                }
            }

            if (! $graphGenerated) {
                $this->error('Could not generate graph from any table');

                return self::FAILURE;
            }

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Error: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
