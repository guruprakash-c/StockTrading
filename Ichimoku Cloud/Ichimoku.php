<?php
require_once 'IchimokuCloudCalculator.php'; 

$historicalData = [];
for ($i = 200; $i >= 0; $i--) {
    $historicalData[] = new StockData(
        (new DateTime())->modify("-{$i} days")->format('Y-m-d'), 
        rand(150, 200), 
        rand(100, 150), 
        rand(100, 200)
    );
}

// Calculate the Ichimoku values
$ichimokuResults = IchimokuCloudCalculator::Calculate($historicalData);

// Prepare the data for Chart.js
$labels = [];
$conversionLineData = [];
$baseLineData = [];
$spanAData = [];
$spanBData = [];
$laggingSpanData = [];

// Apply offsets during data preparation
$offset = 26; // The default offset
$totalPeriods = count($ichimokuResults);

foreach ($ichimokuResults as $i => $result) {
    // Labels are simply the dates from the original data
    $labels[] = $result->date;
    
    // The Tenkan-sen and Kijun-sen don't require an offset
    $conversionLineData[] = $result->conversionLine;
    $baseLineData[] = $result->baseLine;

    // The Chikou Span is plotted 26 periods behind
    if ($i >= $offset) {
        $laggingSpanData[] = $ichimokuResults[$i - $offset]->laggingSpan;
    } else {
        $laggingSpanData[] = null;
    }

    // The Spans are plotted 26 periods ahead
    // Create an empty space for the offset
    if ($i < $totalPeriods - $offset) {
        // Find the index for the future point
        $futureIndex = $i + $offset;

        // Ensure the future index exists before plotting
        if (isset($ichimokuResults[$i]) && $futureIndex < $totalPeriods) {
            $spanAData[$futureIndex] = $result->spanA;
            $spanBData[$futureIndex] = $result->spanB;
        }
    }
}

// Fill any leading gaps for the Spans with nulls
for ($i = 0; $i < $totalPeriods; $i++) {
    if (!isset($spanAData[$i])) {
        $spanAData[$i] = null;
    }
    if (!isset($spanBData[$i])) {
        $spanBData[$i] = null;
    }
}

// Combine all datasets and encode to JSON
$chartData = [
    'labels' => $labels,
    'datasets' => [
        // Conversion Line
        ['label' => 'Conversion Line', 'data' => $conversionLineData, 'borderColor' => 'blue', 'borderWidth' => 2, 'fill' => false, 'pointRadius' => 0],
        // Base Line
        ['label' => 'Base Line', 'data' => $baseLineData, 'borderColor' => 'red', 'borderWidth' => 2, 'fill' => false, 'pointRadius' => 0],
        // Lagging Span
        ['label' => 'Lagging Span', 'data' => $laggingSpanData, 'borderColor' => 'green', 'borderWidth' => 2, 'fill' => false, 'pointRadius' => 0],
        // Span A (Leading Span A)
        ['label' => 'Span A', 'data' => $spanAData, 'borderColor' => 'rgba(75, 192, 192, 0.4)', 'borderWidth' => 1, 'fill' => '+1', 'pointRadius' => 0], // The `fill` property creates the cloud
        // Span B (Leading Span B)
        ['label' => 'Span B', 'data' => $spanBData, 'borderColor' => 'rgba(255, 99, 132, 0.4)', 'borderWidth' => 1, 'pointRadius' => 0],
    ]
];

// Set the header to indicate JSON content
header('Content-Type: application/json');
echo json_encode($chartData);

?>