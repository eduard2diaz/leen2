<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PlanTrabajoRepository")
 * @UniqueEntity(fields={"plantel","numero"})
 */
class PlanTrabajo
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Plantel")
     * @ORM\JoinColumn(nullable=false)
     */
    private $plantel;

    /**
     * @ORM\Column(type="date")
     */
    private $fechainicio;

    /**
     * @ORM\Column(type="date",nullable=true)
     */
    private $fechafin;

    /**
     * @ORM\Column(type="float")
     * @Assert\Range(
     *      min = 0,
     * )
     */
    private $montoasignado;

    /**
     * @ORM\Column(type="integer")
     */
    private $numero;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TipoAccion")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tipoAccion;

    /**
     * @ORM\Column(type="text")
     */
    private $descripcionaccion;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\Length(
     *      max = 50,
     *      maxMessage = "El tiempo estimado no puede exceder los {{ limit }} caracteres",
     *)
     */
    private $tiempoestimado;

    /**
     * @ORM\Column(type="float")
     */
    private $costoestimado;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $planarchivo;

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

    public function getTipoAccion(): ?TipoAccion
    {
        return $this->tipoAccion;
    }

    public function setTipoAccion(?TipoAccion $tipoAccion): self
    {
        $this->tipoAccion = $tipoAccion;

        return $this;
    }

    public function getDescripcionaccion(): ?string
    {
        return $this->descripcionaccion;
    }

    public function setDescripcionaccion(string $descripcionaccion): self
    {
        $this->descripcionaccion = $descripcionaccion;

        return $this;
    }

    public function getTiempoestimado(): ?string
    {
        return $this->tiempoestimado;
    }

    public function setTiempoestimado(string $tiempoestimado): self
    {
        $this->tiempoestimado = $tiempoestimado;

        return $this;
    }

    public function getCostoestimado(): ?float
    {
        return $this->costoestimado;
    }

    public function setCostoestimado(float $costoestimado): self
    {
        $this->costoestimado = $costoestimado;

        return $this;
    }

    public function getPlanarchivo(): ?string
    {
        return $this->planarchivo;
    }

    public function setPlanarchivo(string $planarchivo): self
    {
        $this->planarchivo = $planarchivo;

        return $this;
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(?UploadedFile $file)
    {
        $this->file = $file;
    }

    public function getFechainicio(): ?\DateTimeInterface
    {
        return $this->fechainicio;
    }

    public function setFechainicio(\DateTimeInterface $fechainicio): self
    {
        $this->fechainicio = $fechainicio;

        return $this;
    }

    public function getFechafin(): ?\DateTimeInterface
    {
        return $this->fechafin;
    }

    public function setFechafin(?\DateTimeInterface $fechafin): self
    {
        $this->fechafin = $fechafin;

        return $this;
    }

    public function getMontoasignado(): ?float
    {
        return $this->montoasignado;
    }

    public function setMontoasignado(float $montoasignado): self
    {
        $this->montoasignado = $montoasignado;

        return $this;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return mixed
     */
    public function getPlantel()
    {
        return $this->plantel;
    }

    /**
     * @param mixed $plantel
     */
    public function setPlantel($plantel): void
    {
        $this->plantel = $plantel;
    }

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero(int $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function __toString()
    {
        return (String)$this->getNumero();
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context)
    {
        if (null == $this->getPlantel())
            $context->addViolation('Seleccione un plantel.');
        if ($this->getFechafin() != null && $this->getFechainicio() > $this->getFechafin())
            $context->addViolation('Compruebe las fechas de inicio y fin.');
        if (null == $this->getTipoAccion())
            $context->addViolation('Seleccione un tipo de acción.');
    }
}
