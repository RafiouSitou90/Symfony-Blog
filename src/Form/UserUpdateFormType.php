<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserUpdateFormType extends AbstractType
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
                'required' => true,
                'attr'=> [
                    'class' => 'form-control input-lg'
                ],
            ])
            ->add('fullName', TextType::class, [
                'required' => true,
                'attr'=> [
                    'class' => 'form-control input-lg'
                ],
            ])
            ->add('profile', ProfileFormType::class, [
                'label' => false,
                'required' => false
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
