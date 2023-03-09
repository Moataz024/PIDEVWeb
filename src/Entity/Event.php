<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("events")]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message : 'Le nom est obligatoire')]
    #[Groups("events")]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message : 'La categorie est obligatoire')]
    #[Groups("events")]
    private ?string $category = null;

    #[ORM\ManyToOne(inversedBy: 'ownedEvents')]
    #[Groups("events")]
    private ?User $organisateur = null;


    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'inscriptions')]
    #[Groups("events")]
    private Collection $participants;


    #[ORM\ManyToMany(targetEntity: SponsorE::class, mappedBy: 'sponsoredEvents')]
    #[Groups("events")]
    private Collection $sponsors;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message : 'Le lieu est obligatoire')]
    #[Groups("events")]
    private ?string $lieu = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message : 'La description est obligatoire')]
    #[Groups("events")]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups("events")]
    private ?string $image = null;



    public function __construct()
    {
        $this->participants = new ArrayCollection();
        $this->sponsors = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getOrganisateur(): ?User
    {
        return $this->organisateur;
    }

    public function setOrganisateur(?User $organisateur): self
    {
        $this->organisateur = $organisateur;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(User $participant): self
    {
        if (!$this->participants->contains($participant)) {
            $this->participants->add($participant);
        }

        return $this;
    }

    public function removeParticipant(User $participant): self
    {
        $this->participants->removeElement($participant);

        return $this;
    }

    /**
     * @return Collection<int, SponsorE>
     */
    public function getSponsors(): Collection
    {
        return $this->sponsors;
    }

    public function addSponsor(SponsorE $sponsor): self
    {
        if (!$this->sponsors->contains($sponsor)) {
            $this->sponsors->add($sponsor);
            $sponsor->addSponsoredEvent($this);
        }

        return $this;
    }

    public function removeSponsor(SponsorE $sponsor): self
    {
        if ($this->sponsors->removeElement($sponsor)) {
            $sponsor->removeSponsoredEvent($this);
        }

        return $this;
    }
    public function __toString()
    {
        return $this->nom; // or any other string representation of the object
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /*public function addParticipant(User $participant): self
    {
        $this->participants[] = $participant;

        return $this;
    }*/

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }








}
