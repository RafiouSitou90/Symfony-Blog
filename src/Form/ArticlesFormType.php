<?php

namespace App\Form;

use App\Entity\Articles;
use App\Entity\Categories;
use App\Form\Type\DateTimePickerType;
use App\Form\Type\TagsInputType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ArticlesFormType extends AbstractType
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
            ->add('title', TextType::class, [])
            ->add('summary', TextType::class, [])
            ->add('content', TextareaType::class, [])
            ->add('imageFile', VichImageType::class, [
                'label' => 'Image',
                'download_uri' => false,
                'required' => true
            ])
            ->add('publishedAt', DateTimePickerType::class, [
                'required' => false
            ])
            ->add('articleStatus', ChoiceType::class, [
                'choices' => [
                    strtoupper(Articles::DRAFT()) => Articles::DRAFT(),
                    strtoupper(Articles::PUBLISHED()) => Articles::PUBLISHED(),
                    strtoupper(Articles::ARCHIVED()) => Articles::ARCHIVED()
                ],
            ])
            ->add('commentsStatus', ChoiceType::class, [
                'choices' => [
                    strtoupper(Articles::COMMENT_OPENED()) => Articles::COMMENT_OPENED(),
                    strtoupper(Articles::COMMENT_CLOSED()) => Articles::COMMENT_CLOSED()
                ],
            ])
            ->add('category', EntityType::class, [
                'placeholder' => 'Select the article category',
                'label' => 'Category',
                'class' => Categories::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC');
                },
                'choice_label' => 'name',
                'choice_value' => 'id',
            ])
            ->add('tags', TagsInputType::class, [
                'label' => 'Tags',
                'required' => false,
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
            'data_class' => Articles::class,
        ]);
    }
}
