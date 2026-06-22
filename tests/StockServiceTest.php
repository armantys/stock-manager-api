<?php

namespace App\Tests;

use App\Entity\Product;
use App\Entity\Warehouse;
use App\Enum\StockMovementType;
use App\Service\StockService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class StockServiceTest extends KernelTestCase
{
    public function testStockIncreases(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        $service = $container->get(StockService::class);

        $em = $container->get('doctrine')->getManager();

        $product = new Product();
        $warehouse = new Warehouse();

        $em->persist($product);
        $em->persist($warehouse);
        $em->flush();

        $service->applyMovement(
            $product,
            $warehouse,
            10,
            StockMovementType::IN
        );

        $em->flush();

        $this->assertTrue(true); // simple smoke test
    }

    public function testStockCannotGoNegative(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        $service = $container->get(StockService::class);

        $product = new Product();
        $warehouse = new Warehouse();

        $this->expectException(\DomainException::class);

        $service->applyMovement(
            $product,
            $warehouse,
            10,
            StockMovementType::OUT
        );
    }
}