<?php

namespace App\Dto;

class StockMovementInput
{
    public int $quantity;

    public int $productId;

    public int $warehouseId;

    public string $type; // IN / OUT
}