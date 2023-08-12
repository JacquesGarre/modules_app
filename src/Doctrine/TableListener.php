<?php

namespace App\Doctrine;

use App\Entity\Table;
use App\Service\DataService;

class TableListener
{
    public function __construct(private DataService $dataService)
    {
        $this->dataService = $dataService;
    }

    public function postLoad(Table $table)
    {
        $data = $this->dataService->get(
            $table->getModule()->getSqlTable(),
            $table->getColumnsNames()
        );
        $table->setData($data);
    }
}
