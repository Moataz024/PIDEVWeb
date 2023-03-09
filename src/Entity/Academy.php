<?php

namespace App\Entity;

use App\Repository\AcademyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AcademyRepository::class)]
class Academy
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;
        
    #[ORM\Column(nullable : true)]
    private ?string $createdBy = null;

    #[ORM\Column(length: 255)]
    private ?string $category = null;

    #[ORM\OneToMany(mappedBy: 'academy', targetEntity: Coach::class)]
    private Collection $coaches;

    // #[ORM\Column(nullable : true)]
    // private ?int $age = null;

    // #[ORM\Column(nullable: true)]
    // private ?int $telephone = null;

    #[ORM\OneToMany(mappedBy: 'academy', targetEntity: Application::class)]
    private Collection $applications;

    public function __construct()
    {
        $this->coaches = new ArrayCollection();
        $this->applications = new ArrayCollection();
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
     * @return Collection<int, Coach>
     */
    public function getCoaches(): Collection
    {
        return $this->coaches;
    }

    public function addCoach(Coach $coach): self
    {
        if (!$this->coaches->contains($coach)) {
            $this->coaches->add($coach);
            $coach->setAcademy($this);
        }

        return $this;
    }

    public function removeCoach(Coach $coach): self
    {
        if ($this->coaches->removeElement($coach)) {
            // set the owning side to null (unless already changed)
            if ($coach->getAcademy() === $this) {
                $coach->setAcademy(null);
            }
        }

        return $this;
    }
   

    // public function getAge(): ?int
    // {
    //     return $this->age;
    // }

    // public function setAge(int $age): self
    // {
    //     $this->age = $age;

    //     return $this;
    // }
    public function __toString() {
        return $this->name;
    }

    // public function getTelephone(): ?int
    // {
    //     return $this->telephone;
    // }

    // public function setTelephone(?int $telephone): self
    // {
    //     $this->telephone = $telephone;

    //     return $this;
    // }

    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    public function setCreatedBy(string $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
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
            $application->setAcademy($this);
        }

        return $this;
    }

    public function removeApplication(Application $application): self
    {
        if ($this->applications->removeElement($application)) {
            // set the owning side to null (unless already changed)
            if ($application->getAcademy() === $this) {
                $application->setAcademy(null);
            }
        }

        return $this;
    }
    public function __sleep()
    {
        return ['id'];
    }
    
}
