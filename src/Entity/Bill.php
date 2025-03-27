<?php

namespace App\Entity;

use App\Repository\BillRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BillRepository::class)]
class Bill
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $billReferenceNumber = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2)]
    private ?string $billTotalVat = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2)]
    private ?string $billTotalBeforeVat = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $billDate = null;

    #[ORM\OneToOne(inversedBy: 'bill', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Order $appOrder = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $billPath = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBillReferenceNumber(): ?string
    {
        return $this->billReferenceNumber;
    }

    public function setBillReferenceNumber(string $billReferenceNumber): static
    {
        $this->billReferenceNumber = $billReferenceNumber;

        return $this;
    }

    public function getBillTotalVat(): ?string
    {
        return $this->billTotalVat;
    }

    public function setBillTotalVat(string $billTotalVat): static
    {
        $this->billTotalVat = $billTotalVat;

        return $this;
    }

    public function getBillTotalBeforeVat(): ?string
    {
        return $this->billTotalBeforeVat;
    }

    public function setBillTotalBeforeVat(string $billTotalBeforeVat): static
    {
        $this->billTotalBeforeVat = $billTotalBeforeVat;

        return $this;
    }

    public function getBillDate(): ?\DateTimeInterface
    {
        return $this->billDate;
    }

    public function setBillDate(\DateTimeInterface $billDate): static
    {
        $this->billDate = $billDate;

        return $this;
    }


    public function getAppOrder(): ?Order
    {
        return $this->appOrder;
    }

    public function setAppOrder(Order $appOrder): static
    {
        $this->appOrder = $appOrder;

        return $this;
    }

    public function getBillPath(): ?string
    {
        return $this->billPath;
    }

    public function setBillPath(?string $billPath): static
    {
        $this->billPath = $billPath;

        return $this;
    }
}
