<?php

namespace Tests\Unit;

use App\Services\NumericColumnExtractor;
use PHPUnit\Framework\TestCase;

/**
 * Test suite for NumericColumnExtractor service
 *
 * Tests the ability to identify and extract numeric columns from tables
 */
class NumericColumnExtractorTest extends TestCase
{
    private NumericColumnExtractor $extractor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->extractor = new NumericColumnExtractor;
    }

    /**
     * Test that extractor can identify numeric columns
     */
    public function test_it_can_identify_numeric_columns(): void
    {
        $tableData = [
            ['Name', 'Age', 'City'],
            ['John', '25', 'New York'],
            ['Jane', '30', 'London'],
            ['Bob', '35', 'Paris'],
        ];

        $numericColumns = $this->extractor->identifyNumericColumns($tableData);

        $this->assertIsArray($numericColumns);
        $this->assertContains(1, $numericColumns); // Age column index
    }

    /**
     * Test that extractor can extract values from a numeric column
     */
    public function test_it_can_extract_numeric_values(): void
    {
        $tableData = [
            ['Name', 'Height'],
            ['Record 1', '1.83'],
            ['Record 2', '1.91'],
            ['Record 3', '2.09'],
        ];

        $values = $this->extractor->extractColumnValues($tableData, 1);

        $this->assertIsArray($values);
        $this->assertEquals([1.83, 1.91, 2.09], $values);
    }

    /**
     * Test that extractor handles non-numeric values
     */
    public function test_it_filters_non_numeric_values(): void
    {
        $tableData = [
            ['Name', 'Score'],
            ['Test 1', '100'],
            ['Test 2', 'N/A'],
            ['Test 3', '85.5'],
        ];

        $values = $this->extractor->extractColumnValues($tableData, 1);

        $this->assertIsArray($values);
        $this->assertCount(2, $values);
    }
}
