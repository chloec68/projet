<?php 

namespace App\Model;

class SearchDataGoodies
{
    private ?string $name = null;
    private ?string $productGender = null;
    private ?string $color = null;
    private ?int $category = null;
    private $size = null;

    // Getters and Setters
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getProductGender()
    {
        return $this->productGender;
    }

    public function setProductGender($productGender): self
    {
        $this->productGender = $productGender;
        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;
        return $this;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function setSize($size): self
    {
        $this->size = $size;
        return $this;
    }

    public function getCategory(): int
    {
        return $this->category;
    }

    public function setCategory($category): self
    {
        $this->category = $category;
        return $this;
    }
}
