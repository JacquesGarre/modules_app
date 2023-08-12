<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;
use Exception;
use App\Entity\Module;

class DataService
{
    public function __construct(private EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function get($table, array $selectedColumns = [], array $conditions = [], $limit = null, $page = null)
    {
        $conn = $this->em->getConnection();

        $selectedColumns = empty($selectedColumns) ? '*' : '`id`, `'.implode('`,`', $selectedColumns).'`';

        $conds = [];
        foreach($conditions as $field => $value){
            if(is_array($value)){
                $conds[] = $field." RLIKE '".implode('|', $value)."'";
            } else if(is_int($value)){
                $conds[] = $field." = ".$value;
            } else {
                $conds[] = $field." LIKE '".$value."'";
            }
        }
        $where = empty($conds) ? '' : implode(' AND ', $conds);
        $limit = empty($limit) ? '' : "LIMIT $limit";

        // Test if column doesn't exist already
        $sql = "SELECT $selectedColumns FROM $table $where $limit";
        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery();
        return $result->fetchAllAssociative();
    }

}