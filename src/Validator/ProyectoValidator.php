<?php

namespace App\Validator;

use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ProyectoValidator extends ConstraintValidator
{
    private $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function validate($value, Constraint $constraint)
    {

        /* @var $constraint App\Validator\Proyecto */
        $pa = PropertyAccess::createPropertyAccessor();

        if (!$constraint instanceof Proyecto)
            throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\Proyecto');

        if ($constraint->em) {
            $em = $this->registry->getManager($constraint->em);
            if (!$em)
                throw new ConstraintDefinitionException(sprintf('Object manager "%s" does not exist.', $constraint->em));
        } else {
            $em = $this->registry->getManagerForClass(get_class($value));
            if (!$em)
                throw new ConstraintDefinitionException(sprintf('Unable to find the object manager associated with an entity of class "%s".', get_class($value)));
        }

        $id = $pa->getValue($value, 'id');
        $monto = $pa->getValue($value, 'montoasignado');
        if (null != $id) {
            $result=$em->getRepository(\App\Entity\Proyecto::class)->findSumaGastos($id);

            if ($result > $monto)
                $this->context->buildViolation($constraint->message)->atPath($constraint->errorPath)->addViolation();
        }
    }
}
