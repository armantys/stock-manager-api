<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use App\Entity\StockMovement;
use App\Enum\StockMovementType;
use App\Service\StockService;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;

class StockMovementProcessor implements ProcessorInterface
{
    public function __construct(
        private StockService $stockService,
        private EntityManagerInterface $em
    ) {}

    public function process(
        mixed $data,
        Operation $operation,
        array $uriVariables = [],
        array $context = []
    ): mixed {
        if (!$data instanceof StockMovement) {
            return $data;
        }

        $this->stockService->applyMovement(
            $data->getProduct(),
            $data->getWarehouse(),
            $data->getQuantity(),
            StockMovementType::from($data->getType())
        );

        $this->em->persist($data);
        $this->em->flush();

        return $data;
    }
}