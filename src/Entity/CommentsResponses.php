<?php

namespace App\Entity;

use App\Entity\Traits\Timestamps;
use App\Repository\CommentsResponsesRepository;
use DateTime;
use DateTimeInterface;
use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\NotBlank(message="The content should not be blank")
     * @Assert\NotNull(message="The content should not be null.")
     * @Assert\Length(
     *      min = 10,
     *      max = 10000,
     *      minMessage = "Your content must be at least {{ limit }} characters long.",
     *      maxMessage = "Your content cannot be longer than {{ limit }} characters.",
     *      allowEmptyString = false
     * )
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
    private ?User $author = null;

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
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * @param User|null $user
     *
     * @return $this
     */
    public function setAuthor(?User $user): self
    {
        $this->author = $user;

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
     * @ORM\PrePersist()
     * @return $this
     * @throws Exception
     */
    public function setPublishedAtValue(): self
    {
        $this->publishedAt = new DateTime('now', new DateTimeZone(self::DEFAULT_TIMEZONE));

        return $this;
    }
}
