<?php

namespace App\Entity;

use App\Entity\Traits\Timestamps;
use App\Repository\CategoriesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=CategoriesRepository::class)
 * @ORM\Table(name="tab_categories")
 *
 * @ORM\HasLifecycleCallbacks()
 */
class Categories
{
    use Timestamps;

    /**
     * @var string
     */
    private const DEFAULT_TIMEZONE = 'America/Sao_Paulo';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     * @var string|null
     */
    private ?string $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private ?string $name = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Slug(fields={"name"})
     * @var string
     */
    private string $slug;

    /**
     * @ORM\OneToMany(targetEntity=Articles::class, mappedBy="category")
     * @var Articles[]|ArrayCollection
     */
    private ArrayCollection $articles;

    /**
     * Categories constructor.
     */
    public function __construct()
    {
        $this->articles = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     *
     * @return $this
     */
    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection|Articles[]
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    /**
     * @param Articles $article
     *
     * @return $this
     */
    public function addArticle(Articles $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
            $article->setCategory($this);
        }

        return $this;
    }

    /**
     * @param Articles $article
     *
     * @return $this
     */
    public function removeArticle(Articles $article): self
    {
        if ($this->articles->contains($article)) {
            $this->articles->removeElement($article);
            // set the owning side to null (unless already changed)
            if ($article->getCategory() === $this) {
                $article->setCategory(null);
            }
        }

        return $this;
    }
}
