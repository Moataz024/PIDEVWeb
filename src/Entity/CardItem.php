<?php

namespace App\Entity;

use App\Repository\CardItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: CardItemRepository::class)]
class CardItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

   
    #[ORM\Column]
    #[Groups("cart")]
    private ?int $quantity = null;

   

    #[ORM\ManyToOne(inversedBy: 'cardItems' )]
    private ?Card $card = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column]
    private ?float $prix = null;

   

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

   

    public function getCard(): ?Card
    {
        return $this->card;
    }

    public function setCard(?Card $card): self
    {
        $this->card = $card;

        return $this;
    }

    public function removeCarditem(CardItem $carditem): self
{
    if ($this->cardItems->contains($carditem)) {
        $carditem->setCard(null);
        $this->cardItems->removeElement($carditem);
    }

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

   
}
