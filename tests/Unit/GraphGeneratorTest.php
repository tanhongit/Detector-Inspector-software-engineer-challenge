<?php

namespace Tests\Unit;

use App\Services\GraphGenerator;
use PHPUnit\Framework\TestCase;

/**
 * Test suite for GraphGenerator service
 * 
 * Tests the ability to generate graph images from numeric data
 */
class GraphGeneratorTest extends TestCase
{
    private GraphGenerator $generator;
    private string $tempDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->generator = new GraphGenerator();
        $this->tempDir = sys_get_temp_dir() . '/graph_tests';
        if (!is_dir($this->tempDir)) {
            mkdir($this->tempDir, 0755, true);
        }
    }

    protected function tearDown(): void
    {
        // Cleanup temp files
        if (is_dir($this->tempDir)) {
            $files = glob($this->tempDir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            rmdir($this->tempDir);
        }
        parent::tearDown();
    }

    /**
     * Test that generator can create a graph image
     */
    public function test_it_can_generate_graph(): void
    {
        $data = [1.5, 2.0, 2.5, 3.0, 3.5];
        $outputPath = $this->tempDir . '/test_graph.png';

        $result = $this->generator->generateGraph($data, $outputPath);
        
        $this->assertTrue($result);
        $this->assertFileExists($outputPath);
    }

    /**
     * Test that generator handles empty data
     */
    public function test_it_handles_empty_data(): void
    {
        $data = [];
        $outputPath = $this->tempDir . '/empty_graph.png';

        $result = $this->generator->generateGraph($data, $outputPath);
        
        $this->assertFalse($result);
    }

    /**
     * Test that generator validates output path
     */
    public function test_it_validates_output_path(): void
    {
        $data = [1, 2, 3];
        $outputPath = '/invalid/path/that/does/not/exist/graph.png';

        $result = $this->generator->generateGraph($data, $outputPath);
        
        $this->assertFalse($result);
    }
}
