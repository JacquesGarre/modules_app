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
            ]);

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
                $module = $event->getData();
                $form = $event->getForm();
                if (!$module || null === $module->getId()) {
                    $form->add('sqlTable', TextType::class, [
                        'constraints' => [
                            new NotBlank(),
                            new Regex('/^[A-Za-z0-9_]*$/')
                        ],
                    ]);
                }

                $form->add('Save', ButtonType::class, [
                    'attr' => [
                        'class' => 'btn-primary float-end',
                        'data-action' => 'form#submit',
                        'data-form-target' => 'submitBtn'
                    ],
                ]);

            });

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Module::class,
        ]);
    }
}
