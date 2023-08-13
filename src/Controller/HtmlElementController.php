<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ModalFormService;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\HtmlElementRepository;
use App\Repository\PageRepository;
use App\Entity\HtmlElement;
use App\Service\FormService;
use Symfony\Component\PropertyAccess\PropertyAccess;

class HtmlElementController extends AbstractController
{
    #[Route('/administration/htmlelements/add/{pageId}/{parentId?}', name: 'app_htmlelement_add')]
    public function add(
        int $pageId, 
        ModalFormService $modal, 
        Request $request, 
        PageRepository $pageRepository, 
        HtmlElementRepository $htmlElementRepository,
        FormService $formService,
        $parentId = null,
    ): Response
    {
        $page = $pageRepository->findOneBy(['id' => $pageId]);

        $htmlelement = new HtmlElement();
        $htmlelement->setPage($page);

        $params = [
            'pageId' => $pageId
        ];

        if(!empty($parentId)){
            $parent = $htmlElementRepository->findOneBy(['id' => $parentId]);
            $htmlelement->setParent($parent);
            $params['parentId'] = $parentId;
        }

        if($request->query->get('onchange')){
        
            if(!empty($request->request->all()['htmlelement'])){
                $formValues = $request->request->all()['htmlelement'];
                $htmlelement->setType($formValues['type']);
                $htmlelement->setSizeClass($formValues['sizeClass']);
                $htmlelement->setAdditionnalClasses($formValues['additionnalClasses']);
            }

            $form = $formService->getForm('htmlelement', 'app_htmlelement_add', $htmlelement, 'POST', $params, 'write', true);
            return $this->render('includes/_form.html.twig', [
                'form' => $form,
            ]);
        }

        return $modal->show(
            $title = 'Create a new html element in page '.$page->getTitle(),
            $class = 'htmlelement',
            $route = 'app_htmlelement_add',
            $request,
            $htmlelement,
            'POST',
            $params
        );
    }

    #[Route('/administration/htmlelements/{id}/edit', name: 'app_htmlelement_edit')]
    public function edit(
        ModalFormService $modal, 
        Request $request, 
        int $id, 
        HtmlElementRepository $htmlelementRepository,
        FormService $formService    
    ): Response
    {
        $htmlelement = $htmlelementRepository->findOneBy(['id' => $id]);
        $params = ['id' => $id];

        if($request->query->get('onchange')){
            $formValues = $request->request->all()['htmlelement'];
            foreach ($formValues as $key => $value) {
                $setterMethod = 'set' . ucfirst($key);
                if (method_exists($htmlelement, $setterMethod)) {
                    $htmlelement->{$setterMethod}($value);
                }
            }
            $form = $formService->getForm('htmlelement', 'app_htmlelement_edit', $htmlelement, 'POST', $params, 'write', true);
            return $this->render('includes/_form.html.twig', [
                'form' => $form,
            ]);

        }

        return $modal->show(
            $title = 'Edit html element '.$htmlelement->getType(),
            $class = 'htmlelement',
            $route = 'app_htmlelement_edit',
            $request,
            $htmlelement,
            'POST',
            $params
        );
    }

    #[Route('/administration/htmlelements/{id}/delete', name: 'app_htmlelement_delete')]
    public function delete(ModalFormService $modal, Request $request, int $id, HtmlElementRepository $htmlelementRepository): Response
    {
        $htmlelement = $htmlelementRepository->findOneBy(['id' => $id]);
        $params = ['id' => $id];
        return $modal->show(
            $title = 'Delete html element '.$htmlelement->getType(),
            $class = 'htmlelement',
            $route = 'app_htmlelement_delete',
            $request,
            $htmlelement,
            $method = 'DELETE',
            $params
        );
    }
}
