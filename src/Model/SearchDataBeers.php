<?php 

namespace App\Model;

class SearchDataBeers
{
    private ?string $name = null;
    private $type = null;
    private ?string $color = null;
    private ?bool $isPermanent = null;
    private ?int $category = null;

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

    public function getType()
    {
        return $this->type;
    }

    public function setType( $type): self
    {
        $this->type = $type;
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

    public function getIsPermanent(): ?bool
    {
        return $this->isPermanent;
    }

    public function setIsPermanent(?bool $isPermanent): self
    {
        $this->isPermanent = $isPermanent;
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
