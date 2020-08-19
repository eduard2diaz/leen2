<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity
 * @UniqueEntity(fields={"curp","grado","escuela"})
 */
class CondicionDocenteEducativa
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\DiagnosticoPlantel")
     * @ORM\JoinColumn(nullable=false)
     */
    private $diagnostico;

    /**
     * @ORM\Column(type="string", length=18)
     * @Assert\Length(
     *      max = 18,
     *      min = 18,
     *      maxMessage = "El CURP no puede exceder los {{ limit }} caracteres",
     *)
     */
    private $curp;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\Length(
     *      max = 50,
     *      maxMessage = "El nombre no puede exceder los {{ limit }} caracteres",
     *)
     */
    private $nombre;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\GradoEnsenanza")
     * @ORM\JoinColumn(nullable=false)
     */
    private $grado;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Escuela")
     * @ORM\JoinColumn(nullable=false)
     */
    private $escuela;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDiagnostico(): ?DiagnosticoPlantel
    {
        return $this->diagnostico;
    }

    public function setDiagnostico(?DiagnosticoPlantel $diagnostico): self
    {
        $this->diagnostico = $diagnostico;

        return $this;
    }

    public function getCurp(): ?string
    {
        return $this->curp;
    }

    public function setCurp(string $curp): self
    {
        $this->curp = $curp;

        return $this;
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
     * @return mixed
     */
    public function getGrado()
    {
        return $this->grado;
    }

    /**
     * @param mixed $grado
     */
    public function setGrado($grado): void
    {
        $this->grado = $grado;
    }

    /**
     * @return mixed
     */
    public function getEscuela()
    {
        return $this->escuela;
    }

    /**
     * @param mixed $escuela
     */
    public function setEscuela($escuela): void
    {
        $this->escuela = $escuela;
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context)
    {
        if (null==$this->getDiagnostico())
            $context->addViolation('Seleccione un diagnóstico.');
        if (null==$this->getEscuela())
            $context->addViolation('Seleccione una escuela.');
    }
}
