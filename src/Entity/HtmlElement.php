<?php

namespace App\Entity;

use App\Repository\HtmlElementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HtmlElementRepository::class)]
class HtmlElement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'htmlElements')]
    private ?Page $Page = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
    private ?self $parent = null;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class, orphanRemoval: true)]
    private Collection $children;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sizeClass = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $additionnalClasses = null;

    #[ORM\ManyToOne(inversedBy: 'htmlElements')]
    private ?Table $ModuleTable = null;

    #[ORM\ManyToOne(inversedBy: 'htmlElements')]
    private ?Form $ModuleForm = null;

    #[ORM\ManyToOne(inversedBy: 'htmlElements')]
    private ?Layout $layout = null;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPage(): ?Page
    {
        return $this->Page;
    }

    public function setPage(?Page $Page): static
    {
        $this->Page = $Page;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(self $child): static
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(self $child): static
    {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

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

    public function getSizeClass(): ?string
    {
        return $this->sizeClass;
    }

    public function setSizeClass(?string $sizeClass): static
    {
        $this->sizeClass = $sizeClass;

        return $this;
    }

    public function getAdditionnalClasses(): ?string
    {
        return $this->additionnalClasses;
    }

    public function setAdditionnalClasses(?string $additionnalClasses): static
    {
        $this->additionnalClasses = $additionnalClasses;

        return $this;
    }

    public function getModuleTable(): ?Table
    {
        return $this->ModuleTable;
    }

    public function setModuleTable(?Table $ModuleTable): static
    {
        $this->ModuleTable = $ModuleTable;

        return $this;
    }

    public function getModuleForm(): ?Form
    {
        return $this->ModuleForm;
    }

    public function setModuleForm(?Form $ModuleForm): static
    {
        $this->ModuleForm = $ModuleForm;

        return $this;
    }

    public function getLayout(): ?Layout
    {
        return $this->layout;
    }

    public function setLayout(?Layout $layout): static
    {
        $this->layout = $layout;

        return $this;
    }
}
