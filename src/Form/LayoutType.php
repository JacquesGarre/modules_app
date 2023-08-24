<?php

namespace App\Form;

use App\Entity\Layout;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
class LayoutType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) use ($options): void {

                $layout = $event->getData();

                $form = $event->getForm();

                $method = $event->getForm()->getConfig()->getMethod();

                if($method !== 'DELETE'){
                    
                    $form
                        ->add('title', TextType::class, [
                            'disabled' => $options['attr']['data-mode'] == 'read'   
                        ]);
                
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
                        ->add('title', HiddenType::class);
                    $form
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
            'data_class' => Layout::class,
            "allow_extra_fields" => true
        ]);
    }
}
