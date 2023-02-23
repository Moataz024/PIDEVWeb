<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\DateTime;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
   // #[Assert\DateTime()]
    #[Assert\NotNull(message:'The date reservation cannot be null.')]
    #[Assert\GreaterThanOrEqual('now', message:'The date reservation cannot be in the past.')]
    private ?\DateTimeInterface $dateReservation = null;

    
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    //#[Assert\DateTime()]
    #[Assert\NotNull(message:'The start time cannot be null.')]
    #[Assert\GreaterThanOrEqual('now', message:'The date cannot be in the past.')]
    //#[Assert\Time()]
    private ?\DateTimeInterface $startTime = null;

   
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    //#[Assert\DateTime()]
    #[Assert\NotNull(message:'The End time cannot be null.')]
    #[Assert\GreaterThanOrEqual('now', message:'The date cannot be in the past.')]
    //#[Assert\Time()]
    private ?\DateTimeInterface $endTime = null;

    #[ORM\Column(nullable: true)]
    private ?bool $resStatus = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    private ?Terrain $terrain = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $client = null;

    #[ORM\OneToMany(mappedBy: 'reservation', targetEntity: Equipment::class)]
    private Collection $equipments;

    #[ORM\Column]
    #[Assert\NotNull(message:'le nombre des personne a reserver est obligatoir')]
    #[Assert\Positive(message:'le nombre des personne a reserver doit etre Positif')]
    private ?int $nbPerson = null;

    public function __construct()
    {
        $this->equipments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateReservation(): ?\DateTimeInterface
    {
        return $this->dateReservation;
    }

    public function setDateReservation(\DateTimeInterface $dateReservation): self
    {
        $this->dateReservation = $dateReservation;

        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeInterface $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTimeInterface $endTime): self
    {
        $this->endTime = $endTime;

        return $this;
    }

    public function isResStatus(): ?bool
    {
        return $this->resStatus;
    }

    public function setResStatus(?bool $resStatus): self
    {
        $this->resStatus = $resStatus;

        return $this;
    }

    public function getTerrain(): ?Terrain
    {
        return $this->terrain;
    }

    public function setTerrain(?Terrain $terrain): self
    {
        $this->terrain = $terrain;

        return $this;
    }

    public function getClient(): ?User
    {
        return $this->client;
    }

    public function setClient(?User $client): self
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return Collection<int, Equipment>
     */
    public function getEquipments(): Collection
    {
        return $this->equipments;
    }

    public function addEquipment(Equipment $equipment): self
    {
        if (!$this->equipments->contains($equipment)) {
            $this->equipments->add($equipment);
            $equipment->setReservation($this);
        }

        return $this;
    }

    public function removeEquipment(Equipment $equipment): self
    {
        if ($this->equipments->removeElement($equipment)) {
            // set the owning side to null (unless already changed)
            if ($equipment->getReservation() === $this) {
                $equipment->setReservation(null);
            }
        }

        return $this;
    }

    public function getNbPerson(): ?int
    {
        return $this->nbPerson;
    }

    public function setNbPerson(int $nbPerson): self
    {
        $this->nbPerson = $nbPerson;

        return $this;
    }
}
