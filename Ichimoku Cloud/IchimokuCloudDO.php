<?php

class StockData
{
    public $date;
    public $high;
    public $low;
    public $close;

    public function __construct($date, $high, $low, $close)
    {
        $this->date = $date;
        $this->high = $high;
        $this->low = $low;
        $this->close = $close;
    }
}

class IchimokuResult
{
    public $date;
    public $conversionLine = null;
    public $baseLine = null;
    public $spanA = null;
    public $spanB = null;
    public $laggingSpan = null;

    public function __construct($date)
    {
        $this->date = $date;
    }
}
