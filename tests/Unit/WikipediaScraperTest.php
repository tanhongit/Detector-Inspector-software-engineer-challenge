<?php

namespace Tests\Unit;

use App\Services\WikipediaScraper;
use PHPUnit\Framework\TestCase;

/**
 * Test suite for WikipediaScraper service
 *
 * Tests the ability to fetch and parse Wikipedia pages
 */
class WikipediaScraperTest extends TestCase
{
    private WikipediaScraper $scraper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->scraper = new WikipediaScraper();
    }

    /**
     * Test that scraper can fetch a Wikipedia page
     */
    public function test_it_can_fetch_wikipedia_page(): void
    {
        $url = 'https://en.wikipedia.org/wiki/Women%27s_high_jump_world_record_progression';
        $html = $this->scraper->fetchPage($url);

        $this->assertNotEmpty($html);
        $this->assertStringContainsString('wikipedia', strtolower($html));
    }

    /**
     * Test that scraper can extract tables from HTML
     */
    public function test_it_can_extract_tables(): void
    {
        $html = '<html><body><table class="wikitable"><tr><th>Header</th></tr><tr><td>Data</td></tr></table></body></html>';
        $tables = $this->scraper->extractTables($html);

        $this->assertIsArray($tables);
        $this->assertNotEmpty($tables);
    }

    /**
     * Test that scraper returns empty array when no tables found
     */
    public function test_it_returns_empty_array_when_no_tables(): void
    {
        $html = '<html><body><p>No tables here</p></body></html>';
        $tables = $this->scraper->extractTables($html);

        $this->assertIsArray($tables);
        $this->assertEmpty($tables);
    }
}

