<?php

namespace App\Entity;

use App\Repository\CardRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\CardIem;

#[ORM\Entity(repositoryClass: CardRepository::class)]
class Card 
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'card', targetEntity: CardItem::class)]
    private Collection $cardItems;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $total = null;

    #[ORM\OneToOne(inversedBy: 'card', cascade: ['persist', 'remove'])]
    private ?User $user = null;

    public function __construct()
    {
        $this->cardItems = new ArrayCollection();
        $total = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, CardItem>
     */
    public function getCardItems(): Collection
    {
        return $this->cardItems;
    }

    public function addCardItem(CardItem $cardItem): self
    {
        if (!$this->cardItems->contains($cardItem)) {
            $this->cardItems->add($cardItem);
            $cardItem->setCard($this);
        }

        return $this;
    }

    public function removeCardItem(CardItem $cardItem): self
    {
        if ($this->cardItems->removeElement($cardItem)) {
            // set the owning side to null (unless already changed)
            if ($cardItem->getCard() === $this) {
                $cardItem->setCard(null);
            }
        }

        return $this;
    }

    public function getTotal(): ?string
    {
        return $this->total;
    }

    public function setTotal(?string $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
    public function getCartItemByProduit(Produit $produit): ?CardItem
{
    foreach ($this->cardItems as $cartItem) {
        if ($cartItem->getProduit() === $produit) {
            return $cartItem;
        }
    }
    
    return null;
}
}
