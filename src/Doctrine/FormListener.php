<?php

namespace App\Doctrine;

use App\Entity\Form;
use App\Service\DataService;
use App\Service\FormService;
use stdClass;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class FormListener
{
    public function __construct(
        private DataService $dataService,
        private FormFactoryInterface $formFactory,
        private UrlGeneratorInterface $router,
        private FormService $formService
    )
    {
        $this->dataService = $dataService;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->formService = $formService;
    }

    public function postLoad(Form $formEntity)
    {
        $entity = new stdClass();
        $form = $this->formService->getEntityForm(
            $formEntity->getModule()->getSqlTable(), 
            $entity, 
            $formEntity
        );
        $formEntity->setHtml($form->createView());

    }
}
