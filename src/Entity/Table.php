<?php

namespace App\Entity;

use App\Repository\TableRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Doctrine\TableListener;
use App\Validator\TableCanBeEditable;
use App\Validator\TableCanBeViewable;
use App\Validator\TableHasOneColumn;
use App\Validator\TableCanHaveAddAction;

#[ORM\Entity(repositoryClass: TableRepository::class)]
#[ORM\EntityListeners([TableListener::class])]
#[ORM\Table(name: '`table`')]
#[TableCanBeViewable]
#[TableCanBeEditable]
#[TableCanHaveAddAction]
#[TableHasOneColumn]
class Table
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\ManyToMany(targetEntity: Field::class, inversedBy: 'tables')]
    private Collection $columns;

    #[ORM\ManyToOne(inversedBy: 'tables')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Module $module = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $inlineActions = null;

    #[ORM\OneToMany(mappedBy: 'ModuleTable', targetEntity: HtmlElement::class, orphanRemoval: true)]
    private Collection $htmlElements;

    private $data = [];

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $defaultLimit = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $actions = null;

    private $total = 0;
    private $pages = 0;
    private $currentLimit = 10;
    private $currentPage = 1;

    #[ORM\ManyToMany(targetEntity: Field::class)]
    #[ORM\JoinTable(name: "table_filters")]
    private Collection $filters;


    public function __construct()
    {
        $this->columns = new ArrayCollection();
        $this->htmlElements = new ArrayCollection();
        $this->filters = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->title;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection<int, Field>
     */
    public function getColumns(): Collection
    {
        return $this->columns;
    }

    public function getColumnsNames(): array
    {
        return array_map(function($column){
            return $column->getName();
        }, $this->getColumns()->toArray());
    }



    public function addColumn(Field $column): static
    {
        if (!$this->columns->contains($column)) {
            $this->columns->add($column);
        }

        return $this;
    }

    public function removeColumn(Field $column): static
    {
        $this->columns->removeElement($column);

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

    public function getInlineActions(): ?array
    {
        return json_decode($this->inlineActions);
    }

    public function setInlineActions(?array $inlineActions): static
    {
        $this->inlineActions = json_encode($inlineActions);
        return $this;
    }

    /**
     * @return Collection<int, HtmlElement>
     */
    public function getHtmlElements(): Collection
    {
        return $this->htmlElements;
    }

    public function addHtmlElement(HtmlElement $htmlElement): static
    {
        if (!$this->htmlElements->contains($htmlElement)) {
            $this->htmlElements->add($htmlElement);
            $htmlElement->setModuleTable($this);
        }

        return $this;
    }

    public function removeHtmlElement(HtmlElement $htmlElement): static
    {
        if ($this->htmlElements->removeElement($htmlElement)) {
            // set the owning side to null (unless already changed)
            if ($htmlElement->getModuleTable() === $this) {
                $htmlElement->setModuleTable(null);
            }
        }

        return $this;
    }

    public function setData(array $data)
    {
        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getDefaultLimit(): ?string
    {
        return $this->defaultLimit;
    }

    public function setDefaultLimit(?string $defaultLimit): static
    {
        $this->defaultLimit = $defaultLimit;

        return $this;
    }

    public function getActions(): ?array
    {
        return json_decode($this->actions);
    }

    public function setActions(?array  $actions): static
    {
        $this->actions = json_encode($actions);
        return $this;
    }

    public function setTotal(int $total)
    {
        $this->total = $total;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function setPages(int $pages)
    {
        $this->pages = $pages;
    }

    public function getPages(): int
    {
        return $this->pages;
    }

    
    public function setCurrentLimit(int $currentLimit)
    {
        $this->currentLimit = $currentLimit;
    }

    public function getCurrentLimit(): int
    {
        return $this->currentLimit;
    }

    public function setCurrentPage(int $currentPage)
    {
        $this->currentPage = $currentPage;
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * @return Collection<int, Field>
     */
    public function getFilters(): Collection
    {
        return $this->filters;
    }

    public function addFilter(Field $filter): static
    {
        if (!$this->filters->contains($filter)) {
            $this->filters->add($filter);
        }

        return $this;
    }

    public function removeFilter(Field $filter): static
    {
        $this->filters->removeElement($filter);

        return $this;
    }



}
