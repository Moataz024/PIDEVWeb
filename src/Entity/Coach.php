<?php

namespace App\Entity;

use App\Repository\CoachRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CoachRepository::class)]
class Coach
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(nullable : true)]
    private ?string $createdBy = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $telephone = null;

    #[ORM\JoinColumn(onDelete:"SET NULL")]
    #[ORM\ManyToOne(inversedBy: 'coaches')]
    private ?Academy $academyId = null;

    // #[ORM\Column(length: 255)]
    // private ?string $sport = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
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

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getAcademy(): ?Academy
    {
        return $this->academyId;
    }

    public function setAcademy(?Academy $Academy): self
    {
        $this->academyId = $Academy;

        return $this;
    }
    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    public function setCreatedBy(string $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }
    // public function __toString() {
    //     return $this->name;
    // }

    // public function getSport(): ?string
    // {
    //     return $this->sport;
    // }

    // public function setSport(string $sport): self
    // {
    //     $this->sport = $sport;

    //     return $this;
    // }
}
