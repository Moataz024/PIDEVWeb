<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: ProduitRepository::class)]
#[Vich\Uploadable]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("produits")]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups("produits")]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public ?string $libelle = null;

    #[ORM\Column]
    #[Groups("produits")]
    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    private ?float $prix = null;

    #[ORM\Column]
    #[Groups("produits")]
    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    private ?int $stock = null;

    #[ORM\Column(length: 255)]
    #[Groups("produits")]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $ref = null;

    #[ORM\ManyToOne(inversedBy: 'produits')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups("produits")]
    #[Assert\NotNull]
    private ?Categorie $categorie = null;


    #[Vich\UploadableField(mapping: 'produit_directory', fileNameProperty: 'imageName')]
    #[Groups("produits")]
    #[Assert\Image(maxSize: "2M")]
    private ?File $imageFile = null;

    #[ORM\Column(nullable: true)]
    #[Groups("produits")]
    private ?string $imageName = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function getRef(): ?string
    {
        return $this->ref;
    }

    public function setRef(string $ref): self
    {
        $this->ref = $ref;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }


       /**
      * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
      * of 'UploadedFile' is injected into this setter to trigger the update. If this
      * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
      * must be able to accept an instance of 'File' as the bundle will inject one here
      * during Doctrine hydration.
      *
      * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
      */
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
