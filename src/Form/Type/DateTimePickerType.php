<?php


namespace App\Form\Type;


use App\Utils\MomentFormatConverter;
use Locale;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function Symfony\Component\String\u;

class DateTimePickerType extends AbstractType
{
    /**
     * @var MomentFormatConverter
     */
    private MomentFormatConverter $momentFormatConverter;

    /**
     * DateTimePickerType constructor.
     * @param MomentFormatConverter $momentFormatConverter
     */
    public function __construct (MomentFormatConverter $momentFormatConverter)
    {
        $this->momentFormatConverter = $momentFormatConverter;
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     *
     * @return void
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['attr']['data-date-format'] = $this->momentFormatConverter->convert($options['format']);
        $view->vars['attr']['data-date-locale'] = u(Locale::getDefault())->replace('_', '-')->lower();
    }

    /**
     * @param OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'widget' => 'single_text',
            'html5' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return DateTimeType::class;
    }
}
