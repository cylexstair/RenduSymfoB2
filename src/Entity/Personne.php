<?php

namespace App\Entity;

use App\Repository\PersonneRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PersonneRepository::class)
 */
class Personne
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="integer")
     */
    private $argent;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    public $aRembourser;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getnom(): ?string
    {
        return $this->nom;
    }

    public function setnom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getargent(): ?int
    {
        return $this->argent;
    }

    public function setargent(int $argent): self
    {
        $this->argent = $argent;

        return $this;
    }

    public function getARembourser(): ?int
    {
        return $this->aRembourser;
    }

    public function setARembourser(?int $aRembourser): self
    {
        $this->aRembourser = $aRembourser;

        return $this;
    }
}
