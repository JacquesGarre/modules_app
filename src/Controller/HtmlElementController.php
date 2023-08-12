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


class HtmlElementController extends AbstractController
{
    #[Route('/administration/htmlelements/add/{pageId}/{parentId?}', name: 'app_htmlelement_add')]
    public function add(
        int $pageId, 
        ModalFormService $modal, 
        Request $request, 
        PageRepository $pageRepository, 
        HtmlElementRepository $htmlElementRepository,
        $parentId = null
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
    public function edit(ModalFormService $modal, Request $request, int $id, HtmlElementRepository $htmlelementRepository): Response
    {
        $htmlelement = $htmlelementRepository->findOneBy(['id' => $id]);
        $params = ['id' => $id];

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
