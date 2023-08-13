<?php

namespace App\Doctrine;

use App\Entity\Form;
use App\Service\DataService;
use stdClass;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;

class FormListener
{
    public function __construct(
        private DataService $dataService,
        private FormFactoryInterface $formFactory,
    )
    {
        $this->dataService = $dataService;
        $this->formFactory = $formFactory;
    }

    public function postLoad(Form $formEntity)
    {
        
        $entity = new stdClass();

        $form = $this->formFactory->create(
            FormType::class, 
            $entity,
            [   
                'action' => '/'.$formEntity->getAction(),
                'method' => 'POST'
            ]
        );

        foreach($formEntity->getFields() as $field){
            $entity->{$field->getName()} = null;
            switch($field->getType()){
                case 'text':
                    $form->add($field->getName(), TextType::class, [
                        'data' => $field->getValue(),
                        'disabled' => $field->isDisabled(),
                        'required' => $field->isRequired(),
                    ]);
                break;
                case 'listing':
                    $form->add($field->getName(), ChoiceType::class, [
                        'data' => $field->getValue(),
                        'disabled' => $field->isDisabled(),
                        'required' => $field->isRequired(),
                        'choices' => $field->getChoices(),
                        'multiple'  => $field->isMultiple(),
                    ]);
                break;
            }
        }
        switch($formEntity->getAction()){
            case 'add':
                $form->add('save', ButtonType::class, ['label' => 'Create '.$formEntity->getModule()->getLabelSingular()]);
            break;
            case 'edit':
                $form->add('save', ButtonType::class, ['label' => 'Update '.$formEntity->getModule()->getLabelSingular()]);
            break;
        }

        $formEntity->setHtml($form->createView());

    }
}
