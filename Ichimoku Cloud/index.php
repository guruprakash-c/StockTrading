<?php
require_once 'IchimokuCloudCalculator.php'; 

$historicalData = [
    // Add at least 78 data points for a full calculation
    new StockData('2025-01-01', 100, 90, 95),
    new StockData('2025-03-10', 110, 100, 105),
    new StockData('2025-05-01', 120, 110, 115),
    new StockData('2025-07-01', 150, 140, 145),
    new StockData('2025-07-02', 160, 150, 155),
];

for ($i = 100; $i > 0; $i--) {
    $historicalData[] = new StockData(
        (new DateTime())->modify("-{$i} days")->format('Y-m-d'), 
        rand(150, 200), 
        rand(100, 150), 
        rand(100, 200)
    );
}

$ichimokuResults `= IchimokuCloudCalculator::Calculate($historicalData);
header('Content-Type: application/json; charset=utf-8');
echo json_encode($ichimokuResults);
?>

<?php 
// foreach ($ichimokuResults as $result) {
//     echo "Date: {$result->date}, 
//           Conversion Line: {$result->conversionLine}, 
//           Base Line: {$result->baseLine}, 
//           Span A: {$result->spanA}, 
//           Span B: {$result->spanB}, 
//           Lagging: {$result->laggingSpan} \n";
// }
?>
