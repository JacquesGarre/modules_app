<?php

namespace App\Entity;

use App\Repository\FieldRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Doctrine\FieldListener;

#[ORM\Entity(repositoryClass: FieldRepository::class)]
#[ORM\EntityListeners([FieldListener::class])]
class Field
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column]
    private ?bool $required = null;

    #[ORM\Column]
    private ?bool $disabled = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $value = null;

    #[ORM\ManyToOne(inversedBy: 'fields')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Module $module = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $foreignTable = null;

    #[ORM\Column(nullable: true)]
    private ?bool $multiple = null;

    #[ORM\ManyToMany(targetEntity: Form::class, mappedBy: 'fields')]
    private Collection $forms;

    #[ORM\ManyToMany(targetEntity: Table::class, mappedBy: 'columns')]
    private Collection $tables;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $list = null;

    private array $choices = [];

    public function __construct()
    {
        $this->forms = new ArrayCollection();
        $this->tables = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getLabel();
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function isRequired(): ?bool
    {
        return $this->required;
    }

    public function setRequired(bool $required): static
    {
        $this->required = $required;

        return $this;
    }

    public function isDisabled(): ?bool
    {
        return $this->disabled;
    }

    public function setDisabled(bool $disabled): static
    {
        $this->disabled = $disabled;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getModule(): ?Module
    {
        return $this->module;
    }

    public function setModule(?Module $module): static
    {
        $this->module = $module;

        return $this;
    }

    public function getForeignTable(): ?string
    {
        return $this->foreignTable;
    }

    public function setForeignTable(?string $foreignTable): static
    {
        $this->foreignTable = $foreignTable;

        return $this;
    }

    public function isMultiple(): ?bool
    {
        return $this->multiple;
    }

    public function setMultiple(?bool $multiple): static
    {
        $this->multiple = $multiple;

        return $this;
    }

    /**
     * @return Collection<int, Form>
     */
    public function getForms(): Collection
    {
        return $this->forms;
    }

    public function addForm(Form $form): static
    {
        if (!$this->forms->contains($form)) {
            $this->forms->add($form);
            $form->addField($this);
        }

        return $this;
    }

    public function removeForm(Form $form): static
    {
        if ($this->forms->removeElement($form)) {
            $form->removeField($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Table>
     */
    public function getTables(): Collection
    {
        return $this->tables;
    }

    public function addTable(Table $table): static
    {
        if (!$this->tables->contains($table)) {
            $this->tables->add($table);
            $table->addColumn($this);
        }

        return $this;
    }

    public function removeTable(Table $table): static
    {
        if ($this->tables->removeElement($table)) {
            $table->removeColumn($this);
        }

        return $this;
    }

    public function getList(): ?string
    {
        return $this->list;
    }

    public function setList(?string $list): static
    {
        $this->list = $list;

        return $this;
    }

    public function getChoices(): array
    {
        return $this->choices;
    }

    public function setChoices(array $choices): static
    {
        $this->choices = $choices;

        return $this;
    }

}
