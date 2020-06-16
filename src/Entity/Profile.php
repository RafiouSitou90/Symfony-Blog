<?php

namespace App\Entity;

use App\Entity\Traits\Timestamps;
use App\Repository\ProfileRepository;
use Doctrine\ORM\Mapping as ORM;
use Serializable;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=ProfileRepository::class)
 * @ORM\Table(name="tab_user_profiles")
 *
 * @ORM\HasLifecycleCallbacks()
 * @Vich\Uploadable()
 */
class Profile implements Serializable
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

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * @Vich\UploadableField(mapping="users_images", fileNameProperty="avatarName", size="avatarSize")
     * @var File|null
     */
    private ?File $avatarFile = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string|null
     */
    private ?string $avatarName = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @var int|null
     */
    private ?int $avatarSize = null;


    /**
     * @param File|null $avatarFile
     *
     * @return $this
     */
    public function setAvatarFile(?File $avatarFile): self
    {
        $this->avatarFile = $avatarFile;

        return $this;
    }

    /**
     * @return null|File
     */
    public function getAvatarFile(): ?File
    {
        return $this->avatarFile;
    }

    /**
     * @return string|null
     */
    public function getAvatarName(): ?string
    {
        return $this->avatarName;
    }

    /**
     * @param string|null $avatarName
     *
     * @return $this
     */
    public function setAvatarName(?string $avatarName): self
    {
        $this->avatarName = $avatarName;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getAvatarSize(): ?int
    {
        return $this->avatarSize;
    }

    /**
     * @param int|null $avatarSize
     *
     * @return $this
     */
    public function setAvatarSize(?int $avatarSize): self
    {
        $this->avatarSize = $avatarSize;

        return $this;
    }

    /**
     * @return string
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->avatarName,
            $this->avatarSize
        ));
    }

    /**
     * @param string $serialized
     * @return void
     */
    public function unserialize ($serialized)
    {
        list (
            $this->id,
            $this->avatarName,
            $this->avatarSize
        ) = unserialize($serialized, array('allowed_classes' => false));
    }
}
