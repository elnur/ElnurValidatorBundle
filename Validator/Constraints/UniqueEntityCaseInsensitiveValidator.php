<?php
/*
 * Copyright (c) 2011-2012 Elnur Abdurrakhimov
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
namespace Elnur\ValidatorBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Bridge\Doctrine\RegistryInterface;

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
            if (isset($class->associationMappings[$field])) {
                $builder->andWhere('x.' . $field . ' = :' . $field);
            } else {
                $builder->andWhere(
                    'lower(x.' . $field. ') = lower(:' . $field . ')'
                );
            }

            $value = $class->reflFields[$field]->getValue($entity);
            $builder->setParameter($field, $value);
        }

        $result = $builder->getQuery()->getResult();

        if (count($result) && $result[0] !== $entity) {
            $this->context->addViolationAtSubPath($fields[0], $constraint->message, array($value));
        }

        return true;
    }
}
