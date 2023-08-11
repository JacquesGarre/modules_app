<?php

namespace App\Form;

use App\Entity\Listing;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;


class ListingType extends AbstractType
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
                        ->add('list', TextType::class, [
                            'constraints' => [
                                new NotBlank()
                            ],
                        ])
                        ->add('label', TextType::class, [
                            'constraints' => [
                                new NotBlank()
                            ],
                        ])
                        ->add('value', TextType::class, [
                            'constraints' => [
                                new NotBlank()
                            ],
                        ])
                        ->add('colorClass', ChoiceType::class, [
                            'choices'  => [
                                'Dark' => 'text-dark',
                                'White' => 'text-white',
                                'Primary' => 'text-primary',
                                'Secondary' => 'text-secondary',
                                'Success' => 'text-success',
                                'Danger' => 'text-danger',
                                'Warning' => 'text-warning',
                                'Info' => 'text-info',
                                'Light' => 'text-light',
                                'Muted' => 'text-muted'
                            ],
                            'constraints' => [
                                new NotBlank()
                            ],
                        ])
                        ->add('bgClass', ChoiceType::class, [
                            'choices'  => [
                                'Transparent' => '',
                                'White' => 'bg-white',
                                'Dark' => 'bg-dark',
                                'Primary' => 'bg-primary',
                                'Secondary' => 'bg-secondary',
                                'Success' => 'bg-success',
                                'Danger' => 'bg-danger',
                                'Warning' => 'bg-warning',
                                'Info' => 'bg-info',
                                'Light' => 'bg-light',
                                'Muted' => 'bg-muted'
                            ]
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
                    ->add('list', HiddenType::class)
                    ->add('label', HiddenType::class)
                    ->add('value', HiddenType::class)
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
            'data_class' => Listing::class,
        ]);
    }
}
