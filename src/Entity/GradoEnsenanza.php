<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GradoEnsenanzaRepository")
 *@UniqueEntity(fields={"nombre","tipoensenanza"})
 */
class GradoEnsenanza
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     * @Assert\Length(
     *      max = 30,
     *      maxMessage = "El nombre del grado de enseñanza no puede exceder los {{ limit }} caracteres",
     *)
     */
    private $nombre;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TipoEnsenanza", inversedBy="gradoEnsenanzas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tipoensenanza;

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

    public function getTipoensenanza(): ?TipoEnsenanza
    {
        return $this->tipoensenanza;
    }

    public function setTipoensenanza(?TipoEnsenanza $tipoensenanza): self
    {
        $this->tipoensenanza = $tipoensenanza;

        return $this;
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context)
    {
        if (null==$this->getTipoensenanza())
            $context->addViolation('Seleccione un tipo de enseñanza.');
    }

    public function __toString()
    {
     return $this->getNombre();
    }
}
