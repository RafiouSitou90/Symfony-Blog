<?php

namespace App\Entity;

use App\Entity\Traits\Timestamps;
use App\Repository\CommentsResponsesRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommentsResponsesRepository::class)
 * @ORM\Table(name="tab_comments_responses")
 *
 * @ORM\HasLifecycleCallbacks()
 */
class CommentsResponses
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
     * @ORM\ManyToOne(targetEntity=Comments::class, inversedBy="commentResponses")
     * @var Comments|null
     */
    private ?Comments $comment = null;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="commentResponses")
     * @var User|null
     */
    private ?User $user = null;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTimeInterface
     */
    private DateTimeInterface $publishedAt;

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
     * @return Comments|null
     */
    public function getComment(): ?Comments
    {
        return $this->comment;
    }

    /**
     * @param Comments|null $comment
     *
     * @return $this
     */
    public function setComment(?Comments $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     *
     * @return $this
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;

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
}
