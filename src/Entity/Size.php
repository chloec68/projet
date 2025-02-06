<?php

namespace App\Entity;

use App\Repository\SizeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SizeRepository::class)]
class Size
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $sizeName = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSizeName(): ?string
    {
        return $this->sizeName;
    }

    public function setSizeName(?string $sizeName): static
    {
        $this->sizeName = $sizeName;

        return $this;
    }

}
