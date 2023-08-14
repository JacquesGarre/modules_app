<?php

namespace App\Entity;

use App\Repository\ListingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ListingRepository::class)]
class Listing
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    #[ORM\Column(length: 255)]
    private ?string $value = null;

    #[ORM\Column(length: 255)]
    private ?string $colorClass = null;

    #[ORM\Column(length: 255)]
    private ?string $bgClass = null;

    #[ORM\Column(length: 255)]
    private ?string $list = null;

    public function __construct()
    {

    }

    public function __toString(): string
    {
        return $this->getLabel().' ('.$this->getList().')';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getColorClass(): ?string
    {
        return $this->colorClass;
    }

    public function setColorClass(string $colorClass): static
    {
        $this->colorClass = $colorClass;

        return $this;
    }

    public function getBgClass(): ?string
    {
        return $this->bgClass;
    }

    public function setBgClass(string $bgClass): static
    {
        $this->bgClass = $bgClass;

        return $this;
    }

    public function getList(): ?string
    {
        return $this->list;
    }

    public function setList(string $list): static
    {
        $this->list = $list;

        return $this;
    }

}
