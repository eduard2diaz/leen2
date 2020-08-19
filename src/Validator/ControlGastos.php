<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ControlGastos extends Constraint
{
    public $message = 'Los gastos registrados superan al monto asignado al proyecto.';
    public $service = 'control_gastos.validator';
    public $em = null;
    public $plantrabajo;
    public $repositoryMethod = 'findBy';
    public $errorPath = 'monto';
    public $ignoreNull = true;

    public function getRequiredOptions()
    {
        return ['plantrabajo'];
    }

    public function validatedBy()
    {
        return $this->service;
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    public function getDefaultOption()
    {
        return 'plantrabajo';
    }
}
