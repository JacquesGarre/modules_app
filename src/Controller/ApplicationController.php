<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ModuleRepository;
use App\Repository\ListingRepository;
use App\Repository\PageRepository;
use App\Service\ModalFormService;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\FieldRepository;
use App\Entity\Field;
use App\Repository\FormRepository;
use App\Repository\TableRepository;
use App\Service\DataService;
use App\Service\FormService;
use Exception;
use stdClass;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
class ApplicationController extends AbstractController
{
    #[Route('/{table}/add', name: 'app_application_add_entity')]
    public function add(
        ModalFormService $modal, 
        Request $request, 
        string $table, 
        ModuleRepository $moduleRepository, 
        DataService $dataService,
        FormRepository $formRepository
    ): Response
    {
        $module = $moduleRepository->findOneBy(['sqlTable' => $table]);
        $entity = new stdClass();

        $formEntity = $formRepository->findOneBy([
            'action' => 'add',
            'module' => $module
        ]);

        if(empty($formEntity)){
            throw new Exception('Please create a form with action "add" on entity '.$module->getLabelSingular());
        }

        $params = [
            'table' => $table,
        ];

        return $modal->show(
            $title = 'Add '.$module->getLabelSingular(),
            $class = $table,
            $route = 'app_application_add_entity',
            $request,
            $entity,
            'POST',
            $params, 
            $formEntity
        );
    }

    #[Route('/{table}/edit/{id}', name: 'app_application_edit_entity')]
    public function edit(
        ModalFormService $modal, 
        Request $request, 
        string $table, 
        int $id, 
        ModuleRepository $moduleRepository, 
        DataService $dataService,
        FormRepository $formRepository
    ): Response
    {
        $module = $moduleRepository->findOneBy(['sqlTable' => $table]);
        $entity = $dataService->get($table, [], ['id' => $id])[0];

        $formEntity = $formRepository->findOneBy([
            'action' => 'edit',
            'module' => $module
        ]);

        if(empty($formEntity)){
            throw new Exception('Please create a form with action "edit" on entity '.$module->getLabelSingular());
        }

        $params = [
            'table' => $table,
            'id' => $id
        ];

        return $modal->show(
            $title = 'Edit '.$module->getLabelSingular(),
            $class = $table,
            $route = 'app_application_edit_entity',
            $request,
            $entity,
            'POST',
            $params, 
            $formEntity
        );
    }

    #[Route('/{table}/delete/{id}', name: 'app_application_delete_entity')]
    public function delete(
        ModalFormService $modal,
        Request $request,
        string $table,
        int $id,
        ModuleRepository $moduleRepository,
        DataService $dataService,
        FormRepository $formRepository
    ): Response
    {
        $module = $moduleRepository->findOneBy(['sqlTable' => $table]);
        $entity = $dataService->getOneBy($table, [], ['id' => $id]);
        $params = [
            'moduleId' => $module->getId(),
            'id' => $id
        ];

        return $modal->show(
            $title = 'Delete '.$module->getLabelSingular(),
            $class = $table,
            $route = 'app_data_delete',
            $request,
            $entity,
            'POST',
            $params
        );
    }

    #[Route('/table_reload/{id}', name: 'app_application_table_reload')]
    public function tableReload(
        int $id,
        TableRepository $tableRepository,
    ): Response
    {
        $table = $tableRepository->findOneBy(['id' => $id]);
        return $this->render('_application/components/_table.html.twig', [
            'table' => $table
        ]);
    }

    #[Route('/form_reload/{id}', name: 'app_application_form_reload')]
    public function formReload(
        int $id,
        FormRepository $formRepository,
        FormService $formService,
        Request $request,
        UrlGeneratorInterface $router,
        PageRepository $pageRepository,
        DataService $dataService
    ): Response
    {

        $formEntity = $formRepository->findOneBy(['id' => $id]);
        $entity = new stdClass();


        $referer = $request->headers->get('referer'); // get the referer, it can be empty!
        if (!\is_string($referer) || !$referer) {
            echo 'Referer is invalid or empty.';
        }
        $refererPathInfo = Request::create($referer)->getPathInfo();

     
        // try to match the path with the application routing
        $routeInfos = $router->match($refererPathInfo);
        if(
            !empty($routeInfos['_route'])
            && $routeInfos['_route'] == 'app_application_page'
            && $formEntity->getAction() == 'edit'
            && !empty($routeInfos['id'])
            && !empty($routeInfos['uri'])
        ){
            $uri = $routeInfos['uri'];
            if(!empty($routeInfos['id'])){
                $uri .= '/{id}';
            }
            $page = $pageRepository->findOneBy(['uri' => $uri]);
            $module = $page->getModule();
            if(!empty($module)){
                $data = $dataService->getOneBy(
                    $module->getSqlTable(),
                    [],
                    ['id' => $routeInfos['id']]
                );
                if(!empty($data)){
                    foreach($data as $fieldID => $value){
                        $entity->{$fieldID} = $value;
                    }
                }
            }
        }

        $mode = $request->query->get('disable') == 1 ? 'read' : 'write';
        $form = $formService->getEntityForm(
            $formEntity->getModule()->getSqlTable(),
            $entity,
            $formEntity,
            $mode
        );
        $formEntity->setHtml($form->createView());

        return $this->render('form/form.html.twig', [
            'form' => $formEntity
        ]);
    }

    #[Route('/{uri}/{id?}', name: 'app_application_page')]
    public function index(
        string $uri,
        $id = null,
        PageRepository $pageRepository,
        DataService $dataService,
    ): Response
    {
        if(!empty($id)){
            $uri .= '/{id}';
        }
        $page = $pageRepository->findOneBy([
            'uri' => $uri
        ]);
        $entity = false;
        if(!empty($id)){
            $entity = $dataService->getOneBy($page->getModule()->getSqlTable(), [], ['id' => $id]);
        }


        return $this->render('_application/page.html.twig', [
            'page' => $page,
            'entity' => $entity
        ]);
    }

}
