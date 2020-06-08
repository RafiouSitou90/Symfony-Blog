<?php


namespace App\Tests\Form;


use App\Entity\User;
use App\Form\UserFormType;
use Symfony\Component\Form\Test\TypeTestCase;

class UserFormTypeTest extends TypeTestCase
{
    /**
     * @return void
     */
    public function testValidSubmitData()
    {
        $formData = [
            'username' => 'Username',
            'email' => 'email@domain.com',
            'fullName' => 'Full Name',
            'plainPassword' => [
                'first_option' => 'Password',
                'second_option' => 'Password',
            ],
        ];

        $user = (new User())
            ->setUsername('Username')
            ->setEmail('email@domain.com')
            ->setFullName('Full Name')
        ;

        $form = $this->factory->create(UserFormType::class, $user);
        $expected = (new User())
            ->setUsername('Username')
            ->setEmail('email@domain.com')
            ->setFullName('Full Name')
        ;

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($expected, $user);

        $children = $form->createView()->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
