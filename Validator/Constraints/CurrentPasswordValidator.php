<?php
namespace Elnur\ValidatorBundle\Validator\Constraints;

use Symfony\Component\Validator\ConstraintValidator,
    Symfony\Component\Validator\Constraint,
    Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface,
    Symfony\Component\Security\Core\SecurityContextInterface;

class CurrentPasswordValidator extends ConstraintValidator
{
    /**
     * @var \Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface
     */
    private $encoderFactory;

    /**
     * @var \Symfony\Component\Security\Core\SecurityContextInterface
     */
    private $securityContext;

    /**
     * @param \Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface $encoderFactory
     * @param \Symfony\Component\Security\Core\SecurityContextInterface        $securityContext
     */
    public function __construct(EncoderFactoryInterface  $encoderFactory,
                                SecurityContextInterface $securityContext)
    {
        $this->encoderFactory  = $encoderFactory;
        $this->securityContext = $securityContext;
    }

    /**
     * @param  string $currentPassword
     * @param  \Symfony\Component\Validator\Constraint $constraint
     * @return boolean
     */
    public function isValid($currentPassword, Constraint $constraint)
    {
        $currentUser = $this->securityContext->getToken()->getUser();
        $encoder = $this->encoderFactory->getEncoder($currentUser);

        $isValid = $encoder->isPasswordValid(
            $currentUser->getPassword(),
            $currentPassword,
            $currentUser->getSalt()
        );

        if (!$isValid) {
            $this->setMessage($constraint->message);
            return false;
        }

        return true;
    }
}
