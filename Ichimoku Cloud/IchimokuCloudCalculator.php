<?php 
require "IchimokuCloudDO.php"; 

final class IchimokuCloudCalculator
{
    public static function Calculate(array $data, int $cLinePeriod = 9, int $bLinePeriod = 26, int $spanBPeriod = 52, int $offset = 26): array
    {
        $results = [];
        $totalPeriods = count($data);

        if ($totalPeriods < $spanBPeriod + $offset) {
            // Not enough data
            return $results;
        }

        $tempResults = [];

        for ($i = 0; $i < $totalPeriods; $i++) {
            $result = new IchimokuResult($data[$i]->date);

            // Conversion Line
            if ($i >= $cLinePeriod - 1) {
                $periodHistory = array_slice($data, $i - $cLinePeriod + 1, $cLinePeriod);
                $cLineHigh = max(array_map(fn($item) => $item->high, $periodHistory));
                $cLineLow = min(array_map(fn($item) => $item->low, $periodHistory));
                $result->cLineSen = ($cLineHigh + $cLineLow) / 2;
            }

            // Base Line
            if ($i >= $bLinePeriod - 1) {
                $periodHistory = array_slice($data, $i - $bLinePeriod + 1, $bLinePeriod);
                $bLineHigh = max(array_map(fn($item) => $item->high, $periodHistory));
                $bLineLow = min(array_map(fn($item) => $item->low, $periodHistory));
                $result->bLineSen = ($bLineHigh + $bLineLow) / 2;
            }
            
            $tempResults[] = $result;
        }

        for ($i = 0; $i < $totalPeriods; $i++) {
            $result = $tempResults[$i];

            $finalResult = new IchimokuResult($result->date);
            
            // Span A (plotted 26 periods ahead)
            if ($i >= $bLinePeriod - 1 && $i < $totalPeriods - $offset) {
                $finalResult->spanA = ($tempResults[$i]->conversionLine + $tempResults[$i]->baseLine) / 2;
            }
            
            //  Span B (plotted 26 periods ahead)
            if ($i >= $spanBPeriod - 1 && $i < $totalPeriods - $offset) {
                $periodHistory = array_slice($data, $i - $spanBPeriod + 1, $spanBPeriod);
                $BHigh = max(array_map(fn($item) => $item->high, $periodHistory));
                $BLow = min(array_map(fn($item) => $item->low, $periodHistory));
                $finalResult->SpanB = ($BHigh + $BLow) / 2;
            }

            // plotted on current period
            $finalResult->conversionLine = $result->conversionLine;
            $finalResult->baseLine = $result->baseLine;

            // plotted 26 periods behind
            if ($i >= $offset) {
                $finalResult->laggingSpan = $data[$i - $offset]->close;
            }
            
            $results[] = $finalResult;
        }

        return $results;
    }
}