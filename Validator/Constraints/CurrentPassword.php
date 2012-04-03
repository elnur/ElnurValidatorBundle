<?php
namespace Elnur\ValidatorBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CurrentPassword extends Constraint
{
    public $message = "Current password is invalid";

    /**
     * @return string
     */
    public function validatedBy()
    {
        return 'elnur.validator.current_password';
    }
}
