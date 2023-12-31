<?php

namespace App\Form;

use App\Entity\Form;
use App\Repository\FieldRepository;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use App\Entity\Field;

class FormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
                $field = $event->getData();
                $form = $event->getForm();

                $method = $event->getForm()->getConfig()->getMethod();
                
                if($method !== 'DELETE'){
                    $form
                    ->add('title')
                    ->add('action', ChoiceType::class, array(
                        'choices' => [
                            'Add' => 'add',
                            'Edit' => 'edit'
                        ]
                    ))
                    ->add('fields', EntityType::class, [
                        'expanded'      => true,
                        'class'         => Field::class,
                        'query_builder' => function (FieldRepository $repository) use($field) {
                
                            return $repository
                                ->createQueryBuilder('f')
                                ->where('f.module = :moduleId')
                                ->setParameter('moduleId', $field->getModule()->getId());
                        },
                        'multiple' => true
                    ])
                    ->add('Submit', ButtonType::class, [
                        'attr' => [
                            'class' => 'btn-primary float-end',
                            'data-action' => 'form#submit',
                            'data-form-target' => 'submitBtn'
                        ],
                    ]);
                } else {
                    $form
                    ->add('title', HiddenType::class)
                    ->add('action', HiddenType::class)
                    ->add('Submit', ButtonType::class, [
                        'attr' => [
                            'class' => 'btn-danger float-end',
                            'data-action' => 'form#submit',
                            'data-form-target' => 'submitBtn'
                        ],
                    ]);
                }

            });


        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Form::class,
        ]);
    }
}
