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
 * @UniqueEntity("ccts")
 */
class Escuela
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
     *      maxMessage = "El nombre de la escuela no puede exceder los {{ limit }} caracteres",
     * )
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=10)
     * @Assert\Length(
     *      max = 11,
     *      min = 11,
     *      maxMessage = "La clave de la escuela no puede exceder los {{ limit }} caracteres",
     * )
     */
    private $ccts;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\TipoEnsenanza")
     */
    private $tipoensenanza;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Plantel", inversedBy="escuelas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $plantel;

    public function __construct()
    {
        $this->tipoensenanza = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|TipoEnsenanza[]
     */
    public function getTipoensenanza(): Collection
    {
        return $this->tipoensenanza;
    }

    public function addTipoensenanza(TipoEnsenanza $tipoensenanza): self
    {
        if (!$this->tipoensenanza->contains($tipoensenanza)) {
            $this->tipoensenanza[] = $tipoensenanza;
        }

        return $this;
    }

    public function removeTipoensenanza(TipoEnsenanza $tipoensenanza): self
    {
        if ($this->tipoensenanza->contains($tipoensenanza)) {
            $this->tipoensenanza->removeElement($tipoensenanza);
        }

        return $this;
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

    /**
     * @return mixed
     */
    public function getCcts()
    {
        return $this->ccts;
    }

    /**
     * @param mixed $ccts
     */
    public function setCcts($ccts): void
    {
        $this->ccts = $ccts;
    }

    public function getPlantel(): ?Plantel
    {
        return $this->plantel;
    }

    public function setPlantel(?Plantel $plantel): self
    {
        $this->plantel = $plantel;

        return $this;
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context)
    {
        if($this->getTipoensenanza()->isEmpty())
            $context->addViolation('Seleccione al menos un tipo de enseñanza.');
    }

    public function __toString()
    {
        return $this->getNombre().'('.$this->getCcts().')';
    }


}
