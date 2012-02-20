<?php
namespace Elnur\ValidatorBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UniqueEntityCaseInsensitive extends Constraint
{
    /**
     * @var string
     */
    public $message = 'This value is already used';

    /**
     * @var array
     */
    public $fields = array();

    /**
     * @return array
     */
    public function getRequiredOptions()
    {
        return array('fields');
    }

    /**
     * @return string
     */
    public function getDefaultOption()
    {
        return 'fields';
    }

    /**
     * @return string
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    /**
     * @return string
     */
    public function validatedBy()
    {
        return 'elnur.validator.unique_entity_case_insensitive';
    }
}
