<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use App\State\StockMovementProcessor;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new Post(
            processor: StockMovementProcessor::class
        ),
        new Get()
    ],
    denormalizationContext: ['groups' => ['stock:write']],
    normalizationContext: ['groups' => ['stock:read']]
)]
#[ORM\Entity]
class StockMovement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['stock:read'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\Positive]
    #[Groups(['stock:write', 'stock:read'])]
    private ?int $quantity = null;

    #[ORM\Column(length: 3)]
    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['IN', 'OUT'])]
    #[Groups(['stock:read', 'stock:write'])]
    private ?string $type = null;

    #[ORM\Column]
    #[Groups(['stock:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'stockMovements')]
    #[Groups(['stock:read', 'stock:write'])]
    private ?Product $product = null;

    #[ORM\ManyToOne(inversedBy: 'stockMovements')]
    #[Groups(['stock:read', 'stock:write'])]
    private ?Warehouse $warehouse = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $type = strtoupper($type);

        if (!in_array($type, ['IN', 'OUT'], true)) {
            throw new \InvalidArgumentException('Type invalide');
        }

        $this->type = $type;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;
        return $this;
    }

    public function getWarehouse(): ?Warehouse
    {
        return $this->warehouse;
    }

    public function setWarehouse(?Warehouse $warehouse): static
    {
        $this->warehouse = $warehouse;
        return $this;
    }
}