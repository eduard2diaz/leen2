<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Doctrine\Common\Persistence\ManagerRegistry;

class ControlGastosValidator extends ConstraintValidator
{
    private $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function validate($value, Constraint $constraint)
    {

        /* @var $constraint App\Validator\ControlGastos */
        $pa = PropertyAccess::createPropertyAccessor();

        if (!$constraint instanceof ControlGastos)
            throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\ControlGastos');

        if ($constraint->em) {
            $em = $this->registry->getManager($constraint->em);
            if (!$em)
                throw new ConstraintDefinitionException(sprintf('Object manager "%s" does not exist.', $constraint->em));
        } else {
            $em = $this->registry->getManagerForClass(get_class($value));
            if (!$em)
                throw new ConstraintDefinitionException(sprintf('Unable to find the object manager associated with an entity of class "%s".', get_class($value)));
        }

        $plantrabajo = $pa->getValue($value, $constraint->plantrabajo);
        $id = $pa->getValue($value, 'id');

        $monto=$em->getRepository(\App\Entity\PlanTrabajo::class)->findSumaGastos($plantrabajo);
        if (!$id)
            $monto+=$pa->getValue($value, 'monto');

        if ($monto> $plantrabajo->getMontoAsignado())
            $this->context->buildViolation($constraint->message)->atPath($constraint->errorPath)->addViolation();
    }
}
