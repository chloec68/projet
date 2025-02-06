<?php

namespace App\Entity;

use App\Repository\EstablishmentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EstablishmentRepository::class)]
class Establishment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $establishmentName = null;

    #[ORM\Column(length: 50)]
    private ?string $EstablishmentAddress = null;

    #[ORM\Column(length: 10)]
    private ?string $EstablishmentPostcode = null;

    #[ORM\Column(length: 50)]
    private ?string $establishmentCity = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $establishmentOpeningTime = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $establishmentClosingTime = null;

    #[ORM\Column(length: 10)]
    private ?string $establishmentOpeningDay = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEstablishmentName(): ?string
    {
        return $this->establishmentName;
    }

    public function setEstablishmentName(string $establishmentName): static
    {
        $this->establishmentName = $establishmentName;

        return $this;
    }

    public function getEstablishmentAddress(): ?string
    {
        return $this->EstablishmentAddress;
    }

    public function setEstablishmentAddress(string $EstablishmentAddress): static
    {
        $this->EstablishmentAddress = $EstablishmentAddress;

        return $this;
    }

    public function getEstablishmentPostcode(): ?string
    {
        return $this->EstablishmentPostcode;
    }

    public function setEstablishmentPostcode(string $EstablishmentPostcode): static
    {
        $this->EstablishmentPostcode = $EstablishmentPostcode;

        return $this;
    }

    public function getEstablishmentCity(): ?string
    {
        return $this->establishmentCity;
    }

    public function setEstablishmentCity(string $establishmentCity): static
    {
        $this->establishmentCity = $establishmentCity;

        return $this;
    }

    public function getEstablishmentOpeningTime(): ?\DateTimeInterface
    {
        return $this->establishmentOpeningTime;
    }

    public function setEstablishmentOpeningTime(\DateTimeInterface $establishmentOpeningTime): static
    {
        $this->establishmentOpeningTime = $establishmentOpeningTime;

        return $this;
    }

    public function getEstablishmentClosingTime(): ?\DateTimeInterface
    {
        return $this->establishmentClosingTime;
    }

    public function setEstablishmentClosingTime(\DateTimeInterface $establishmentClosingTime): static
    {
        $this->establishmentClosingTime = $establishmentClosingTime;

        return $this;
    }

    public function getEstablishmentOpeningDay(): ?string
    {
        return $this->establishmentOpeningDay;
    }

    public function setEstablishmentOpeningDay(string $establishmentOpeningDay): static
    {
        $this->establishmentOpeningDay = $establishmentOpeningDay;

        return $this;
    }
}
