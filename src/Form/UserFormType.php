<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'required' => true,
                'attr'=> [
                    'class' => 'form-control input-lg'
                ],
            ])
            ->add('email', EmailType::class, [
                'attr'=> [
                    'class' => 'form-control input-lg'
                ],
                'required' => true
            ])
            ->add('fullName', TextType::class, [
                'required' => true,
                'attr'=> [
                    'class' => 'form-control input-lg'
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'required' => true,
//                'constraints' => [
//                    new NotBlank([
//                        'message' => 'The password should not be blank',
//                    ]),
//                    new Length([
//                        'min' => 8,
//                        'minMessage' => 'The password must be at least {{ limit }} characters long.',
//                        'max' => 50,
//                        'maxMessage' => 'The password cannot be longer than {{ limit }} characters.',
//                    ]),
//                ],
                'first_options' => array(
                    'label' => 'Password',
                    'attr'=> ['class' => 'form-control input-lg'],
                ),
                'second_options' => array(
                    'label' => 'Confirm the password',
                    'attr'=> ['class' => 'form-control input-lg']
                )
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
