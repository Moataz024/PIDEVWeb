<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Nullable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
// #[Vich\Uploadable]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("users")]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\Email]
    #[ORM\Column(length: 180, unique: true)]
    #[Groups("users")]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\NotCompromisedPassword]
    private ?string $password = null;

    #[ORM\OneToMany(mappedBy: 'organisateur', targetEntity: Event::class)]
    private Collection $ownedEvents;

    #[ORM\ManyToMany(targetEntity: Event::class, mappedBy: 'participants')]
    private Collection $inscriptions;

    #[ORM\Column(length: 255)]
    #[Groups("users")]
    private ?string $nomutilisateur = null;

    #[ORM\Column(length: 255)]
    #[Groups("users")]
    private ?string $phone = null;


    #[ORM\Column(length : 255, nullable : true)]
    private ?string $avatarName = null;



    // #[Vich\UploadableField(mapping : 'user_avatar',fileNameProperty : 'avatarname')]
    // private $avatarFile;

    #[ORM\Column(length: 255)]
    #[Groups("users")]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    #[Groups("users")]
    private ?string $lastname = null;

    #[ORM\Column]
    #[Groups("users")]
    private ?bool $status = null;

    #[ORM\OneToMany(mappedBy: 'User', targetEntity: Application::class)]
    private Collection $applications;


    public function __construct()
    {
        $this->ownedEvents = new ArrayCollection();
        $this->inscriptions = new ArrayCollection();
        $this->status = false;
        $this->applications = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getAvatarName()
    {
        return $this->avatarName;
    }

    /**
     * @param mixed $avatarName
     */
    public function setAvatarName($avatarName): void
    {
        $this->avatarName = $avatarName;
    }

    // /**
    //  * @return mixed
    //  */
    // public function getAvatarFile()
    // {
    //     return $this->avatarFile;
    // }

    // /**
    //  * @param mixed $avatarFile
    //  */
    // public function setAvatarFile($avatarFile): void
    // {
    //     $this->avatarFile = $avatarFile;
    // }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

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
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
    

    public function __toString()
    {
        return $this->email; // or any other string representation of the object
    }

    /**
     * @return Collection<int, Event>
     */
    public function getOwnedEvents(): Collection
    {
        return $this->ownedEvents;
    }

    public function addOwnedEvent(Event $ownedEvent): self
    {
        if (!$this->ownedEvents->contains($ownedEvent)) {
            $this->ownedEvents->add($ownedEvent);
            $ownedEvent->setOrganisateur($this);
        }

        return $this;
    }

    public function removeOwnedEvent(Event $ownedEvent): self
    {
        if ($this->ownedEvents->removeElement($ownedEvent)) {
            // set the owning side to null (unless already changed)
            if ($ownedEvent->getOrganisateur() === $this) {
                $ownedEvent->setOrganisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getInscriptions(): Collection
    {
        return $this->inscriptions;
    }

    public function addInscription(Event $inscription): self
    {
        if (!$this->inscriptions->contains($inscription)) {
            $this->inscriptions->add($inscription);
            $inscription->addParticipant($this);
        }

        return $this;
    }

    public function removeInscription(Event $inscription): self
    {
        if ($this->inscriptions->removeElement($inscription)) {
            $inscription->removeParticipant($this);
        }

        return $this;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNomutilisateur(): ?string
    {
        return $this->nomutilisateur;
    }

    /**
     * @param string|null $nomutilisateur
     */
    public function setNomutilisateur(?string $nomutilisateur): void
    {
        $this->nomutilisateur = $nomutilisateur;
    }



    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }
    // public function __sleep()
    // {
    //     return ['id', 'email', 'roles', 'password', 'avatarName', 'nomutilisateur', 'phone' , 'firstname' , 'lastname' , 'status'];
    // }
    // // public function __sleep()
    // // {
    // //     return ['id'];
    // // }

    // public function __wakeup()
    // {
    //     if ($this->avatarName) {
    //         $this->avatarFile = new File($this->getAvatarPath());
    //     }
    // }
    private function getAvatarPath(): string
    {
        return sprintf('%s/%s', $this->getUploadDir(), $this->avatarName);
    }

    private function getUploadDir(): string
    {
        return 'images/users';
    }

    /**
     * @return Collection<int, Application>
     */
    public function getApplications(): Collection
    {
        return $this->applications;
    }

    public function addApplication(Application $application): self
    {
        if (!$this->applications->contains($application)) {
            $this->applications->add($application);
            $application->setUser($this);
        }

        return $this;
    }

    public function removeApplication(Application $application): self
    {
        if ($this->applications->removeElement($application)) {
            // set the owning side to null (unless already changed)
            if ($application->getUser() === $this) {
                $application->setUser(null);
            }
        }

        return $this;
    }
   



}
