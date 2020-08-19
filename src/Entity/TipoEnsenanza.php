<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity
 * @UniqueEntity("nombre")
 */
class TipoEnsenanza
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=40)
     * @Assert\Length(
     *      max = 40,
     *      maxMessage = "El nombre del tipo de enseñanza no puede exceder los {{ limit }} caracteres",
     *)
     */
    private $nombre;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\GradoEnsenanza", mappedBy="tipoensenanza", orphanRemoval=true)
     */
    private $gradoEnsenanzas;

    public function __construct()
    {
        $this->gradoEnsenanzas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * @return Collection|GradoEnsenanza[]
     */
    public function getGradoEnsenanzas(): Collection
    {
        return $this->gradoEnsenanzas;
    }

    public function addGradoEnsenanza(GradoEnsenanza $gradoEnsenanza): self
    {
        if (!$this->gradoEnsenanzas->contains($gradoEnsenanza)) {
            $this->gradoEnsenanzas[] = $gradoEnsenanza;
            $gradoEnsenanza->setTipoensenanza($this);
        }

        return $this;
    }

    public function removeGradoEnsenanza(GradoEnsenanza $gradoEnsenanza): self
    {
        if ($this->gradoEnsenanzas->contains($gradoEnsenanza)) {
            $this->gradoEnsenanzas->removeElement($gradoEnsenanza);
            // set the owning side to null (unless already changed)
            if ($gradoEnsenanza->getTipoensenanza() === $this) {
                $gradoEnsenanza->setTipoensenanza(null);
            }
        }

        return $this;
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context)
    {
        if (null==$this->getGradoEnsenanzas())
            $context->addViolation('Seleccione un grado de enseñanza.');
    }

    public function __toString()
    {
      return $this->getNombre();
    }

}
