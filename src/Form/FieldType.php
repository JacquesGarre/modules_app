<?php

namespace App\Form;

use App\Entity\Field;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class FieldType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices'  => [
                    'Text' => 'text'
                ],
                'constraints' => [
                    new NotBlank()
                ],
            ])
            ->add('label', TextType::class, [
                'constraints' => [
                    new NotBlank()
                ],
            ])
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Regex('/^[A-Za-z0-9_]*$/')
                ],
            ])
            ->add('value')
            ->add('required')
            ->add('disabled')
            ->add('readonly')
            ->add('Save', ButtonType::class, [
                'attr' => [
                    'class' => 'btn-primary float-end',
                    'data-action' => 'form#submit',
                    'data-form-target' => 'submitBtn'
                ],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Field::class,
        ]);
    }
}
