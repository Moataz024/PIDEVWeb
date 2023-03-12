<?php

namespace App\Entity;

use App\Repository\CardRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CardRepository::class)]
class Card
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("cart")]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups("cart")]
    private ?string $total = "0";

    #[ORM\OneToMany(mappedBy: 'card', targetEntity: CardItem::class, cascade: ['persist', 'remove'])]
    #[Groups("cart")]
    private Collection $cardItems;

    #[ORM\OneToOne(inversedBy: 'card')]
    #[Groups("cart")]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'card', targetEntity: Commande::class)]
    private Collection $commandes;

    public function __construct()
    {
        $this->cardItems = new ArrayCollection();
        $total = 0;
        $this->commandes = new ArrayCollection();
        
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTotal(): ?string
    {
        return $this->total;
    }

    public function setTotal(string $total): self
    {
        $this->total = $total;

        return $this;
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

    // public function removeCardItem(CardItem $cardItem): self
    // {
    //     if ($this->cardItems->removeElement($cardItem)) {
    //         // set the owning side to null (unless already changed)
    //         if ($cardItem->getCard() === $this) {
    //             $cardItem->setCard(null);
    //         }
    //     }

    //     return $this;
    // }
    public function removeCardItem(CardItem $carditem): self
{
    if ($this->cardItems->contains($carditem)) {
        $carditem->setCard(null);
        $this->cardItems->removeElement($carditem);
    }

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
    public function getCartItemByProduit($libelle): ?CardItem
    {
        foreach ($this->cardItems as $cartItem) {
            if ($cartItem->getLibelle() === $libelle) {
                return $cartItem;
            }
        }
        
        return null;
    }

    // public function addCommande(Commande $commande): self
    // {
    //     if (!$this->commandes->contains($commande)) {
    //         $this->commandes->add($commande);
    //         $commande->setCard($this);
    //     }

    //     return $this;
    // }

    // public function removeCommande(Commande $commande): self
    // {
    //     if ($this->commandes->removeElement($commande)) {
    //         // set the owning side to null (unless already changed)
    //         if ($commande->getCard() === $this) {
    //             $commande->setCard(null);
    //         }
    //     }

    //     return $this;
    // }

    // public function addCommand(Commande $command): self
    // {
    //     if (!$this->commands->contains($command)) {
    //         $this->commands->add($command);
    //         $command->setCard($this);
    //     }

    //     return $this;
    // }

    // public function removeCommand(Commande $command): self
    // {
    //     if ($this->commands->removeElement($command)) {
    //         // set the owning side to null (unless already changed)
    //         if ($command->getCard() === $this) {
    //             $command->setCard(null);
    //         }
    //     }

    //     return $this;
    // }

    /**
     * @return Collection<int, Commande>
     */
    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

    public function addCommande(Commande $commande): self
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes->add($commande);
            $commande->setCard($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): self
    {
        if ($this->commandes->removeElement($commande)) {
            // set the owning side to null (unless already changed)
            if ($commande->getCard() === $this) {
                $commande->setCard(null);
            }
        }

        return $this;
    }

}
