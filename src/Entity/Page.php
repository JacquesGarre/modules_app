<?php

namespace App\Entity;

use App\Repository\PageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PageRepository::class)]
class Page
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $uri = null;

    #[ORM\OneToMany(mappedBy: 'Page', targetEntity: HtmlElement::class, orphanRemoval: true)]
    private Collection $htmlElements;

    #[ORM\OneToOne(inversedBy: 'page', cascade: ['persist', 'remove'])]
    private ?Module $module = null;

    #[ORM\ManyToOne(inversedBy: 'pages')]
    private ?Layout $layout = null;

    public function __construct()
    {
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

    public function getUri(): ?string
    {
        return $this->uri;
    }

    public function setUri(string $uri): static
    {
        $this->uri = $uri;

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
            $htmlElement->setPage($this);
        }

        return $this;
    }

    public function removeHtmlElement(HtmlElement $htmlElement): static
    {
        if ($this->htmlElements->removeElement($htmlElement)) {
            // set the owning side to null (unless already changed)
            if ($htmlElement->getPage() === $this) {
                $htmlElement->setPage(null);
            }
        }

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
