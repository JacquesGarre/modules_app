<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ModuleRepository;

class AdminController extends AbstractController
{
    #[Route('/administration', name: 'app_admin')]
    public function index(ModuleRepository $moduleRepository): Response
    {
        $modules = $moduleRepository->findAll();

        return $this->render('admin/index.html.twig', [
            'modules' => $modules,
        ]);
    }
}
