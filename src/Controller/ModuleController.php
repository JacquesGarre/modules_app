<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ModalFormService;
use Symfony\Component\HttpFoundation\Request;

class ModuleController extends AbstractController
{
    #[Route('/administration/modules/add', name: 'app_module_add')]
    public function add(ModalFormService $modal, Request $request): Response
    {
        return $modal->show(
            $title = 'Create a new module',
            $class = 'module',
            $route = 'app_module_add',
            $request
        );
    }
}
