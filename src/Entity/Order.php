<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateOfPlacement = null;

    #[ORM\Column(length: 50)]
    private ?string $orderReference = null;

    #[ORM\Column(length: 50)]
    private ?string $orderUserFirstName = null;

    #[ORM\Column(length: 50)]
    private ?string $orderUserLastName = null;

    #[ORM\Column]
    private ?bool $orderIsCollected = false;

    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2)]
    private ?string $orderTotal = null;

    #[ORM\Column(length: 255)]
    private ?string $orderEmail = null;

    // /**
    //  * @var Collection<int, Product>
    //  */
    // #[ORM\ManyToMany(targetEntity: Product::class, inversedBy: 'orders')]
    // private Collection $products;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $appUser = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Establishment $establishment = null;

    #[ORM\OneToOne(mappedBy: 'appOrder', cascade: ['persist', 'remove'])]
    private ?Bill $bill = null;

    /**
     * @var Collection<int, OrderProducts>
     */
    #[ORM\OneToMany(targetEntity: OrderProducts::class, mappedBy: 'appOrder', cascade:['persist'])]
    private Collection $orderProducts;


    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->orderProducts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateOfPlacement(): ?\DateTimeInterface
    {
        return $this->dateOfPlacement;
    }

    public function setDateOfPlacement(\DateTimeInterface $dateOfPlacement): static
    {
        $this->dateOfPlacement = $dateOfPlacement;

        return $this;
    }

    public function getOrderReference(): ?string
    {
        return $this->orderReference;
    }

    public function setOrderReference(string $orderReference): static
    {
        $this->orderReference = $orderReference;

        return $this;
    }

    public function getOrderUserFirstName(): ?string
    {
        return $this->orderUserFirstName;
    }

    public function setOrderUserFirstName(string $orderUserFirstName): static
    {
        $this->orderUserFirstName = $orderUserFirstName;

        return $this;
    }

    public function getOrderUserLastName(): ?string
    {
        return $this->orderUserLastName;
    }

    public function setOrderUserLastName(string $orderUserLastName): static
    {
        $this->orderUserLastName = $orderUserLastName;

        return $this;
    }

    public function isOrderIsCollected(): ?bool
    {
        return $this->orderIsCollected;
    }

    public function setOrderIsCollected(bool $orderIsCollected): static
    {
        $this->orderIsCollected = $orderIsCollected;

        return $this;
    }

    public function getOrderTotal(): ?string
    {
        return $this->orderTotal;
    }

    public function setOrderTotal(string $orderTotal): static
    {
        $this->orderTotal = $orderTotal;

        return $this;
    }

    public function getOrderEmail(): ?string
    {
        return $this->orderEmail;
    }

    public function setOrderEmail(string $orderEmail): static
    {
        $this->orderEmail = $orderEmail;

        return $this;
    }

    // /**
    //  * @return Collection<int, Product>
    //  */
    // public function getProducts(): Collection
    // {
    //     return $this->products;
    // }

    // public function addProduct(Product $product, int $quantity): static
    // {
    //     if (!$this->products->contains($product)) {
    //         $this->products->add($product);
    //         // $this->productQuantity[$product->getId()] = $quantity;
    //     }

    //     return $this;
    // }

    // public function getProductQuantity(Product $product): ?int
    // {   

    //     return $this->productQuantity[$product->getId()];
    // }

    // public function removeProduct(Product $product): static
    // {
    //     $this->products->removeElement($product);

    //     return $this;
    // }

    public function getAppUser(): ?User
    {
        return $this->appUser;
    }

    public function setAppUser(?User $appUser): static
    {
        $this->appUser = $appUser;

        return $this;
    }

    public function getEstablishment(): ?Establishment
    {
        return $this->establishment;
    }

    public function setEstablishment(?Establishment $establishment): static
    {
        $this->establishment = $establishment;

        return $this;
    }

    public function getBill(): ?Bill
    {
        return $this->bill;
    }

    public function setBill(Bill $bill): static
    {
        // set the owning side of the relation if necessary
        if ($bill->getAppOrder() !== $this) {
            $bill->setAppOrder($this);
        }

        $this->bill = $bill;

        return $this;
    }

    /**
     * @return Collection<int, OrderProducts>
     */
    public function getOrderProducts(): Collection
    {
        return $this->orderProducts;
    }

    public function addOrderProduct(OrderProducts $orderProduct): static
    {
        if (!$this->orderProducts->contains($orderProduct)) {
            $this->orderProducts->add($orderProduct);
            $orderProduct->setAppOrder($this);
        }

        return $this;
    }

    public function removeOrderProduct(OrderProducts $orderProduct): static
    {
        if ($this->orderProducts->removeElement($orderProduct)) {
            // set the owning side to null (unless already changed)
            if ($orderProduct->getAppOrder() === $this) {
                $orderProduct->setAppOrder(null);
            }
        }

        return $this;
    }

}
