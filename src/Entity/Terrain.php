<?php

namespace App\Entity;

use App\Repository\TerrainRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: TerrainRepository::class)]
#[Vich\Uploadable]
class Terrain
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    
    #[ORM\Column(length: 60)]
    #[Assert\NotBlank(message:'Le Nom du Terrain est obligatoir')]
    #[Assert\Length(max:60,maxMessage:'Votre Nom du Terrain ne depasse pas 60 caractères.')]
    #[Assert\Length(min:3,minMessage:'Votre Nom du Terrain doit depasser 3 caractères.')]
    private ?string $name = null;

    
    #[ORM\Column]
    #[Assert\NotBlank(message:'La Capacity du Terrain est obligatoir')]
    private ?int $capacity = null;

    
    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message:'Sport Type du Terrain est obligatoir')]
    #[Assert\Length(max:50,maxMessage:'Votre Sport Type du Terrain ne depasse pas 50 caractères.')]
    #[Assert\Length(min:3,minMessage:'Votre Sport Type du Terrain doit depasser 3 caractères.')]
    private ?string $sportType = null;

   
    #[ORM\Column]
    #[Assert\NotNull(message:'Le Prix de Reservation du Terrain est obligatoir')]
    #[Assert\Positive(message:'Le Prix de Reservation doit etre Positif')]
    #[Assert\Range(min:0, max:7000, notInRangeMessage:'The rent price must be between {{ min }} and {{ max }} Dt.')]
    private ?float $rentPrice = null;
    
    #[ORM\Column(nullable: true)]
    private ?bool $disponibility = null;

  
    #[ORM\Column]
    #[Assert\NotNull(message:'Le Code Postale de la region est obligatoir')]
    #[Assert\Positive(message:'Le Code Postale doit etre Positif')]
    private ?int $postalCode = null;
    
    #[ORM\Column]
    #[Assert\NotNull(message:'le numero de la rue du Terrain est obligatoir')]
    #[Assert\Positive(message:'le numero de la rue doit etre Positif')]
    private ?int $roadNumber = null;
   
    #[ORM\Column(length: 70)]
    #[Assert\NotBlank(message:'La Ville du Terrain est obligatoir')]
    #[Assert\Length(max:70,maxMessage:'La Ville du Terrain ne depasse pas 70 caractères.')]
    #[Assert\Length(min:3,minMessage:'La Ville du Terrain doit depasser 3 caractères.')]
    private ?string $city = null;

    
    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message:'Le Pays du Terrain est obligatoir')]
    #[Assert\Length(max:50,maxMessage:'Le Pays du Terrain ne depasse pas  50 caractères.')]
    #[Assert\Length(min:3,minMessage:'Le Pays du Terrain doit depasser 3 caractères.')]
    private ?string $country = null;

    #[ORM\OneToMany(mappedBy: 'terrain', targetEntity: Reservation::class)]
    private Collection $reservations;

     // NOTE: This is not a mapped field of entity metadata, just a simple property.
     #[Vich\UploadableField(mapping: 'terrain_directory', fileNameProperty: 'imageName')]
     private ?File $imageFile = null;
 
     #[ORM\Column(nullable: true)]
     private ?string $imageName = null;
 
     #[ORM\Column(nullable: true)]
     private ?\DateTimeImmutable $updatedAt = null;
 
     /**
      * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
      * of 'UploadedFile' is injected into this setter to trigger the update. If this
      * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
      * must be able to accept an instance of 'File' as the bundle will inject one here
      * during Doctrine hydration.
      *
      * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
      */
     
    public function __construct()
    {
        $this->reservations = new ArrayCollection();
        $this->updatedAt = new \DateTimeImmutable();
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

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): self
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function getSportType(): ?string
    {
        return $this->sportType;
    }

    public function setSportType(string $sportType): self
    {
        $this->sportType = $sportType;

        return $this;
    }

    public function getRentPrice(): ?float
    {
        return $this->rentPrice;
    }

    public function setRentPrice(float $rentPrice): self
    {
        $this->rentPrice = $rentPrice;

        return $this;
    }

    public function isDisponibility(): ?bool
    {
        return $this->disponibility;
    }

    public function setDisponibility(?bool $disponibility): self
    {
        $this->disponibility = $disponibility;

        return $this;
    }

    public function getPostalCode(): ?int
    {
        return $this->postalCode;
    }

    public function setPostalCode(int $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getRoadNumber(): ?int
    {
        return $this->roadNumber;
    }

    public function setRoadNumber(int $roadNumber): self
    {
        $this->roadNumber = $roadNumber;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

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
            $reservation->setTerrain($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getTerrain() === $this) {
                $reservation->setTerrain(null);
            }
        }

        return $this;
    }

    // Image Uploder :

    public function setImageFile(?File $imageFile = null): void
     {
         $this->imageFile = $imageFile;
 
         if (null !== $imageFile) {
             // It is required that at least one field changes if you are using doctrine
             // otherwise the event listeners won't be called and the file is lost
             $this->updatedAt = new \DateTimeImmutable();
         }
     }
 
     public function getImageFile(): ?File
     {
         return $this->imageFile;
     }
 
     public function setImageName(?string $imageName): void
     {
         $this->imageName = $imageName;
     }
 
     public function getImageName(): ?string
     {
         return $this->imageName;
     }
 
}
