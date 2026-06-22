<?php

namespace App\Enum;

enum StockMovementType: string
{
    case IN = 'IN';
    case OUT = 'OUT';
}