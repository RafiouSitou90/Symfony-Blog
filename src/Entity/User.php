<?php

namespace App\Entity;

use App\Entity\Traits\Timestamps;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="tab_users")
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 * @UniqueEntity(fields={"username"}, message="There is already an account with this username")
 *
 * @ORM\HasLifecycleCallbacks()
 * @Vich\Uploadable()
 */
class User implements UserInterface
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
     * @var string
     */
    private string $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank(message="The username should not be blank")
     * @Assert\NotNull(message="The username should not be null.")
     * @Assert\Length(
     *      min = 8,
     *      max = 180,
     *      minMessage = "Your username must be at least {{ limit }} characters long.",
     *      maxMessage = "Your username cannot be longer than {{ limit }} characters.",
     *      allowEmptyString = false
     * )
     * @var string
     */
    private string $username = '';

    /**
     * @ORM\Column(type="json")
     * @var array
     */
    private array $roles = [];

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private string $password;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank(message="The email should not be blank.")
     * @Assert\NotNull(message="The email should not be null.")
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email."
     * )
     * @var string
     */
    private string $email = '';

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="The fullname should not be blank")
     * @Assert\NotNull(message="The fullname should not be null.")
     * @Assert\Length(
     *      min = 5,
     *      max = 180,
     *      minMessage = "Your fullname must be at least {{ limit }} characters long.",
     *      maxMessage = "Your fullname cannot be longer than {{ limit }} characters.",
     *      allowEmptyString = false
     * )
     * @var string
     */
    private string $fullName = '';

    /**
     * @ORM\OneToMany(targetEntity=Articles::class, mappedBy="author")
     * @var Articles[]|ArrayCollection
     */
    private $articles;

    /**
     * @ORM\OneToMany(targetEntity=Comments::class, mappedBy="author")
     * @var Comments[]|ArrayCollection
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity=Ratings::class, mappedBy="user")
     * @var Ratings[]|ArrayCollection
     */
    private $ratings;

    /**
     * @ORM\OneToMany(targetEntity=CommentsResponses::class, mappedBy="author")
     * @var CommentsResponses[]|ArrayCollection
     */
    private $commentResponses;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private ?string $token = null;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private bool $isActive = false;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private bool $isDeleted = false;

    /**
     * @ORM\OneToOne(targetEntity=Profile::class, cascade={"persist", "remove"}, orphanRemoval=true)
     * @var Profile|null
     */
    private ?Profile $profile = null;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->articles = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->ratings = new ArrayCollection();
        $this->commentResponses = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    /**
     * @param string $username
     *
     * @return $this
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     *
     * @return array
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param array $roles
     *
     * @return $this
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     *
     * @return string
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    /**
     * @param string $password
     *
     * @return $this
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     * @return null
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
        return null;
    }

    /**
     * @see UserInterface
     * @return void
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return $this->fullName;
    }

    /**
     * @param string $fullName
     *
     * @return $this
     */
    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

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
            $article->setAuthor($this);
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
            if ($article->getAuthor() === $this) {
                $article->setAuthor(null);
            }
        }

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
            $comment->setAuthor($this);
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
            if ($comment->getAuthor() === $this) {
                $comment->setAuthor(null);
            }
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
            $rating->setUser($this);
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
            if ($rating->getUser() === $this) {
                $rating->setUser(null);
            }
        }

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
            $commentResponse->setAuthor($this);
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
            if ($commentResponse->getAuthor() === $this) {
                $commentResponse->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @param string|null $token
     *
     * @return $this
     */
    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @ORM\PrePersist()
     *
     * @return void
     * @throws Exception
     */
    public function generateToken()
    {
        $this->token = self::generateActivationToken();
    }

    /**
     * @param bool $isActive
     *
     * @return $this
     */
    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsDeleted(): bool
    {
        return $this->isDeleted;
    }

    /**
     * @param bool $isDeleted
     *
     * @return $this
     */
    public function setIsDeleted(bool $isDeleted): self
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    /**
     * @return string
     * @throws Exception
     */
    public static function generateActivationToken ()
    {
        return urlencode(rtrim(strtr(base64_encode(random_bytes(64)), '+/', '-_'), '='));
    }

    public function getProfile(): ?Profile
    {
        return $this->profile;
    }

    public function setProfile(?Profile $profile): self
    {
        $this->profile = $profile;

        return $this;
    }
}
