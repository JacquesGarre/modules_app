<?php

namespace App\Form;

use App\Entity\Module;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ModuleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('labelPlural', TextType::class, [
                'constraints' => [
                    new NotBlank()
                ],
            ])
            ->add('labelSingular', TextType::class, [
                'constraints' => [
                    new NotBlank()
                ],
            ])
            ->add('sqlTable', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Regex('/^[A-Za-z0-9_]*$/')
                ],
            ])
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
            'data_class' => Module::class,
        ]);
    }
}
