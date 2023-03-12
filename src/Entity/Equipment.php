<?php

namespace App\Entity;

use App\Repository\EquipmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: EquipmentRepository::class)]
class Equipment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    /**
     * @Groups({"equipments"})
    */
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    /**
     * @Groups({"equipments"})
    */
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    /**
     * @Groups({"equipments"})
    */
    private ?string $adress = null;

    #[ORM\Column(length: 255)]
    /**
     * @Groups({"equipments"})
    */
    private ?string $type = null;

    #[ORM\Column]
    /**
     * @Groups({"equipments"})
    */
    private ?int $quantity = null;

    #[ORM\ManyToOne(inversedBy: 'equipment')]
    /**
     * @Groups({"suppliers"})
    */
    private ?Suppliers $suppliers = null;

    #[ORM\Column(length: 255)]
    /**
     * @Groups({"equipments"})
    */
    private ?string $Price = null;

    #[ORM\ManyToOne(inversedBy: 'equipment')]
    /**
     * @Groups({"category"})
    */
    private ?Category $category = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    /**
     * @Groups({"equipments"})
    */
    private ?string $image = null;

    #[ORM\OneToMany(mappedBy: 'equipment', targetEntity: Rent::class)]
    private Collection $rents;

    #[ORM\OneToMany(mappedBy: 'equipment', targetEntity: Comment::class)]
    private Collection $Comment;

    #[ORM\Column(nullable: true)]
    private ?int $Likes = null;

    #[ORM\Column(nullable: true)]
    private ?int $Dislikes = null;

    #[ORM\ManyToMany(targetEntity: Reservation::class, mappedBy: 'equipments')]
    private Collection $reservations;


    public function __construct()
    {
        $this->reservations = new ArrayCollection();
        $this->rents = new ArrayCollection();
        $this->Comment = new ArrayCollection();
        $this->equipmentRatings = new ArrayCollection();
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

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(string $adress): self
    {
        $this->adress = $adress;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getSuppliers(): ?Suppliers
    {
        return $this->suppliers;
    }

    public function setSuppliers(?Suppliers $suppliers): self
    {
        $this->suppliers = $suppliers;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->Price;
    }

    public function setPrice(string $Price): self
    {
        $this->Price = $Price;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection<int, Rent>
     */
    public function getRents(): Collection
    {
        return $this->rents;
    }

    public function addRent(Rent $rent): self
    {
        if (!$this->rents->contains($rent)) {
            $this->rents->add($rent);
            $rent->setEquipment($this);
        }

        return $this;
    }

    public function removeRent(Rent $rent): self
    {
        if ($this->rents->removeElement($rent)) {
            // set the owning side to null (unless already changed)
            if ($rent->getEquipment() === $this) {
                $rent->setEquipment(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComment(): Collection
    {
        return $this->Comment;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->Comment->contains($comment)) {
            $this->Comment->add($comment);
            $comment->setEquipment($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->Comment->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getEquipment() === $this) {
                $comment->setEquipment(null);
            }
        }

        return $this;
    }
    public function __toString(){
      return $this->name;

    }

    public function getLikes(): ?int
    {
        return $this->Likes;
    }

    public function setLikes(?int $Likes): self
    {
        $this->Likes = $Likes;

        return $this;
    }

    public function getDislikes(): ?int
    {
        return $this->Dislikes;
    }

    public function setDislikes(?int $Dislikes): self
    {
        $this->Dislikes = $Dislikes;

        return $this;
    }
    /**
     * @ORM\Column(type="decimal", precision=2, scale=1, nullable=true)
     */
    private $averageRating;

    // #[ORM\OneToMany(mappedBy: 'relation', targetEntity: EquipmentRating::class)]
    // private Collection $equipmentRatings;

    // ...

    public function getAverageRating(): ?float
    {
        return $this->averageRating;
    }

    public function setAverageRating(?float $averageRating): self
    {
        $this->averageRating = $averageRating;

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
            $reservation->addEquipment($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            $reservation->removeEquipment($this);
        }

        return $this;
    }
    // /**
    //  * @return Collection<int, EquipmentRating>
    //  */
    // public function getEquipmentRatings(): Collection
    // {
    //     return $this->equipmentRatings;
    // }

    // public function addEquipmentRating(EquipmentRating $equipmentRating): self
    // {
    //     if (!$this->equipmentRatings->contains($equipmentRating)) {
    //         $this->equipmentRatings->add($equipmentRating);
    //         $equipmentRating->setRelation($this);
    //     }

    //     return $this;
    // }

    // public function removeEquipmentRating(EquipmentRating $equipmentRating): self
    // {
    //     if ($this->equipmentRatings->removeElement($equipmentRating)) {
    //         // set the owning side to null (unless already changed)
    //         if ($equipmentRating->getRelation() === $this) {
    //             $equipmentRating->setRelation(null);
    //         }
    //     }
 
    //     return $this;
    // }
}
