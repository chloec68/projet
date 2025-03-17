<?php

namespace App\Entity;

use App\Repository\NewsletterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NewsletterRepository::class)]
class Newsletter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $newsletterContent = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $newsletterDate = null;

    public function __construct()
    {
        $this->recipients = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNewsletterContent(): ?string
    {
        return $this->newsletterContent;
    }

    public function setNewsletterContent(string $newsletterContent): static
    {
        $this->newsletterContent = $newsletterContent;

        return $this;
    }

    public function getNewsletterDate(): ?\DateTimeInterface
    {
        return $this->newsletterDate;
    }

    public function setNewsletterDate(\DateTimeInterface $newsletterDate): static
    {
        $this->newsletterDate = $newsletterDate;

        return $this;
    }
}
