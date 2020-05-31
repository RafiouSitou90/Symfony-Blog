<?php

namespace App\Entity;

use App\Entity\Traits\Timestamps;
use App\Repository\RatingsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RatingsRepository::class)
 * @ORM\Table(name="tab_ratings")
 *
 * @ORM\HasLifecycleCallbacks()
 */
class Ratings
{
    use Timestamps;

    /**
     * @var string
     */
    private const LIKE = 'like';

    /**
     * @var string
     */
    private const DISLIKE = 'dislike';

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
     * @ORM\ManyToOne(targetEntity=Articles::class, inversedBy="ratings")
     * @var Articles|null
     */
    private ?Articles $article = null;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="ratings")
     * @var User|null
     */
    private ?User $user = null;

    /**
     * @ORM\Column(type="string", length=10)
     * @var string
     */
    private string $status;

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return Articles|null
     */
    public function getArticle(): ?Articles
    {
        return $this->article;
    }

    public function setArticle(?Articles $article): self
    {
        $this->article = $article;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string
     */
    public static function LIKE ()
    {
        return self::LIKE;
    }

    /**
     * @return string
     */
    public static function DISLIKE ()
    {
        return self::DISLIKE;
    }
}
