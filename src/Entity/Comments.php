<?php

namespace App\Entity;

use App\Entity\Traits\Timestamps;
use App\Repository\CommentsRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommentsRepository::class)
 * @ORM\Table(name="tab_comments")
 *
 * @ORM\HasLifecycleCallbacks()
 */
class Comments
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
     * @ORM\Column(type="text")
     * @var string
     */
    private ?string $content = null;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTimeInterface
     */
    private DateTimeInterface $publishedAt;

    /**
     * @ORM\ManyToOne(targetEntity=Articles::class, inversedBy="comments")
     * @var Articles|null
     */
    private ?Articles $article = null;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="comments")
     * @var User|null
     */
    private ?User $author = null;

    /**
     * @ORM\OneToMany(targetEntity=CommentsResponses::class, mappedBy="comment")
     * @var CommentsResponses[]|ArrayCollection
     */
    private ArrayCollection $commentResponses;

    /**
     * Comments constructor.
     */
    public function __construct()
    {
        $this->commentResponses = new ArrayCollection();
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
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param string $content
     *
     * @return $this
     */
    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getPublishedAt(): ?DateTimeInterface
    {
        return $this->publishedAt;
    }

    /**
     * @param DateTimeInterface $publishedAt
     *
     * @return $this
     */
    public function setPublishedAt(DateTimeInterface $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * @return Articles|null
     */
    public function getArticle(): ?Articles
    {
        return $this->article;
    }

    /**
     * @param Articles|null $article
     *
     * @return $this
     */
    public function setArticle(?Articles $article): self
    {
        $this->article = $article;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * @param User|null $author
     *
     * @return $this
     */
    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection|CommentsResponses[]
     */
    public function getCommentResponses(): Collection
    {
        return $this->commentResponses;
    }

    /**
     * @param CommentsResponses $commentResponse
     *
     * @return $this
     */
    public function addCommentResponse(CommentsResponses $commentResponse): self
    {
        if (!$this->commentResponses->contains($commentResponse)) {
            $this->commentResponses[] = $commentResponse;
            $commentResponse->setComment($this);
        }

        return $this;
    }

    /**
     * @param CommentsResponses $commentResponse
     *
     * @return $this
     */
    public function removeCommentResponse(CommentsResponses $commentResponse): self
    {
        if ($this->commentResponses->contains($commentResponse)) {
            $this->commentResponses->removeElement($commentResponse);
            // set the owning side to null (unless already changed)
            if ($commentResponse->getComment() === $this) {
                $commentResponse->setComment(null);
            }
        }

        return $this;
    }
}
