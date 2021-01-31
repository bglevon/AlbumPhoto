<?php

namespace App\Entity;

use App\Repository\PhotoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PhotoRepository::class)
 */
class Photo
{
    /**
     * Photo id
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @var integer
     */
    private $id;

    /**
     * Photo title
     *
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $title;

    /**
     * Photo slug by title
     *
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $slug;

    /**
     * Photo url (slug)
     *
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $url;

    /**
     * Join variable
     *
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="photos")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var User
     */
    private $user;

    /**
     * Variable for the file (form), which does not exist in the database
     */
    private $photo;

    /**
     * Full file name with extension
     *
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $filename;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Set title.
     *
     * @param string $title
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * Set slug
     *
     * @param string $slug
     */
    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * Set url
     *
     * @param string $url
     */
    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * Set user
     *
     * @param string $user
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get photo
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * Set photo
     *
     * @param $photo
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Get filename
     *
     * @return string
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * Set filename
     *
     * @param string $filename
     */
    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }
}
