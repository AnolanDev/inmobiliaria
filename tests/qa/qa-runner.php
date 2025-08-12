<?php

/**
 * QA Test Suite Runner
 * 
 * Professional test execution script with comprehensive reporting
 */

class QATestRunner
{
    private array $testSuites = [
        'unit' => 'tests/qa/Unit',
        'integration' => 'tests/qa/Integration', 
        'security' => 'tests/qa/Security',
        'performance' => 'tests/qa/Performance',
        'api' => 'tests/qa/API'
    ];
    
    private string $reportDir = 'tests/qa/Reports';
    private array $results = [];
    
    public function __construct()
    {
        $this->ensureDirectoriesExist();
        $this->displayBanner();
    }
    
    private function displayBanner(): void
    {
        echo "\n";
        echo "🧪═══════════════════════════════════════════════════════════════════\n";
        echo "   INMOBILIARIA QA TEST SUITE v1.0                                   \n";
        echo "   Professional Quality Assurance Testing Framework                  \n";
        echo "═══════════════════════════════════════════════════════════════════🧪\n\n";
    }
    
    public function runAllTests(): void
    {
        $startTime = microtime(true);
        
        echo "🚀 Starting comprehensive QA test execution...\n\n";
        
        foreach ($this->testSuites as $suiteName => $suitePath) {
            $this->runTestSuite($suiteName, $suitePath);
        }
        
        $totalTime = microtime(true) - $startTime;
        $this->generateFinalReport($totalTime);
    }
    
    public function runTestSuite(string $name, string $path): void
    {
        echo "📋 Running {$name} tests...\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        
        $startTime = microtime(true);
        
        // Build PHPUnit command
        $command = $this->buildTestCommand($name, $path);
        
        // Execute tests
        $output = [];
        $returnCode = 0;
        exec($command, $output, $returnCode);
        
        $endTime = microtime(true);
        $executionTime = round(($endTime - $startTime) * 1000, 2);
        
        // Parse results
        $this->results[$name] = [
            'execution_time' => $executionTime,
            'exit_code' => $returnCode,
            'output' => implode("\n", $output),
            'success' => $returnCode === 0
        ];
        
        // Display immediate results
        $this->displaySuiteResults($name);
        echo "\n";
    }
    
    private function buildTestCommand(string $name, string $path): string
    {
        $configFile = "tests/qa/Config/phpunit.qa.xml";
        $coverageDir = "{$this->reportDir}/coverage/{$name}";
        $junitFile = "{$this->reportDir}/junit-{$name}.xml";
        
        return "php vendor/bin/phpunit " .
               "--configuration {$configFile} " .
               "--testsuite \"QA " . ucfirst($name) . " Tests\" " .
               "--no-coverage " .
               "--log-junit {$junitFile} " .
               "--testdox-html {$this->reportDir}/{$name}-testdox.html";
    }
    
    private function displaySuiteResults(string $name): void
    {
        $result = $this->results[$name];
        $status = $result['success'] ? '✅ PASSED' : '❌ FAILED';
        $time = $result['execution_time'];
        
        echo "🎯 {$name} Tests: {$status} ({$time}ms)\n";
        
        if (!$result['success']) {
            echo "🔍 Error Details:\n";
            echo $this->extractErrorSummary($result['output']);
        }
    }
    
    private function extractErrorSummary(string $output): string
    {
        $lines = explode("\n", $output);
        $errorLines = array_filter($lines, function($line) {
            return strpos($line, 'FAILURES!') !== false ||
                   strpos($line, 'ERRORS!') !== false ||
                   strpos($line, 'Failed') !== false ||
                   strpos($line, 'Error:') !== false;
        });
        
        return implode("\n", array_slice($errorLines, 0, 5)) . "\n";
    }
    
    private function generateFinalReport(float $totalTime): void
    {
        echo "📊═══════════════════════════════════════════════════════════════════\n";
        echo "   FINAL QA REPORT                                                   \n";
        echo "═══════════════════════════════════════════════════════════════════📊\n\n";
        
        $passedSuites = 0;
        $totalSuites = count($this->results);
        
        foreach ($this->results as $suiteName => $result) {
            $status = $result['success'] ? '✅' : '❌';
            $time = $result['execution_time'];
            echo "  {$status} " . ucfirst($suiteName) . " Tests: {$time}ms\n";
            
            if ($result['success']) {
                $passedSuites++;
            }
        }
        
        echo "\n";
        echo "🕐 Total Execution Time: " . round($totalTime * 1000, 2) . "ms\n";
        echo "📈 Success Rate: {$passedSuites}/{$totalSuites} (" . round(($passedSuites/$totalSuites)*100, 1) . "%)\n";
        
        if ($passedSuites === $totalSuites) {
            echo "\n🎉 ALL TESTS PASSED! System is ready for deployment.\n";
        } else {
            echo "\n⚠️  Some tests failed. Review reports before deployment.\n";
        }
        
        echo "\n📁 Reports generated in: {$this->reportDir}/\n";
        echo "🌐 View HTML reports: file://" . realpath($this->reportDir) . "/\n\n";
        
        $this->generateHtmlDashboard();
    }
    
    private function generateHtmlDashboard(): void
    {
        $html = $this->buildHtmlDashboard();
        file_put_contents("{$this->reportDir}/qa-dashboard.html", $html);
        echo "📊 QA Dashboard: {$this->reportDir}/qa-dashboard.html\n";
    }
    
    private function buildHtmlDashboard(): string
    {
        $passedCount = count(array_filter($this->results, fn($r) => $r['success']));
        $totalCount = count($this->results);
        $successRate = round(($passedCount / $totalCount) * 100, 1);
        
        $suiteRows = '';
        foreach ($this->results as $name => $result) {
            $status = $result['success'] ? '✅ PASSED' : '❌ FAILED';
            $statusClass = $result['success'] ? 'success' : 'danger';
            
            $suiteRows .= "
                <tr class='table-{$statusClass}'>
                    <td>" . ucfirst($name) . "</td>
                    <td><span class='badge badge-{$statusClass}'>{$status}</span></td>
                    <td>{$result['execution_time']}ms</td>
                    <td>
                        <a href='coverage/{$name}/index.html' class='btn btn-sm btn-outline-primary'>Coverage</a>
                        <a href='{$name}-testdox.html' class='btn btn-sm btn-outline-info'>Details</a>
                    </td>
                </tr>";
        }
        
        return "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>QA Dashboard - Inmobiliaria</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <style>
        .qa-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .metric-card { border-left: 4px solid #007bff; }
        .success-metric { border-left-color: #28a745; }
        .danger-metric { border-left-color: #dc3545; }
    </style>
</head>
<body>
    <div class='container-fluid'>
        <div class='row qa-header py-4 mb-4'>
            <div class='col'>
                <h1>🧪 QA Testing Dashboard</h1>
                <p class='mb-0'>Inmobiliaria Quality Assurance Report - " . date('Y-m-d H:i:s') . "</p>
            </div>
        </div>
        
        <div class='row mb-4'>
            <div class='col-md-3'>
                <div class='card metric-card success-metric'>
                    <div class='card-body'>
                        <h5>Success Rate</h5>
                        <h2 class='text-success'>{$successRate}%</h2>
                    </div>
                </div>
            </div>
            <div class='col-md-3'>
                <div class='card metric-card'>
                    <div class='card-body'>
                        <h5>Total Suites</h5>
                        <h2>{$totalCount}</h2>
                    </div>
                </div>
            </div>
            <div class='col-md-3'>
                <div class='card metric-card success-metric'>
                    <div class='card-body'>
                        <h5>Passed</h5>
                        <h2 class='text-success'>{$passedCount}</h2>
                    </div>
                </div>
            </div>
            <div class='col-md-3'>
                <div class='card metric-card danger-metric'>
                    <div class='card-body'>
                        <h5>Failed</h5>
                        <h2 class='text-danger'>" . ($totalCount - $passedCount) . "</h2>
                    </div>
                </div>
            </div>
        </div>
        
        <div class='card'>
            <div class='card-header'>
                <h3>Test Suite Results</h3>
            </div>
            <div class='card-body'>
                <table class='table table-striped'>
                    <thead>
                        <tr>
                            <th>Test Suite</th>
                            <th>Status</th>
                            <th>Execution Time</th>
                            <th>Reports</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$suiteRows}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>";
    }
    
    private function ensureDirectoriesExist(): void
    {
        $dirs = [
            $this->reportDir,
            "{$this->reportDir}/coverage",
            "{$this->reportDir}/junit"
        ];
        
        foreach ($this->testSuites as $suite => $path) {
            $dirs[] = "{$this->reportDir}/coverage/{$suite}";
        }
        
        foreach ($dirs as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }
    }
    
    public function runSpecificSuite(string $suiteName): void
    {
        if (!isset($this->testSuites[$suiteName])) {
            echo "❌ Unknown test suite: {$suiteName}\n";
            echo "Available suites: " . implode(', ', array_keys($this->testSuites)) . "\n";
            return;
        }
        
        $this->runTestSuite($suiteName, $this->testSuites[$suiteName]);
        echo "\n📁 Reports generated in: {$this->reportDir}/\n";
    }
}

// CLI interface
if (php_sapi_name() === 'cli') {
    $runner = new QATestRunner();
    
    $suite = $argv[1] ?? 'all';
    
    if ($suite === 'all') {
        $runner->runAllTests();
    } else {
        $runner->runSpecificSuite($suite);
    }
}