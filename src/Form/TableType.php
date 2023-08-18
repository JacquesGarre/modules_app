<?php

namespace App\Form;

use App\Entity\Table;
use App\Entity\Field;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Repository\FieldRepository;

class TableType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
                $table = $event->getData();
                $form = $event->getForm();

                $method = $event->getForm()->getConfig()->getMethod();
                
                if($method !== 'DELETE'){

                    $form->add('title', TextType::class, [
                            'constraints' => [
                                new NotBlank()
                            ],
                        ])
                        ->add('columns', EntityType::class, [
                            'label'         => 'Columns',
                            'expanded'      => true,
                            'class'         => Field::class,
                            'query_builder' => function (FieldRepository $repository) use($table) {
                                return $repository
                                    ->createQueryBuilder('f')
                                    ->where('f.module = :moduleId')
                                    ->setParameter('moduleId', $table->getModule()->getId());
                            },
                            'multiple' => true
                        ])
                        ->add('inlineActions', ChoiceType::class, array(
                            'label' => 'Actions',
                            'expanded'  => true,
                            'multiple'  => true,
                            'choices' => [
                                'View' => 'view',
                                'Edit' => 'edit',
                                'Delete' => 'delete'
                            ]
                        ))
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
            'data_class' => Table::class,
            "allow_extra_fields" => true
        ]);
    }
}
