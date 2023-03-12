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
#[Vich\Uploadable]
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
    #[Assert\NotBlank]
    private ?string $nomutilisateur = null;

    #[ORM\Column(length: 255)]
    #[Groups("users")]
    #[Assert\NotBlank]
    private ?string $phone = null;


    #[ORM\Column(length : 255, nullable : true)]
    private $avatarName;


    #[Vich\UploadableField(mapping : "user_avatar",fileNameProperty : "avatarname")]
    private $avatarFile;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups("users")]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups("users")]
    private ?string $lastname = null;

    #[ORM\Column]
    #[Groups("users")]
    private ?bool $status = null;

    #[ORM\Column]
    private ?bool $isVerified = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $verifToken = null;

    #[ORM\OneToOne(mappedBy: 'user')]
    private ?Card $card = null;

    #[ORM\OneToMany(mappedBy: 'user',targetEntity: Commande::class)]
    private Collection $commandes;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Terrain::class, orphanRemoval: true)]
    private Collection $terrains;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Reservation::class, orphanRemoval: true)]
    private Collection $reservations;

    public function __construct()
    {
        $this->ownedEvents = new ArrayCollection();
        $this->inscriptions = new ArrayCollection();
        $this->terrains = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->status = false;
        $this->isVerified = false;
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

    /**
     * @return mixed
     */
    public function getAvatarFile()
    {
        return $this->avatarFile;
    }

    /**
     * @param mixed $avatarFile
     */
    public function setAvatarFile($avatarFile): void
    {
        $this->avatarFile = $avatarFile;
    }

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
    /*public function __sleep()
    {
        return ['id', 'email', 'roles', 'password', 'avatarName', 'nomutilisateur', 'phone' , 'firstname' , 'lastname' , 'status'];
    }

    public function __wakeup()
    {
        if ($this->avatarName) {
            $this->avatarFile = new File($this->getAvatarPath());
        }
    }*/
    private function getAvatarPath(): string
    {
        return sprintf('%s/%s', $this->getUploadDir(), $this->avatarName);
    }

    private function getUploadDir(): string
    {
        return 'images/users';
    }

    public function isIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getVerifToken(): ?string
    {
        return $this->verifToken;
    }

    public function setVerifToken(?string $verifToken): self
    {
        $this->verifToken = $verifToken;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }


    public function getCard() : ?Card{
        return $this->card;
    }

    public function setCard(?Card $card): self
    {
        if($card === null && $this->card !== null){
            $this->card->setUser(null);
        }

        if($card !== null && $card->getUser() !== $this){
            $card->setUser($this);
        }

        $this->card = $card;

        return $this;
    }


    /*
     * @return Collection<int,Commande>
     */
    public function getCommandes(): Collection{
        return $this->commandes;
    }


    public function addCommande(Commande $commande){
        if(!$this->commandes->contains($commande)){
            $this->commandes->add($commande);
        }
        return $this;
    }

    public function removeCommande(Commande $commande): self
    {
        if($this->commandes->removeElement($commande)){
            if($commande->getUser() === $this){
                $commande->setUser(null);
            }
        }
        return $this;
    }
    /**
     * @return Collection<int, Terrain>
     */
    public function getTerrains(): Collection
    {
        return $this->terrains;
    }

    public function addTerrain(Terrain $terrain): self
    {
        if (!$this->terrains->contains($terrain)) {
            $this->terrains->add($terrain);
            $terrain->setOwner($this);
        }

        return $this;
    }

    public function removeTerrain(Terrain $terrain): self
    {
        if ($this->terrains->removeElement($terrain)) {
            // set the owning side to null (unless already changed)
            if ($terrain->getOwner() === $this) {
                $terrain->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setClient($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getClient() === $this) {
                $reservation->setClient(null);
            }
        }

        return $this;
    }
}
