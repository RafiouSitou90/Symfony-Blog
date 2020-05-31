<?php

namespace App\Entity;

use App\Entity\Traits\Timestamps;
use App\Repository\TagsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TagsRepository::class)
 * @ORM\Table(name="tab_tags")
 *
 * @ORM\HasLifecycleCallbacks()
 */
class Tags
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
}
