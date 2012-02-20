<?php
namespace Elnur\ValidatorBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint,
    Symfony\Component\Validator\ConstraintValidator,
    Symfony\Bridge\Doctrine\RegistryInterface;

class UniqueEntityCaseInsensitiveValidator extends ConstraintValidator
{
    /**
     * @var \Symfony\Bridge\Doctrine\RegistryInterface
     */
    private $registry;

    /**
     * @param \Symfony\Bridge\Doctrine\RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param  $entity
     * @param  \Symfony\Component\Validator\Constraint $constraint
     * @return bool
     */
    public function isValid($entity, Constraint $constraint)
    {
        $fields = (array) $constraint->fields;

        $em = $this->registry->getEntityManager();
        $entityName = $this->context->getCurrentClass();
        $class = $em->getClassMetadata($entityName);

        $repository = $em->getRepository($entityName);
        $builder = $repository->createQueryBuilder('x');

        foreach ($fields as $field) {
            $value = $class->reflFields[$field]->getValue($entity);
            $builder->andWhere(
                'lower(x.' . $field. ') = lower(:' . $field . ')'
            );
            $builder->setParameter($field, $value);
        }

        $result = $builder->getQuery()->getResult();

        if (count($result) && $result[0] !== $entity) {
            $oldPath = $this->context->getPropertyPath();
            $this->context->setPropertyPath(
                empty($oldPath) ? $fields[0] : $oldPath . '.' . $fields[0]
            );
            $this->context->addViolation(
                $constraint->message,
                array(),
                $class->reflFields[$fields[0]]->getValue($entity)
            );
            $this->context->setPropertyPath($oldPath);
        }

        return true;
    }
}
