<?php

namespace App\Services;

/**
 * NumericColumnExtractor Service
 *
 * Responsible for identifying numeric columns in table data
 * and extracting numeric values
 */
class NumericColumnExtractor
{
    /**
     * Minimum percentage of numeric values required to consider a column numeric
     */
    private const NUMERIC_THRESHOLD = 0.5; // 50%

    /**
     * Identify which columns in the table contain primarily numeric data
     *
     * @param  array<int, array<int, string>>  $tableData  2D array representing table data
     * @return array<int, int> Array of column indices that contain numeric data
     */
    public function identifyNumericColumns(array $tableData): array
    {
        if (empty($tableData) || count($tableData) < 2) {
            return [];
        }

        $numericColumns = [];
        $columnCount = count($tableData[0]);

        // Skip header row and analyze data rows
        $dataRows = array_slice($tableData, 1);

        for ($col = 0; $col < $columnCount; $col++) {
            $numericCount = 0;
            $totalCount = 0;

            foreach ($dataRows as $row) {
                if (isset($row[$col])) {
                    $value = $row[$col];
                    $totalCount++;

                    if ($this->isNumeric($value)) {
                        $numericCount++;
                    }
                }
            }

            // If more than threshold of values are numeric, consider it a numeric column
            if ($totalCount > 0 && ($numericCount / $totalCount) >= self::NUMERIC_THRESHOLD) {
                $numericColumns[] = $col;
            }
        }

        return $numericColumns;
    }

    /**
     * Extract numeric values from a specific column
     *
     * @param  array<int, array<int, string>>  $tableData  2D array representing table data
     * @param  int  $columnIndex  The index of the column to extract
     * @return array<int, float> Array of numeric values
     */
    public function extractColumnValues(array $tableData, int $columnIndex): array
    {
        $values = [];

        // Skip header row
        $dataRows = array_slice($tableData, 1);

        foreach ($dataRows as $row) {
            if (isset($row[$columnIndex])) {
                $value = $this->parseNumericValue($row[$columnIndex]);
                if ($value !== null) {
                    $values[] = $value;
                }
            }
        }

        return $values;
    }

    /**
     * Check if a value is numeric (allowing for various formats)
     *
     * @param  string  $value  The value to check
     * @return bool True if the value is numeric
     */
    private function isNumeric(string $value): bool
    {
        // Remove common non-numeric characters but keep decimal points and negative signs
        $cleaned = preg_replace('/[^\d.\-]/', '', $value);

        return $cleaned !== null && $cleaned !== '' && is_numeric($cleaned);
    }

    /**
     * Parse a string value into a numeric value
     *
     * @param  string  $value  The value to parse
     * @return null|float The numeric value or null if not numeric
     */
    private function parseNumericValue(string $value): ?float
    {
        // Remove common non-numeric characters (commas, spaces, etc.)
        $cleaned = preg_replace('/[^\d.\-]/', '', $value);

        if ($cleaned !== null && $cleaned !== '' && is_numeric($cleaned)) {
            return (float) $cleaned;
        }

        return null;
    }
}
