<?php

namespace App\Entity;

use App\Repository\EstablishmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @var Collection<int, Order>
     */
    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'establishment')]
    private Collection $orders;

    /**
     * @var Collection<int, Schedule>
     */
    #[ORM\OneToMany(targetEntity: Schedule::class, mappedBy: 'establishment', orphanRemoval: true)]
    private Collection $schedules;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->schedules = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    // public function addOrder(Order $order): static
    // {
    //     if (!$this->orders->contains($order)) {
    //         $this->orders->add($order);
    //         $order->setEstablishment($this);
    //     }

    //     return $this;
    // }

    public function removeOrder(Order $order): static
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getEstablishment() === $this) {
                $order->setEstablishment(null);
            }
        }

        return $this;
    }


    public function __toString()
    {
        return $this->establishmentName;
    }

    /**
     * @return Collection<int, Schedule>
     */
    public function getSchedules(): Collection
    {
        return $this->schedules;
    }

    public function addSchedule(Schedule $schedule): static
    {
        if (!$this->schedules->contains($schedule)) {
            $this->schedules->add($schedule);
            $schedule->setEstablishment($this);
        }

        return $this;
    }

    public function removeSchedule(Schedule $schedule): static
    {
        if ($this->schedules->removeElement($schedule)) {
            // set the owning side to null (unless already changed)
            if ($schedule->getEstablishment() === $this) {
                $schedule->setEstablishment(null);
            }
        }

        return $this;
    }
}
