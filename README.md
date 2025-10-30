# Wikipedia Graph Generator

A Laravel-based application that scrapes Wikipedia pages, identifies numeric columns in tables, and generates visual graphs with clear axis labels.

---

## 📋 Challenge Requirements

**Task**: Write a program that:
- Takes a Wikipedia URL as input
- Scans the page for tables
- Identifies numeric columns in the table
- Plots a graph of the numeric values
- Saves the graph as an image file

**Example**: [Women's High Jump World Record Progression](https://en.wikipedia.org/wiki/Women%27s_high_jump_world_record_progression)

**Time Limit**: ~3 hours focused work

---

## 🎯 Solution Overview

This application solves the Detector Inspector Engineering Challenge by:

1. **Scraping Wikipedia pages** - Fetches HTML content and extracts table data
2. **Identifying numeric columns** - Automatically finds columns with numeric values
3. **Generating graphs** - Creates line graphs with:
   - Clear X-axis labels (Record Number)
   - Clear Y-axis labels (Column name from table header)
   - Title showing the data source
   - Grid lines for better readability
   - Professional styling

## 🏗️ Architecture & Design

### Service-Oriented Architecture

The solution follows SOLID principles with four main services:

1. **WikipediaScraper** - Handles HTTP requests and HTML parsing
2. **NumericColumnExtractor** - Identifies and extracts numeric data from tables
3. **GraphGenerator** - Creates visual graphs with axis labels and styling
4. **WikipediaGraphService** - Orchestrates the entire workflow

### Key Features

- ✅ **Clear Axis Labels**: Both X-axis and Y-axis have descriptive labels
  - X-axis: "Record Number" (index of each data point)
  - Y-axis: Column name from the Wikipedia table header
- ✅ **Test-Driven Development**: Comprehensive unit and feature tests
- ✅ **Dependency Injection**: Clean, testable code using Laravel's service container
- ✅ **Error Handling**: Graceful handling of edge cases and invalid inputs
- ✅ **Type Safety**: Full PHP type hints and PHPStan static analysis

## 🚀 Getting Started

### Prerequisites

- PHP 8.2 or higher
- Composer
- GD extension for image processing

### Installation

1. **Clone the repository**
```bash
git clone <repository-url>
cd software-engineer-challenge
```

2. **Install dependencies**
```bash
composer install
```

3. **Set up environment**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Create storage directories**
```bash
php artisan storage:link
mkdir -p storage/app/public/graphs
chmod -R 775 storage
```

## 📊 Usage

### Command Line Usage

Generate a graph from any Wikipedia page with tables:

```bash
php artisan tinker
```

Then run:
```php
$service = app(App\Services\WikipediaGraphService::class);
$result = $service->generateGraph('https://en.wikipedia.org/wiki/Women%27s_high_jump_world_record_progression');
echo "Graph saved to: {$result['full_path']}\n";
```

### Example Test

Run the included test script:

```bash
php test_graph_with_labels.php
```

This generates a sample graph with clear axis labels showing:
- X-axis: "Record Number"
- Y-axis: "Height (meters)"
- Title: "Women's High Jump World Record Progression"

## 🧪 Testing

The project includes comprehensive test coverage:

### Run All Tests
```bash
./vendor/bin/phpunit
```

### Run Specific Test Suites
```bash
# Unit tests
./vendor/bin/phpunit tests/Unit

# Feature tests
./vendor/bin/phpunit tests/Feature

# Specific test class
./vendor/bin/phpunit --filter=GraphGeneratorTest
```

### Test Coverage

- ✅ WikipediaScraper - Tests HTML fetching and table extraction
- ✅ NumericColumnExtractor - Tests numeric column identification
- ✅ GraphGenerator - Tests graph generation with axis labels
- ✅ WikipediaGraphService - End-to-end integration tests

## 🔍 Code Quality

### Static Analysis
```bash
./vendor/bin/phpstan analyse
```

### Code Formatting
```bash
./vendor/bin/pint
```

---

## 🤖 GitHub Workflows (CI/CD)

This project uses **5 GitHub Actions workflows** for automation and quality assurance:

### 1. **Larastan (PHPStan)** 📊
**File**: `.github/workflows/larastan.yml`

**Trigger**: On every `push` and `pull_request`

**Purpose**: Static code analysis to catch bugs before runtime

**What it does**:
- Runs on PHP 8.2, 8.3, and 8.4 (matrix testing)
- Installs Composer dependencies
- Runs PHPStan analysis with error reporting to GitHub
- Ensures code quality and type safety

**Command**: `composer analyse --error-format=github`

**Matrix Strategy**: Tests across 3 PHP versions to ensure compatibility

---

### 2. **Laravel Pint (Code Formatting)** 🎨
**File**: `.github/workflows/laravel-pint.yml`

**Trigger**: On every `push`

**Purpose**: Automatically fix code style issues

**What it does**:
- Checks out code from the current branch
- Sets up PHP 8.4 with Composer
- Installs dependencies
- Runs Pint to fix code styling
- Auto-commits formatting changes back to the branch

**Command**: `vendor/bin/pint`

**Auto-commit**: Uses `stefanzweifel/git-auto-commit-action@v5` with message "Fix styling"

---

### 3. **IDE Helper Generator** 💡
**File**: `.github/workflows/ide-helper.yml`

**Trigger**: On every `push`

**Purpose**: Generate IDE helper files for better autocomplete and IntelliSense

**What it does**:
- Creates SQLite database for Laravel
- Generates IDE helper files:
  - `ide-helper:generate` - Core Laravel helpers
  - `ide-helper:models -W` - Model docblocks
  - `ide-helper:eloquent` - Eloquent helpers
- Runs Pint to format generated files
- Auto-commits helper files with message "chore: update IDE helper files"

**Files Generated**: `_ide_helper.php` and model annotations

---

### 4. **Dependabot Auto-Merge** 🔄
**File**: `.github/workflows/dependabot-auto-merge.yml`

**Trigger**: On `pull_request_target` from Dependabot

**Purpose**: Automatically merge safe dependency updates

**What it does**:
- Detects Dependabot PRs
- Fetches update metadata (major/minor/patch)
- Auto-merges:
  - ✅ **Semver-minor** updates (new features, backward compatible)
  - ✅ **Semver-patch** updates (bug fixes)
  - ❌ **Semver-major** updates (breaking changes - requires manual review)

**Safety**: Only acts on Dependabot PRs, not human-created PRs

---

### 5. **Deploy to Test Server** 🚀
**File**: `.github/workflows/deploy.yml`

**Trigger**: On `push` to `main` branch

**Purpose**: Automatically deploy to remote test server

**What it does**:
- Uses self-hosted runner
- SSH into remote server using secrets
- Pulls latest code from `main` branch
- Installs/updates Composer dependencies
- Clears all Laravel caches
- Runs database migrations
- Rebuilds cache (config, routes, views)
- Creates storage symlink

**Deployment Steps**:
1. `git pull origin main`
2. `composer install --optimize-autoloader`
3. Clear caches (compiled, cache, config, route, view)
4. `php artisan migrate --force`
5. Rebuild caches (config, route, view)
6. `php artisan storage:link`

**Secrets Required**:
- `REMOTE_HOST` - Server hostname/IP
- `REMOTE_USERNAME` - SSH username
- `REMOTE_PASS` - SSH password
- `SSH_PRIVATE_KEY` - SSH private key
- `REMOTE_PORT` - SSH port

---

### 6. **Dependabot Configuration** 📦
**File**: `.github/dependabot.yml`

**Purpose**: Automatic dependency updates

**What it does**:
- Monitors GitHub Actions versions
- Checks for updates **daily**
- Creates PRs for outdated actions
- Works with auto-merge workflow for safe updates

**Ecosystem**: `github-actions`

---

## 🔐 Workflow Permissions

### Write Permissions (Auto-commit workflows):
- `laravel-pint.yml` - commits formatted code
- `ide-helper.yml` - commits generated helper files
- `dependabot-auto-merge.yml` - merges PRs

### Read Permissions:
- `larastan.yml` - only reads code, reports errors

### Deploy Permissions:
- `deploy.yml` - uses self-hosted runner with SSH access

---

## 🎯 Benefits of CI/CD Setup

1. **Code Quality**: Automatic PHPStan checks on every commit
2. **Consistent Formatting**: Pint auto-fixes code style
3. **Better DX**: IDE helpers improve autocomplete
4. **Up-to-date Dependencies**: Dependabot keeps dependencies current
5. **Automated Deployment**: Push to main = auto-deploy to test server
6. **Multi-version Testing**: Ensures PHP 8.2, 8.3, 8.4 compatibility

## 📝 Assumptions & Design Decisions

### Assumptions

1. **Wikipedia Table Structure**: Tables have header rows with column names
2. **Numeric Data**: At least one column contains parseable numeric values
3. **Data Order**: The order of rows in the table is meaningful (sequential records)
4. **Output Format**: PNG format is acceptable for graph output

### Design Decisions

1. **Laravel Framework**: Provides robust foundation with DI, testing, and file storage
2. **Intervention Image**: Reliable PHP library for image manipulation with GD driver
3. **DOMDocument**: Built-in PHP parser for HTML without external dependencies
4. **Service Layer**: Clean separation of concerns for testability and maintainability
5. **Axis Labels**: 
   - X-axis shows "Record Number" as data represents sequential records
   - Y-axis shows the actual column name from the Wikipedia table
   - Both labels are clearly positioned and readable

### Graph Features

- **800x600 pixels** - Good balance between quality and file size
- **60px padding** - Ensures labels don't get cut off
- **Grid lines** - Improves readability
- **Blue line with red points** - Clear data visualization
- **Rotated Y-axis label** - Standard convention for vertical axis
- **Centered labels** - Professional appearance

## 📊 Graph Axis Labels Feature

### Overview
The graph generator includes clear, descriptive labels for both X-axis (horizontal) and Y-axis (vertical) to make visualizations professional and understandable.

### Axis Label Details

#### X-Axis Label (Horizontal)
- **Label**: "Record Number"
- **Position**: Centered at the bottom of the graph
- **Purpose**: Indicates each point represents a sequential record from the Wikipedia table
- **Styling**: 14pt font, centered alignment

#### Y-Axis Label (Vertical)
- **Label**: Column name from Wikipedia table header (e.g., "Height", "Mark", "Time")
- **Position**: Left side of the graph, rotated 90 degrees
- **Purpose**: Shows what measurement or value is being plotted
- **Styling**: 14pt font, rotated text for optimal space usage

#### Title
- **Format**: "Wikipedia Data: [Column Name]"
- **Position**: Top center of the graph
- **Styling**: 20pt font

### Visual Layout
```
                    Wikipedia Data: Height
                    
Height              [Graph Area with Grid]
(meters)            • Data points
(rotated 90°)       • Connected lines
                    • Y-axis scale numbers
                    • X-axis index numbers
                    
                    Record Number
```

### Implementation Example

```php
// Automatic labels from Wikipedia
$service = app(WikipediaGraphService::class);
$result = $service->generateGraph($wikipediaUrl);
// Generates: X-axis: "Record Number", Y-axis: Column name from table

// Custom labels
$generator = new GraphGenerator();
$generator->generateGraph(
    $data,
    'output.png',
    'Custom Title',
    'Time Period (years)',    // Custom X-axis
    'Performance Score'        // Custom Y-axis
);
```

### Font Specifications
- **Title**: 20pt, centered
- **Axis labels**: 14pt
- **Scale numbers**: 12pt
- **Y-axis label**: Rotated 90 degrees (standard graph convention)

### Positioning Details
- **X-axis label**: `WIDTH / 2, HEIGHT - 15`
- **Y-axis label**: `15, HEIGHT / 2` (rotated 90°)
- Automatic centering and alignment

---

## 📦 Dependencies

- **Laravel 11.x** - Application framework
- **Intervention Image 3.x** - Image manipulation and graph generation
- **GuzzleHTTP** - HTTP client for fetching Wikipedia pages
- **PHPUnit** - Testing framework
- **PHPStan** - Static analysis

## 🔧 Technical Implementation Details

### Service Architecture

#### 1. WikipediaScraper Service
```php
class WikipediaScraper
{
    // Fetches Wikipedia page HTML
    public function fetchPage(string $url): string
    
    // Extracts tables from HTML using DOMDocument
    public function extractTables(string $html): array
}
```

#### 2. NumericColumnExtractor Service
```php
class NumericColumnExtractor
{
    // Identifies which columns contain numeric data
    public function identifyNumericColumns(array $table): array
    
    // Extracts numeric values from a specific column
    public function extractColumnValues(array $table, int $columnIndex): array
}
```

#### 3. GraphGenerator Service
```php
class GraphGenerator
{
    // Main method with axis labels support
    public function generateGraph(
        array $data,
        string $outputPath,
        string $title = 'Numeric Data Visualization',
        string $xAxisLabel = 'Index',
        string $yAxisLabel = 'Value'
    ): bool
    
    // Internal methods
    private function drawGrid($image): void
    private function drawAxes($image): void
    private function drawTitle($image, string $title): void
    private function drawData($image, array $data): void
    private function drawLabels($image, array $data): void
    private function drawAxisLabels($image, string $xAxisLabel, string $yAxisLabel): void
}
```

#### 4. WikipediaGraphService (Orchestrator)
```php
class WikipediaGraphService
{
    public function generateGraph(string $url): array
    {
        // 1. Scrape Wikipedia page
        $html = $this->scraper->fetchPage($url);
        $tables = $this->scraper->extractTables($html);
        
        // 2. Find numeric columns
        $columns = $this->extractor->identifyNumericColumns($table);
        
        // 3. Extract column name for Y-axis label
        $columnName = $table[0][$columnIndex] ?? 'Numeric Column';
        
        // 4. Generate graph with labels
        $this->generator->generateGraph(
            $values,
            $fullPath,
            'Wikipedia Data: ' . $columnName,
            'Record Number',  // X-axis
            $columnName       // Y-axis
        );
        
        return ['path' => $outputPath, 'url' => $url, ...];
    }
}
```

### Graph Generation Process

1. **Create Canvas**: 800x600 pixels with white background
2. **Draw Grid**: 10x10 grid lines for reference
3. **Draw Axes**: X and Y axes with proper scaling
4. **Draw Title**: Centered at top (20pt)
5. **Plot Data**: Blue line connecting red data points
6. **Draw Scale Labels**: Numeric values on Y-axis, indices on X-axis
7. **Draw Axis Labels**: Descriptive text for X and Y axes (14pt)
8. **Save Image**: PNG format with RGBA color

### Constants & Configuration
```php
private const WIDTH = 800;           // Image width in pixels
private const HEIGHT = 600;          // Image height in pixels
private const PADDING = 60;          // Padding around graph area
private const BACKGROUND_COLOR = '#ffffff';  // White background
private const GRID_COLOR = '#e0e0e0';        // Light gray grid
private const AXIS_COLOR = '#333333';        // Dark gray axes
private const LINE_COLOR = '#2563eb';        // Blue data line
private const POINT_COLOR = '#dc2626';       // Red data points
```

---

## 🎓 What Was Learned

This challenge demonstrated:

1. **Holistic Problem Solving** - Breaking down complex requirements into manageable services
2. **TDD Approach** - Writing tests first to drive implementation
3. **Clean Code** - SOLID principles, dependency injection, and type safety
4. **Documentation** - Clear comments and comprehensive README
5. **User Experience** - Adding clear axis labels makes graphs much more understandable
6. **Data Visualization Best Practices** - Following standard conventions for graph labeling

## 📸 Output Example

The generated graphs include:
- Title at the top (e.g., "Wikipedia Data: Height")
- Y-axis label on the left (e.g., "Height (meters)") - rotated 90 degrees
- X-axis label at the bottom (e.g., "Record Number")
- Numeric scale on Y-axis
- Index numbers on X-axis
- Grid for reference
- Data points connected with lines

### Sample Output
- **Location**: `storage/app/test_graph_with_labels.png`
- **Size**: 12KB
- **Dimensions**: 800x600 pixels
- **Format**: PNG with RGBA color

---

## 🆕 Recent Improvements

### Axis Labels Enhancement (October 2025)

**What Changed**:
1. **GraphGenerator.php**
   - Added `$xAxisLabel` and `$yAxisLabel` parameters to `generateGraph()` method
   - New `drawAxisLabels()` method for rendering axis labels
   - X-axis label: Centered at bottom (14pt)
   - Y-axis label: Rotated 90° on left side (14pt)

2. **WikipediaGraphService.php**
   - Automatically extracts column name from table header
   - Passes column name as Y-axis label
   - Uses "Record Number" as X-axis label
   - Enhanced title format: "Wikipedia Data: [Column Name]"

3. **Test Coverage**
   - ✅ 12 tests, 20 assertions, all passing
   - ✅ PHPStan: No errors
   - ✅ Pint: Code formatted
   - ✅ Feature tests verify end-to-end functionality

**Benefits**:
- ✅ Improved clarity - Users immediately understand what the graph represents
- ✅ Professional appearance - Follows standard graphing conventions
- ✅ Automatic labeling - Column names extracted from Wikipedia
- ✅ Flexible customization - Can override default labels
- ✅ Backward compatible - Existing code continues to work

**Files Modified**:
- `app/Services/GraphGenerator.php` - Added axis label support
- `app/Services/WikipediaGraphService.php` - Enhanced to pass column names
- `test_graph_with_labels.php` - Demonstration script created

## 📄 License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
