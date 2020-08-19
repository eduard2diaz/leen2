<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity
 */
class RendicionCuentas
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PlanTrabajo")
     * @ORM\JoinColumn(nullable=false)
     */
    private $plantrabajo;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TipoAccion")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tipoAccion;

    /**
     * @ORM\Column(type="date")
     */
    private $fechacaptura;

    /**
     * @ORM\Column(type="float")
     */
    private $monto;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $rendicionesarchivos;

    /**
     * @Assert\File(
     * maxSize="20mi",
     * notReadableMessage = "No se puede leer el archivo",
     * maxSizeMessage = "El archivo es demasiado grande. El tamaño máximo permitido es 20Mb",
     * uploadIniSizeErrorMessage = "El archivo es demasiado grande. El tamaño máximo permitido es 20Mb",
     * uploadFormSizeErrorMessage = "El archivo es demasiado grande. El tamaño máximo permitido es 20Mb",
     * uploadErrorMessage = "No se puede subir el archivo")
     */
    private $file;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlanTrabajo(): ?PlanTrabajo
    {
        return $this->plantrabajo;
    }

    public function setPlanTrabajo(?PlanTrabajo $plantrabajo): self
    {
        $this->plantrabajo = $plantrabajo;

        return $this;
    }


    public function getTipoAccion(): ?TipoAccion
    {
        return $this->tipoAccion;
    }

    public function setTipoAccion(?TipoAccion $tipoAccion): self
    {
        $this->tipoAccion = $tipoAccion;

        return $this;
    }

    public function getFechacaptura(): ?\DateTimeInterface
    {
        return $this->fechacaptura;
    }

    public function setFechacaptura(\DateTimeInterface $fechacaptura): self
    {
        $this->fechacaptura = $fechacaptura;

        return $this;
    }

    public function getMonto(): ?float
    {
        return $this->monto;
    }

    public function setMonto(float $monto): self
    {
        $this->monto = $monto;

        return $this;
    }

    public function getRendicionesarchivos(): ?string
    {
        return $this->rendicionesarchivos;
    }

    public function setRendicionesarchivos(string $rendicionesarchivos): self
    {
        $this->rendicionesarchivos = $rendicionesarchivos;

        return $this;
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(?UploadedFile $file) {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile() {
        return $this->file;
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context)
    {
        if (null==$this->getPlanTrabajo())
            $context->addViolation('Seleccione un plan de trabajo.');

        if (null==$this->getTipoAccion())
            $context->addViolation('Seleccione un tipo de acción.');
    }

}
