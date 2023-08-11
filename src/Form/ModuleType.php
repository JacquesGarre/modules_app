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
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class ModuleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {

                $module = $event->getData();
                $form = $event->getForm();

                $method = $event->getForm()->getConfig()->getMethod();

                if($method !== 'DELETE'){

                    $form
                        ->add('labelPlural', TextType::class, [
                            'constraints' => [
                                new NotBlank()
                            ],
                        ])
                        ->add('labelSingular', TextType::class, [
                            'constraints' => [
                                new NotBlank()
                            ],
                        ]);

                    
                    if (!$module || null === $module->getId()) {
                        $form->add('sqlTable', TextType::class, [
                            'constraints' => [
                                new NotBlank(),
                                new Regex('/^[A-Za-z0-9_]*$/')
                            ],
                        ]);
                    }

                    $form->add('Submit', ButtonType::class, [
                        'attr' => [
                            'class' => 'btn-primary float-end',
                            'data-action' => 'form#submit',
                            'data-form-target' => 'submitBtn'
                        ],
                    ]);


                } else {

                    $form
                        ->add('labelPlural', HiddenType::class)
                        ->add('labelSingular', HiddenType::class)
                        ->add('sqlTable', HiddenType::class)
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
            'data_class' => Module::class,
        ]);
    }
}
