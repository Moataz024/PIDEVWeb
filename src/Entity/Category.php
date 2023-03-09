<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    /**
     * @Groups({"category"})
    */
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    /**
     * @Groups({"category"})
    */
    private ?string $nom = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Equipment::class)]
    private Collection $equipment;

    #[ORM\Column(length: 255)]
    private ?string $imageC = null;

    public function __construct()
    {
        $this->equipment = new ArrayCollection();
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

    /**
     * @return Collection<int, Equipment>
     */
    public function getEquipment(): Collection
    {
        return $this->equipment;
    }

    public function addEquipment(Equipment $equipment): self
    {
        if (!$this->equipment->contains($equipment)) {
            $this->equipment->add($equipment);
            $equipment->setCategory($this);
        }

        return $this;
    }

    public function removeEquipment(Equipment $equipment): self
    {
        if ($this->equipment->removeElement($equipment)) {
            // set the owning side to null (unless already changed)
            if ($equipment->getCategory() === $this) {
                $equipment->setCategory(null);
            }
        }

        return $this;
    }

    public function getImageC(): ?string
    {
        return $this->imageC;
    }

    public function setImageC(string $imageC): self
    {
        $this->imageC = $imageC;

        return $this;
    }
}
