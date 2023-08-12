<?php

namespace App\Entity;

use App\Repository\FormRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Doctrine\FormListener;
#[ORM\Entity(repositoryClass: FormRepository::class)]
#[ORM\EntityListeners([FormListener::class])]
class Form
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $action = null;

    #[ORM\ManyToMany(targetEntity: Field::class, inversedBy: 'forms')]
    private Collection $fields;

    #[ORM\ManyToOne(inversedBy: 'forms')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Module $module = null;

    #[ORM\OneToMany(mappedBy: 'ModuleForm', targetEntity: HtmlElement::class, orphanRemoval: true)]
    private Collection $htmlElements;

    private $html;

    public function __construct()
    {
        $this->fields = new ArrayCollection();
        $this->htmlElements = new ArrayCollection();
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

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(string $action): static
    {
        $this->action = $action;

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
        }

        return $this;
    }

    public function removeField(Field $field): static
    {
        $this->fields->removeElement($field);

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
            $htmlElement->setModuleForm($this);
        }

        return $this;
    }

    public function removeHtmlElement(HtmlElement $htmlElement): static
    {
        if ($this->htmlElements->removeElement($htmlElement)) {
            // set the owning side to null (unless already changed)
            if ($htmlElement->getModuleForm() === $this) {
                $htmlElement->setModuleForm(null);
            }
        }

        return $this;
    }

    public function setHtml($html)
    {
        $this->html = $html;
    }

    public function getHtml()
    {
        return $this->html;
    }

}
