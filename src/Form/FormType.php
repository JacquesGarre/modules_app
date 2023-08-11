<?php

namespace App\Form;

use App\Entity\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;

class FormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('action', ChoiceType::class, array(
                'choices' => [
                    'Add' => 'add',
                    'Edit' => 'edit'
                ]
            ))
            ->add('fields')
            ->add('Submit', ButtonType::class, [
                'attr' => [
                    'class' => 'btn-primary float-end',
                    'data-action' => 'form#submit',
                    'data-form-target' => 'submitBtn'
                ],
            ]);
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Form::class,
        ]);
    }
}
