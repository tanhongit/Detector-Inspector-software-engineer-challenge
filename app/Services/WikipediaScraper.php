<?php

namespace App\Services;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

/**
 * WikipediaScraper Service
 *
 * Responsible for fetching Wikipedia pages and extracting table data
 *
 * @package App\Services
 */
class WikipediaScraper
{
    private Client $httpClient;

    public function __construct()
    {
        $this->httpClient = new Client([
            'timeout' => 30,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (compatible; WikipediaTableScraper/1.0)',
            ]
        ]);
    }

    /**
     * Fetch the HTML content of a Wikipedia page
     *
     * @param string $url The URL of the Wikipedia page
     * @return string The HTML content
     * @throws \Exception If the page cannot be fetched
     */
    public function fetchPage(string $url): string
    {
        try {
            $response = $this->httpClient->get($url);
            return (string) $response->getBody();
        } catch (\Exception $e) {
            throw new \Exception("Failed to fetch Wikipedia page: " . $e->getMessage());
        }
    }

    /**
     * Extract all tables from HTML content
     *
     * @param string $html The HTML content to parse
     * @return array Array of table data (each table is a 2D array)
     */
    public function extractTables(string $html): array
    {
        $crawler = new Crawler($html);
        $tables = [];

        // Find all tables with class 'wikitable' (Wikipedia's standard table class)
        $crawler->filter('table.wikitable, table.sortable')->each(function (Crawler $table) use (&$tables) {
            $tableData = [];

            // Extract all rows
            $table->filter('tr')->each(function (Crawler $row) use (&$tableData) {
                $rowData = [];

                // Extract headers and cells
                $row->filter('th, td')->each(function (Crawler $cell) use (&$rowData) {
                    // Get text content and clean it up
                    $text = trim($cell->text());
                    // Remove reference markers like [1], [2], etc.
                    $text = preg_replace('/\[\d+\]/', '', $text);
                    $rowData[] = $text;
                });

                if (!empty($rowData)) {
                    $tableData[] = $rowData;
                }
            });

            if (!empty($tableData)) {
                $tables[] = $tableData;
            }
        });

        return $tables;
    }
}

