<?php

namespace App\Entity;

use App\Repository\ApplicationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ApplicationRepository::class)]
class Application
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Appname = null;

    #[ORM\Column(nullable: true)]
    private ?int $Appage = null;

    #[ORM\ManyToOne(inversedBy: 'applications')]
    private ?Academy $academy = null;

    #[ORM\ManyToOne(inversedBy: 'applications')]
    private ?User $User = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAppname(): ?string
    {
        return $this->Appname;
    }

    public function setAppname(?string $Appname): self
    {
        $this->Appname = $Appname;

        return $this;
    }

    public function getAppage(): ?int
    {
        return $this->Appage;
    }

    public function setAppage(?int $Appage): self
    {
        $this->Appage = $Appage;

        return $this;
    }

    public function getAcademy(): ?Academy
    {
        return $this->academy;
    }

    public function setAcademy(?Academy $academy): self
    {
        $this->academy = $academy;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): self
    {
        $this->User = $User;

        return $this;
    }
    public function __sleep()
    {
        return ['id'];
    }
}
