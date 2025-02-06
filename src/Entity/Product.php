<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $productName = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $ProductDescription = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private ?string $productPrice = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $productAlcoholLevel = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $productVolume = null;

    #[ORM\Column(length: 50)]
    private ?string $productColor = null;

    #[ORM\Column]
    private ?int $productStock = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductName(): ?string
    {
        return $this->productName;
    }

    public function setProductName(string $productName): static
    {
        $this->productName = $productName;

        return $this;
    }

    public function getProductDescription(): ?string
    {
        return $this->ProductDescription;
    }

    public function setProductDescription(?string $ProductDescription): static
    {
        $this->ProductDescription = $ProductDescription;

        return $this;
    }

    public function getProductPrice(): ?string
    {
        return $this->productPrice;
    }

    public function setProductPrice(string $productPrice): static
    {
        $this->productPrice = $productPrice;

        return $this;
    }

    public function getProductAlcoholLevel(): ?string
    {
        return $this->productAlcoholLevel;
    }

    public function setProductAlcoholLevel(?string $productAlcoholLevel): static
    {
        $this->productAlcoholLevel = $productAlcoholLevel;

        return $this;
    }

    public function getProductVolume(): ?string
    {
        return $this->productVolume;
    }

    public function setProductVolume(?string $productVolume): static
    {
        $this->productVolume = $productVolume;

        return $this;
    }

    public function getProductColor(): ?string
    {
        return $this->productColor;
    }

    public function setProductColor(string $productColor): static
    {
        $this->productColor = $productColor;

        return $this;
    }

    public function getProductStock(): ?int
    {
        return $this->productStock;
    }

    public function setProductStock(int $productStock): static
    {
        $this->productStock = $productStock;

        return $this;
    }
}
