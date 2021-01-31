<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 */
class User implements UserInterface
{
    /**
     * User id
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @var integer
     */
    private $id;

    /**
     * User firstname
     *
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $firstname;

    /**
     * User lastname
     *
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $lastname;

    /**
     * User email
     *
     * @ORM\Column(type="string", length=180, unique=true)
     *
     * @var string
     */
    private $email;

    /**
     * User roles
     *
     * @ORM\Column(type="json")
     *
     * @var array
     */
    private $roles = [];

    /**
     * User password
     *
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * User photos (join)
     *
     * @ORM\OneToMany(targetEntity=Photo::class, mappedBy="user")
     */
    private $photos;

    public function __construct()
    {
        $this->photos = new ArrayCollection();
    }

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
     * Get firstname
     *
     * @return string
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     */
    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     */
    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Set email
     *
     * @param string $email
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * Get roles
     *
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * Set roles
     *
     * @param string $roles
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get password
     *
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    /**
     * Set password
     *
     * @param string $password
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get salt
     *
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * Get all photos
     *
     * @return Collection|Photo[]
     */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    /**
     * Add photo
     *
     * @param Photo $photo
     */
    public function addPhoto(Photo $photo): self
    {
        if (!$this->photos->contains($photo)) {
            $this->photos[] = $photo;
            $photo->setUser($this);
        }

        return $this;
    }

    /**
     * Remove photo
     *
     * @param Photo $photo
     */
    public function removePhoto(Photo $photo): self
    {
        if ($this->photos->removeElement($photo)) {
            // set the owning side to null (unless already changed)
            if ($photo->getUser() === $this) {
                $photo->setUser(null);
            }
        }

        return $this;
    }
}
