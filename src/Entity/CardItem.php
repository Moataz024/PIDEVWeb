<?php

namespace App\Entity;

use App\Repository\CardItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CardItemRepository::class)]
class CardItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

   
    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\OneToOne(mappedBy: 'carditem', cascade: ['persist', 'remove'])]
    private ?Produit $produit = null;

    #[ORM\ManyToOne(inversedBy: 'cardItems')]
    private ?Card $card = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(?Produit $produit): self
    {
        // unset the owning side of the relation if necessary
        if ($produit === null && $this->produit !== null) {
            $this->produit->setCarditem(null);
        }

        // set the owning side of the relation if necessary
        if ($produit !== null && $produit->getCarditem() !== $this) {
            $produit->setCarditem($this);
        }

        $this->produit = $produit;

        return $this;
    }

    public function getCard(): ?Card
    {
        return $this->card;
    }

    public function setCard(?Card $card): self
    {
        $this->card = $card;

        return $this;
    }
}
