<?php

namespace App\Entity;

use App\Repository\RentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
#[ORM\Entity(repositoryClass: RentRepository::class)]
class Rent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    /**
     * @Groups({"rents"})
    */
    private ?int $id = null;

    #[ORM\Column]
    /**
     * @Groups({"rents"})
    */
    private ?\DateTimeImmutable $rentAt = null;

    #[ORM\ManyToOne(inversedBy: 'rents')]
    private ?Equipment $equipment = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRentAt(): ?\DateTimeImmutable
    {
        return $this->rentAt;
    }

    public function setRentAt(\DateTimeImmutable $rentAt): self
    {
        $this->rentAt = $rentAt;

        return $this;
    }

    public function getEquipment(): ?Equipment
    {
        return $this->equipment;
    }

    public function setEquipment(?Equipment $equipment): self
    {
        $this->equipment = $equipment;

        return $this;
    }
}
