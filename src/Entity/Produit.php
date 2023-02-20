<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\ProduitRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Assert\NotBlank(message:'ce champ est obligatoire')]
    // #[Assert\Type (message:'ce champ n est pas valide')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:'Ce champ est obligatoire')]
    #[Assert\Name]
    // #[Assert\Type (message:'ce champ n est pas valide')]
    private ?string $libelle = null;

    #[ORM\Column]
    #[Assert\NotBlank(message:'ce champ est obligatoire')]
    #[Assert\floatval(message:'ce champ n est pas valide')]
    private ?float $prix = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    // #[Assert\Type(message:'ce champ n est pas valide')]
    private ?int $stock = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    // #[Assert\Type(message:'ce champ n est pas valide')]
    private ?string $ref = null;

    #[ORM\ManyToOne(inversedBy: 'produits')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    private ?Categorie $categorie = null;

    public function getId(): ?int
    {
        return $this->id;
    }
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
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
}
