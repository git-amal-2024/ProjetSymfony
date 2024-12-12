<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client extends User
{


    #[ORM\Column(length: 255)]
    private ?string $codeClient = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tel = null;

    /**
     * @var Collection<int, Commande>
     */
    #[ORM\OneToMany(targetEntity: Commande::class, mappedBy: 'client')]
    private Collection $relation;

    public function __construct()
    {
        $this->relation = new ArrayCollection();
    }


    public function getCodeClient(): ?string
    {
        return $this->codeClient;
    }

    public function setCodeClient(string $codeClient): static
    {
        $this->codeClient = $codeClient;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): static
    {
        $this->tel = $tel;

        return $this;
    }

    /**
     * @return Collection<int, Commande>
     */
    public function getRelation(): Collection
    {
        return $this->relation;
    }

    public function addRelation(Commande $relation): static
    {
        if (!$this->relation->contains($relation)) {
            $this->relation->add($relation);
            $relation->setClient($this);
        }

        return $this;
    }

    public function removeRelation(Commande $relation): static
    {
        if ($this->relation->removeElement($relation)) {
            // set the owning side to null (unless already changed)
            if ($relation->getClient() === $this) {
                $relation->setClient(null);
            }
        }

        return $this;
    }
}
