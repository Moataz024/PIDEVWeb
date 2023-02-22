<?php

namespace App\Entity;

use App\Repository\SponsorERepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: SponsorERepository::class)]
class SponsorE
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message : 'Nom sponsor est obligatoire')]
    #[ORM\Column(length: 255)]
    private ?string $nomSponsor = null;

    #[Assert\Email(message : 'Veuillez indiquer un email valide')]
    #[Assert\NotBlank(message : 'Email est obligatoire')]
    #[ORM\Column(length: 255)]
    private ?string $emailSponsor = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message : 'Telephone est obligatoire')]
    #[Assert\Positive(message : 'Ce numéro n\'est pas valide')]
    private ?string $telSponsor = null;

    #[ORM\ManyToMany(targetEntity: Event::class, inversedBy: 'sponsors')]
    private Collection $sponsoredEvents;

    public function __construct()
    {
        $this->sponsoredEvents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomSponsor(): ?string
    {
        return $this->nomSponsor;
    }

    public function setNomSponsor(string $nomSponsor): self
    {
        $this->nomSponsor = $nomSponsor;

        return $this;
    }

    public function getEmailSponsor(): ?string
    {
        return $this->emailSponsor;
    }

    public function setEmailSponsor(string $emailSponsor): self
    {
        $this->emailSponsor = $emailSponsor;

        return $this;
    }

    public function getTelSponsor(): ?string
    {
        return $this->telSponsor;
    }

    public function setTelSponsor(string $telSponsor): self
    {
        $this->telSponsor = $telSponsor;

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getSponsoredEvents(): Collection
    {
        return $this->sponsoredEvents;
    }

    public function addSponsoredEvent(Event $sponsoredEvent): self
    {
        if (!$this->sponsoredEvents->contains($sponsoredEvent)) {
            $this->sponsoredEvents->add($sponsoredEvent);
        }

        return $this;
    }

    public function removeSponsoredEvent(Event $sponsoredEvent): self
    {
        $this->sponsoredEvents->removeElement($sponsoredEvent);

        return $this;
    }

    public function __toString()
    {
        return $this->nomSponsor; // or any other string representation of the object
    }
}
