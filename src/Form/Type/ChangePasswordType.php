<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePasswordType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('currentPassword', PasswordType::class, [
                'constraints' => [
                    new UserPassword(),
                ],
                'label' => 'Current password',
                'attr' => [
                    'autocomplete' => 'off',
                ],
            ])
            ->add('newPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'constraints' => [
                    new NotBlank([
                        'message' => 'The password should not be blank',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'The password must be at least {{ limit }} characters long.',
                        'max' => 50,
                        'maxMessage' => 'The password cannot be longer than {{ limit }} characters.',
                    ]),
                ],
                'first_options' => array(
                    'label' => 'New password',
                    'attr'=> ['class' => 'form-control input-lg'],
                ),
                'second_options' => array(
                    'label' => 'Confirm the new password',
                    'attr'=> ['class' => 'form-control input-lg']
                )
            ])
        ;
    }
}
