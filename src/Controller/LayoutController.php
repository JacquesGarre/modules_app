<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ModalFormService;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\PageRepository;
use App\Repository\ModuleRepository;
use App\Repository\LayoutRepository;
use App\Entity\Page;
use App\Entity\Layout;
use App\Service\FormService;

class LayoutController extends AbstractController
{
    #[Route('/administration/layouts', name: 'app_layout_index')]
    public function index(LayoutRepository $layoutRepository): Response
    {
        $layouts = $layoutRepository->findAll();
        return $this->render('layout/_table.html.twig', [
            'layouts' => $layouts,
        ]);
    }


    #[Route('/administration/layouts/add', name: 'app_layout_add')]
    public function add(ModalFormService $modal, Request $request): Response
    {
        $layout = new Layout();
        $params = [];
        return $modal->show(
            $title = 'Create a new layout',
            $class = 'layout',
            $route = 'app_layout_add',
            $request,
            $layout,
            'POST',
            $params
        );
    }

    #[Route('/administration/layouts/{id}/edit', name: 'app_layout_edit')]
    public function edit(ModalFormService $modal, Request $request, int $id, LayoutRepository $layoutRepository): Response
    {
        $layout = $layoutRepository->findOneBy(['id' => $id]);
        $params = ['id' => $id];

        return $modal->show(
            $title = 'Edit layout '.$layout->getTitle(),
            $class = 'layout',
            $route = 'app_layout_edit',
            $request,
            $layout,
            'POST',
            $params
        );
    }

    #[Route('/administration/layouts/{id}/delete', name: 'app_layout_delete')]
    public function delete(ModalFormService $modal, Request $request, int $id, LayoutRepository $layoutRepository): Response
    {
        $layout = $layoutRepository->findOneBy(['id' => $id]);
        $params = ['id' => $id];
        return $modal->show(
            $title = 'Delete layout '.$layout->getTitle(),
            $class = 'layout',
            $route = 'app_layout_delete',
            $request,
            $layout,
            $method = 'DELETE',
            $params
        );
    }

    #[Route('/administration/layouts/{id}', name: 'app_layout_show')]
    public function show(
        int $id,
        ModalFormService $modal,
        Request $request,
        LayoutRepository $layoutRepository,
        FormService $formService
    ): Response
    {
        $layout = $layoutRepository->findOneBy(['id' => $id]);

        $params = [];
        $params['id'] = $layout->getId();


        $form = $formService->getForm('layout', 'app_layout_show', $layout, 'POST', $params, 'read');
        if($request->query->get('enable') == 1){
            $form = $formService->getForm('layout', 'app_layout_show', $layout, 'POST', $params, 'write');
        }
        if($request->query->get('enable') == 1 || $request->query->get('disable') == 1){
            return $this->render('form/form.html.twig', [
                'form' => $form
            ]);
        }
        
        if($request->query->get('ajax')){
            return $this->render('layout/_layout_builder.html.twig', [
                'layout' => $layout,
            ]);
        }
        
        if ($request->isMethod('POST')) {
            $form = $formService->getForm('layout', 'app_layout_show', $layout, 'POST', $params, 'write');
            $form->handleRequest($request);
            return $modal->handlePostRequest($form, $layout);
        }
    
        return $this->render('layout/show.html.twig', [
            'layout' => $layout,
            'form' => $form,
        ]);
    }
}
