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
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Field;
use App\Repository\FieldRepository;

class ModuleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use($options): void {

                $module = $event->getData();
                $form = $event->getForm();

                $method = $event->getForm()->getConfig()->getMethod();

                if($method !== 'DELETE'){


                    $form
                        ->add('labelSingular', TextType::class, [
                            'constraints' => [
                                new NotBlank()
                            ],
                            'disabled' => $options['attr']['data-mode'] == 'read'                            
                        ])
                        ->add('labelPlural', TextType::class, [
                            'constraints' => [
                                new NotBlank()
                            ],
                            'disabled' => $options['attr']['data-mode'] == 'read'   
                        ])
                        ->add('pattern', TextType::class,[
                            'disabled' => $options['attr']['data-mode'] == 'read'   
                        ]);
                        
                    if (!$module || null === $module->getId()) {
                        $form->add('sqlTable', TextType::class, [
                            'constraints' => [
                                new NotBlank(),
                                new Regex('/^[A-Za-z0-9_]*$/')
                            ],
                            'disabled' => $options['attr']['data-mode'] == 'read'   
                        ]);
                    }

                    $label = $options['attr']['data-mode'] == 'read'  ? 'Edit' : 'Submit';
                    $action = $options['attr']['data-mode'] == 'read'  ? 'form#enable' : 'form#submit';

                    $form
                        ->add($label, ButtonType::class, [
                            'attr' => [
                                'class' => 'btn-primary float-end',
                                'data-action' => $action,
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
