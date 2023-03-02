<?php

namespace App\Entity;

use App\Repository\AcademyBackRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AcademyBackRepository::class)]
class AcademyBack
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $category = null;

    // #[ORM\Column(length: 255)]
    // private ?string $Coach = null;

    #[ORM\OneToMany(mappedBy: 'Academy', targetEntity: CoachBack::class)]
    private Collection $coachBacks;

    // #[ORM\Column(length: 255)]
    // private ?Academy $academyId = null;

    public function __construct()
    {
        $this->coachBacks = new ArrayCollection();
    }

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

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, CoachBack>
     */
    public function getCoachBacks(): Collection
    {
        return $this->coachBacks;
    }

    public function addCoachBack(CoachBack $coachBack): self
    {
        if (!$this->coachBacks->contains($coachBack)) {
            $this->coachBacks->add($coachBack);
            $coachBack->setAcademy($this);
        }

        return $this;
    }

    public function removeCoachBack(CoachBack $coachBack): self
    {
        if ($this->coachBacks->removeElement($coachBack)) {
            // set the owning side to null (unless already changed)
            if ($coachBack->getAcademy() === $this) {
                $coachBack->setAcademy(null);
            }
        }

        return $this;
    }
    // public function getAcademy(): ?Academy
    // {
    //     return $this->academyId;
    // }

    // public function setAcademy(?Academy $Academy): self
    // {
    //     $this->academyId = $Academy;

    //     return $this;
    // }
    public function __toString() {
        return $this->name;
    }
}
