<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Feature test for Wikipedia Graph Generator
 *
 * Tests the complete workflow from URL to graph generation
 */
class WikipediaGraphGeneratorTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    /**
     * Test the complete workflow with a real Wikipedia page
     *
     * This test demonstrates BDD - describing the behavior we expect
     */
    public function test_it_can_generate_graph_from_wikipedia_url(): void
    {
        // Given: A Wikipedia URL with table data
        $url = 'https://en.wikipedia.org/wiki/Women%27s_high_jump_world_record_progression';

        // When: We run the command to generate a graph
        $this->artisan('wikipedia:graph', [
            'url' => $url,
            '--output' => storage_path('app/test_output.png'),
        ])
            // Then: The command should succeed
            ->assertExitCode(0);

        // And: The output file should exist
        $this->assertFileExists(storage_path('app/test_output.png'));

        // Cleanup
        if (file_exists(storage_path('app/test_output.png'))) {
            unlink(storage_path('app/test_output.png'));
        }
    }
}
