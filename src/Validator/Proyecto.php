<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Proyecto extends Constraint
{
    public $message = 'Existen gastos registrados que superan al monto asignado al proyecto.';
    public $service = 'proyecto.validator';
    public $em = null;
    public $repositoryMethod = 'findBy';
    public $errorPath = 'montoasignado';
    public $ignoreNull = true;

    public function validatedBy()
    {
        return $this->service;
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
