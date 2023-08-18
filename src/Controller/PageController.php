<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ModalFormService;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\PageRepository;
use App\Repository\ModuleRepository;
use App\Entity\Page;
use App\Service\FormService;

class PageController extends AbstractController
{
    #[Route('/administration/pages', name: 'app_page_index')]
    public function index(PageRepository $pageRepository): Response
    {
        $pages = $pageRepository->findAll();
        return $this->render('page/_table.html.twig', [
            'pages' => $pages,
        ]);
    }

    #[Route('/administration/pages/add', name: 'app_page_add')]
    public function add(ModalFormService $modal, Request $request): Response
    {
        $page = new Page();
        $params = [];
        return $modal->show(
            $title = 'Create a new page for ',
            $class = 'page',
            $route = 'app_page_add',
            $request,
            $page,
            'POST',
            $params
        );
    }

    #[Route('/administration/pages/{id}/edit', name: 'app_page_edit')]
    public function edit(ModalFormService $modal, Request $request, int $id, PageRepository $pageRepository): Response
    {
        $page = $pageRepository->findOneBy(['id' => $id]);
        $params = ['id' => $id];

        return $modal->show(
            $title = 'Edit page '.$page->getTitle(),
            $class = 'page',
            $route = 'app_page_edit',
            $request,
            $page,
            'POST',
            $params
        );
    }

    #[Route('/administration/pages/{id}/delete', name: 'app_page_delete')]
    public function delete(ModalFormService $modal, Request $request, int $id, PageRepository $pageRepository): Response
    {
        $page = $pageRepository->findOneBy(['id' => $id]);
        $params = ['id' => $id];
        return $modal->show(
            $title = 'Delete page '.$page->getTitle(),
            $class = 'page',
            $route = 'app_page_delete',
            $request,
            $page,
            $method = 'DELETE',
            $params
        );
    }

    #[Route('/administration/pages/{id}', name: 'app_page_show')]
    public function show(
        int $id,
        ModalFormService $modal,
        Request $request,
        PageRepository $pageRepository,
        FormService $formService
    ): Response
    {
        $page = $pageRepository->findOneBy(['id' => $id]);

        $params = [];
        $params['id'] = $page->getId();


        $form = $formService->getForm('page', 'app_page_show', $page, 'POST', $params, 'read');
        if($request->query->get('enable') == 1){
            $form = $formService->getForm('page', 'app_page_show', $page, 'POST', $params, 'write');
        }
        if($request->query->get('enable') == 1 || $request->query->get('disable') == 1){
            return $this->render('form/form.html.twig', [
                'form' => $form
            ]);
        }
        
        
        if($request->query->get('ajax')){
            return $this->render('page/_page_builder.html.twig', [
                'page' => $page,
            ]);
        }
        
        if ($request->isMethod('POST')) {
            $form = $formService->getForm('page', 'app_page_show', $page, 'POST', $params, 'write');
            $form->handleRequest($request);
            return $modal->handlePostRequest($form, $page);
        }
    
        return $this->render('page/show.html.twig', [
            'page' => $page,
            'form' => $form,
        ]);
    }
}
