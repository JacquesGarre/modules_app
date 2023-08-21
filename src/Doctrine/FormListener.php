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
use Symfony\Component\HttpFoundation\RequestStack;
use App\Repository\PageRepository;

class FormListener
{ 
    private $request;

    public function __construct(
        private DataService $dataService,
        private FormFactoryInterface $formFactory,
        private UrlGeneratorInterface $router,
        private FormService $formService,
        RequestStack $requestStack,
        private PageRepository $pageRepository
    )
    {
        $this->dataService = $dataService;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->formService = $formService;
        $this->request = $requestStack->getCurrentRequest();
        $this->pageRepository = $pageRepository;
    }

    public function postLoad(Form $formEntity)
    {
        $entity = new stdClass();

        // If in detailed view, set values of form
        $mode = 'write';
        if($formEntity->getAction() == 'edit' && $this->request->attributes->get('_controller') == 'App\Controller\ApplicationController::index'){
            $uri = $this->request->attributes->get('uri');
            $id = $this->request->attributes->get('id');
            if(!empty($id)){
                $uri .= '/{id}';
                $page = $this->pageRepository->findOneBy(['uri' => $uri]);
                $module = $page->getModule();
                if(!empty($module)){
                    $data = $this->dataService->getOneBy(
                        $module->getSqlTable(),
                        [],
                        ['id' => $id]
                    );
                    if(!empty($data)){
                        foreach($data as $fieldID => $value){
                            $entity->{$fieldID} = $value;
                        }
                    }
                    $mode = 'read';
                }
            }
        }


        $form = $this->formService->getEntityForm(
            $formEntity->getModule()->getSqlTable(), 
            $entity, 
            $formEntity,
            $mode
        );
        $formEntity->setHtml($form->createView());

    }
}
