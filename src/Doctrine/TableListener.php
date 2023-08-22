<?php

namespace App\Doctrine;

use App\Entity\Table;
use App\Service\DataService;
use Symfony\Component\HttpFoundation\RequestStack;

class TableListener
{
    private $request;

    public function __construct(
        private DataService $dataService,
        RequestStack $requestStack
        
    )
    {
        $this->dataService = $dataService;
        $this->request = $requestStack->getCurrentRequest();
    }

    public function postLoad(Table $table)
    {
        $page = $this->request->query->get('page') ?? null;

        // set data
        $data = $this->dataService->get(
            $table->getModule()->getSqlTable(),
            $table->getColumnsNames(),
            [],
            $table->getDefaultLimit(),
            $page
        );
        $table->setData($data);

        // set total
        $total = $this->dataService->getTotal(
            $table->getModule()->getSqlTable(),
            []
        );
        $table->setTotal($total);

        // set pages = total / limit
        $table->setPages(round($total / $table->getDefaultLimit()));

    }
}
