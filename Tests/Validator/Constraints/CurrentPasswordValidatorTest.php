<?php
namespace Elnur\ValidatorBundle\Tests\Vadilator\Constraints;

use Elnur\ValidatorBundle\Validator\Constraints\CurrentPassword,
    Elnur\ValidatorBundle\Validator\Constraints\CurrentPasswordValidator;

class CurrentPasswordValidatorTest extends \PHPUnit_Framework_TestCase
{
    const INPUT_PASSWORD = 'password';
    const USER_PASSWORD  = 'h4ck1r';
    const USER_SALT      = 's01t';

    /**
     * @var \Elnur\ValidatorBundle\Validator\Constraints\CurrentPasswordValidator
     */
    private $validator;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $token;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $encoderFactory;

    public function setUp()
    {
        $this->encoderFactory = $this->getMock('Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface');
        $securityContext = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');

        $this->validator = new CurrentPasswordValidator($this->encoderFactory, $securityContext);

        $this->token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');

        $securityContext
            ->expects($this->once())
            ->method('getToken')
            ->will($this->returnValue($this->token))
        ;
    }

    /**
     * @test
     */
    public function validWhenPasswordMatch()
    {
        $this->getEncoderMock()
            ->expects($this->once())
            ->method('isPasswordValid')
            ->with(self::USER_PASSWORD, self::INPUT_PASSWORD, self::USER_SALT)
            ->will($this->returnValue(true))
        ;

        $constraint = new CurrentPassword();
        $this->assertTrue($this->validator->isValid(self::INPUT_PASSWORD, $constraint));
    }

    /**
     * @test
     */
    public function notValidWhenPasswordDoesntMatch()
    {
        $this->getEncoderMock()
            ->expects($this->once())
            ->method('isPasswordValid')
            ->with(self::USER_PASSWORD, self::INPUT_PASSWORD, self::USER_SALT)
            ->will($this->returnValue(false))
        ;

        $constraint = new CurrentPassword();
        $this->assertFalse($this->validator->isValid(self::INPUT_PASSWORD, $constraint));
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function throwExceptionWhenUserIsNotLoggedIn()
    {
        $this->token
            ->expects($this->once())
            ->method('getUser')
            ->will($this->returnValue('string'))
        ;

        $constraint = new CurrentPassword();
        $this->validator->isValid(self::INPUT_PASSWORD, $constraint);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getEncoderMock()
    {
        $user = $this->getMock('Symfony\Component\Security\Core\User\UserInterface');
        $encoder = $this->getMock('Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface');

        $this->encoderFactory
            ->expects($this->once())
            ->method('getEncoder')
            ->with($user)
            ->will($this->returnValue($encoder));

        $user
            ->expects($this->once())
            ->method('getPassword')
            ->will($this->returnValue(self::USER_PASSWORD));

        $user
            ->expects($this->once())
            ->method('getSalt')
            ->will($this->returnValue(self::USER_SALT));

        $this->token
            ->expects($this->once())
            ->method('getUser')
            ->will($this->returnValue($user));

        return $encoder;
    }
}
