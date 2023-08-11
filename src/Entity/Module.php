<?php

namespace App\Entity;

use App\Repository\ModuleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ModuleRepository::class)]
class Module
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $labelPlural = null;

    #[ORM\Column(length: 255)]
    private ?string $labelSingular = null;

    #[ORM\Column(length: 255)]
    private ?string $sqlTable = null;

    #[ORM\OneToMany(mappedBy: 'module', targetEntity: Field::class, orphanRemoval: true)]
    private Collection $fields;

    public function __construct()
    {
        $this->fields = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabelPlural(): ?string
    {
        return $this->labelPlural;
    }

    public function setLabelPlural(string $labelPlural): static
    {
        $this->labelPlural = $labelPlural;

        return $this;
    }

    public function getLabelSingular(): ?string
    {
        return $this->labelSingular;
    }

    public function setLabelSingular(string $labelSingular): static
    {
        $this->labelSingular = $labelSingular;

        return $this;
    }

    public function getSqlTable(): ?string
    {
        return $this->sqlTable;
    }

    public function setSqlTable(string $sqlTable): static
    {
        $this->sqlTable = $sqlTable;

        return $this;
    }

    /**
     * @return Collection<int, Field>
     */
    public function getFields(): Collection
    {
        return $this->fields;
    }

    public function addField(Field $field): static
    {
        if (!$this->fields->contains($field)) {
            $this->fields->add($field);
            $field->setModule($this);
        }

        return $this;
    }

    public function removeField(Field $field): static
    {
        if ($this->fields->removeElement($field)) {
            // set the owning side to null (unless already changed)
            if ($field->getModule() === $this) {
                $field->setModule(null);
            }
        }

        return $this;
    }
}
