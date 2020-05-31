<?php

namespace App\Entity;

use App\Entity\Traits\Timestamps;
use App\Repository\ArticlesRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=ArticlesRepository::class)
 * @ORM\Table(name="tab_articles")
 * @UniqueEntity(fields={"slug"}, message="There is already an article with this title")
 *
 * @ORM\HasLifecycleCallbacks()
 * @Vich\Uploadable()
 */
class Articles
{
    use Timestamps;

    /**
     * @var int
     */
    public const NUM_ITEMS = 10;

    /**
     * @var string
     */
    public const DRAFT = 'draft';
    /**
     * @var string
     */
    public const PUBLISHED = 'published';
    /**
     * @var string
     */
    public const ARCHIVED = 'archived';

    /**
     * @var string
     */
    public const COMMENT_CLOSED = 'closed';
    /**
     * @var string
     */
    public const COMMENT_OPENED = 'opened';

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
     * @Assert\NotBlank(message="The title should not be blank")
     * @Assert\NotNull(message="The title should not be null.")
     * @Assert\Length(
     *      min = 8,
     *      max = 255,
     *      minMessage = "Your title must be at least {{ limit }} characters long.",
     *      maxMessage = "Your title cannot be longer than {{ limit }} characters.",
     *      allowEmptyString = false
     * )
     * @var string
     */
    private ?string $title = null;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Gedmo\Slug(fields={"title"})
     */
    private string $slug;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="The summary should not be blank")
     * @Assert\NotNull(message="The summary should not be null.")
     * @Assert\Length(
     *      min = 8,
     *      max = 255,
     *      minMessage = "Your summary must be at least {{ limit }} characters long.",
     *      maxMessage = "Your summary cannot be longer than {{ limit }} characters.",
     *      allowEmptyString = false
     * )
     * @var string
     */
    private ?string $summary = null;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="The content should not be blank")
     * @Assert\NotNull(message="The content should not be null.")
     * @Assert\Length(
     *      min = 10,
     *      minMessage = "Your content must be at least {{ limit }} characters long.",
     *      allowEmptyString = false
     * )
     * @var string
     */
    private ?string $content = null;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * @Vich\UploadableField(mapping="articles_images", fileNameProperty="imageName", size="imageSize")
     * @var File
     */
    private File $imageFile;

    /**
     * @ORM\Column(type="string", length=191, nullable=true)
     * @var string
     */
    private string $imageName;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    private int $imageSize = 0;

    /**
     * @ORM\ManyToOne(targetEntity=Categories::class, inversedBy="articles")
     * @var Categories|null
     */
    private ?Categories $category = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var DateTimeInterface|null
     */
    private ?DateTimeInterface $publishedAt = null;

    /**
     * @ORM\Column(type="string", length=20)
     * @var string
     */
    private string $articleStatus = self::DRAFT;

    /**
     * @ORM\Column(type="string", length=10)
     * @var string
     */
    private string $commentsStatus = self::COMMENT_OPENED;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="articles")
     * @var User|null
     */
    private ?User $author = null;

    /**
     * @ORM\OneToMany(targetEntity=Comments::class, mappedBy="article", orphanRemoval=true, cascade={"persist"})
     * @ORM\OrderBy({"publishedAt": "DESC"})
     *
     * @var Comments[]|ArrayCollection
     */
    private ArrayCollection $comments;

    /**
     * @ORM\ManyToMany(targetEntity=Tags::class, cascade={"persist"})
     * @ORM\JoinTable(name="tab_articles_tags")
     * @ORM\OrderBy({"name": "ASC"})
     * @var Tags[]|ArrayCollection
     */
    private ArrayCollection $tags;

    /**
     * @ORM\OneToMany(targetEntity=Ratings::class, mappedBy="article")
     * @var Ratings[]|ArrayCollection
     */
    private ArrayCollection $ratings;

    /**
     * Articles constructor.
     */
    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->ratings = new ArrayCollection();
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
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

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
     * @return string|null
     */
    public function getSummary(): ?string
    {
        return $this->summary;
    }

    /**
     * @param string $summary
     *
     * @return $this
     */
    public function setSummary(string $summary): self
    {
        $this->summary = $summary;

        return $this;
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
     * @return Categories|null
     */
    public function getCategory(): ?Categories
    {
        return $this->category;
    }

    /**
     * @param Categories|null $category
     *
     * @return $this
     */
    public function setCategory(?Categories $category): self
    {
        $this->category = $category;

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
     * @param DateTimeInterface|null $publishedAt
     *
     * @return $this
     */
    public function setPublishedAt(?DateTimeInterface $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getArticleStatus(): ?string
    {
        return $this->articleStatus;
    }

    /**
     * @param string $articleStatus
     *
     * @return $this
     */
    public function setArticleStatus(string $articleStatus): self
    {
        $this->articleStatus = $articleStatus;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCommentsStatus(): ?string
    {
        return $this->commentsStatus;
    }

    /**
     * @param string $commentsStatus
     *
     * @return $this
     */
    public function setCommentsStatus(string $commentsStatus): self
    {
        $this->commentsStatus = $commentsStatus;

        return $this;
    }


    /**
     * @param File $imageFile
     *
     * @return $this
     */
    public function setImageFile(File $imageFile): self
    {
        $this->imageFile = $imageFile;

        return $this;
    }

    /**
     * @return File
     */
    public function getImageFile(): File
    {
        return $this->imageFile;
    }

    /**
     * @return string|null
     */
    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    /**
     * @param string $imageName
     *
     * @return $this
     */
    public function setImageName(string $imageName): self
    {
        $this->imageName = $imageName;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }

    /**
     * @param int $imageSize
     *
     * @return $this
     */
    public function setImageSize(int $imageSize): self
    {
        $this->imageSize = $imageSize;

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
     * @return Collection|Comments[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    /**
     * @param Comments $comment
     *
     * @return $this
     */
    public function addComment(Comments $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setArticle($this);
        }

        return $this;
    }

    /**
     * @param Comments $comment
     *
     * @return $this
     */
    public function removeComment(Comments $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getArticle() === $this) {
                $comment->setArticle(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Tags[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    /**
     * @param Tags $tag
     *
     * @return $this
     */
    public function addTag(Tags $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    /**
     * @param Tags $tag
     *
     * @return $this
     */
    public function removeTag(Tags $tag): self
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
        }

        return $this;
    }

    /**
     * @return Collection|Ratings[]
     */
    public function getRatings(): Collection
    {
        return $this->ratings;
    }

    /**
     * @param Ratings $rating
     *
     * @return $this
     */
    public function addRating(Ratings $rating): self
    {
        if (!$this->ratings->contains($rating)) {
            $this->ratings[] = $rating;
            $rating->setArticle($this);
        }

        return $this;
    }

    /**
     * @param Ratings $rating
     *
     * @return $this
     */
    public function removeRating(Ratings $rating): self
    {
        if ($this->ratings->contains($rating)) {
            $this->ratings->removeElement($rating);
            // set the owning side to null (unless already changed)
            if ($rating->getArticle() === $this) {
                $rating->setArticle(null);
            }
        }

        return $this;
    }

    /**
     * @return string
     */
    public static function DRAFT ()
    {
        return self::DRAFT;
    }

    /**
     * @return string
     */
    public static function PUBLISHED ()
    {
        return self::PUBLISHED;
    }

    /**
     * @return string
     */
    public static function ARCHIVED ()
    {
        return self::ARCHIVED;
    }

    /**
     * @return string
     */
    public static function COMMENT_OPENED ()
    {
        return self::COMMENT_OPENED;
    }

    /**
     * @return string
     */
    public static function COMMENT_CLOSED ()
    {
        return self::COMMENT_CLOSED;
    }
}
