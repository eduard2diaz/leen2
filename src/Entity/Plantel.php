<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity
 */
class Plantel
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *      max = 255,
     *      maxMessage = "El nombre del plantel no puede exceder los {{ limit }} caracteres",
     * )
     */
    private $nombre;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Escuela", mappedBy="plantel")
     */
    private $escuelas;

    public function __construct()
    {
        $this->escuelas = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * @param mixed $nombre
     */
    public function setNombre($nombre): void
    {
        $this->nombre = $nombre;
    }

    public function __toString()
    {
        return $this->getNombre();
    }

    /**
     * @return Collection|Escuela[]
     */
    public function getEscuelas(): Collection
    {
        return $this->escuelas;
    }

    public function addEscuela(Escuela $escuela): self
    {
        if (!$this->escuelas->contains($escuela)) {
            $this->escuelas[] = $escuela;
            $escuela->setPlantel($this);
        }

        return $this;
    }

    public function removeEscuela(Escuela $escuela): self
    {
        if ($this->escuelas->contains($escuela)) {
            $this->escuelas->removeElement($escuela);
            // set the owning side to null (unless already changed)
            if ($escuela->getPlantel() === $this) {
                $escuela->setPlantel(null);
            }
        }

        return $this;
    }
}
