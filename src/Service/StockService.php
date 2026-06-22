<?php

namespace App\Service;

use App\Entity\Product;
use App\Entity\Warehouse;
use App\Entity\Stock;
use App\Enum\StockMovementType;
use Doctrine\ORM\EntityManagerInterface;

class StockService
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    public function applyMovement(
        Product $product,
        Warehouse $warehouse,
        int $quantity,
        StockMovementType $type
    ): void {
        $stockRepo = $this->em->getRepository(Stock::class);

        $stock = $stockRepo->findOneBy([
            'product' => $product,
            'warehouse' => $warehouse,
        ]);

        if (!$stock) {
            $stock = new Stock();
            $stock->setProduct($product);
            $stock->setWarehouse($warehouse);
            $stock->setQuantity(0);

            $this->em->persist($stock);
        }

        if ($type === StockMovementType::IN) {
            $stock->setQuantity($stock->getQuantity() + $quantity);
        }

        if ($type === StockMovementType::OUT) {
            $newQty = $stock->getQuantity() - $quantity;

            if ($newQty < 0) {
                throw new \DomainException('Stock insuffisant');
            }

            $stock->setQuantity($newQty);
        }

        $this->em->persist($stock);
    }
}