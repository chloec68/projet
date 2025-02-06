<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'products')]
    private Collection $users;

    /**
     * @var Collection<int, Order>
     */
    #[ORM\ManyToMany(targetEntity: Order::class, mappedBy: 'products')]
    private Collection $orders;

    #[ORM\ManyToOne(inversedBy: 'products')]
    private ?Type $type = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    private ?Size $size = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Vat $vat = null;

    /**
     * @var Collection<int, Picture>
     */
    #[ORM\OneToMany(targetEntity: Picture::class, mappedBy: 'product')]
    private Collection $pictures;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->orders = new ArrayCollection();
        $this->pictures = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addProduct($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            $user->removeProduct($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): static
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->addProduct($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): static
    {
        if ($this->orders->removeElement($order)) {
            $order->removeProduct($this);
        }

        return $this;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getSize(): ?Size
    {
        return $this->size;
    }

    public function setSize(?Size $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getVat(): ?Vat
    {
        return $this->vat;
    }

    public function setVat(?Vat $vat): static
    {
        $this->vat = $vat;

        return $this;
    }

    /**
     * @return Collection<int, Picture>
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    public function addPicture(Picture $picture): static
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures->add($picture);
            $picture->setProduct($this);
        }

        return $this;
    }

    public function removePicture(Picture $picture): static
    {
        if ($this->pictures->removeElement($picture)) {
            // set the owning side to null (unless already changed)
            if ($picture->getProduct() === $this) {
                $picture->setProduct(null);
            }
        }

        return $this;
    }
}
