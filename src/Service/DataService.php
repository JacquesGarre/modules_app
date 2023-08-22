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

    public function getColumnsAndValues($args)
    {
        $columns = [];
        $values = [];
        foreach($args as $key => $value){
            $columns[] = $key;
            $values[] = is_array($value) ? json_encode($value) : $value;
        }
        return [$columns, $values];
    }

    public function getSqlConditions($conditions)
    {
        $conditions = array_filter($conditions);

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
        return $conds;
    }

    public function get($table, array $selectedColumns = [], array $conditions = [], $limit = null, $page = null)
    {
        $conn = $this->em->getConnection();
        $selectedColumns = empty($selectedColumns) ? '*' : '`id`, `'.implode('`,`', $selectedColumns).'`';
        $conds = $this->getSqlConditions($conditions);

        $where = empty($conds) ? '' : 'WHERE '.implode(' AND ', $conds);
        $offset = $page > 1 ? "OFFSET ".(intval($page) - 1) * intval($limit) : '';
        $limit = empty($limit) ? '' : "LIMIT $limit";

        $sql = "SELECT $selectedColumns FROM $table $where $limit $offset";
        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery();
        return $result->fetchAllAssociative();
    }

    public function getOneBy($table, array $selectedColumns = [], array $conditions = [])
    {
        $results = $this->get($table, $selectedColumns, $conditions, 1);
        if(!empty($results)){
            return $results[0];
        }
        return false;
    }

    public function insert($table, $args)
    {
        [$columns, $values] = $this->getColumnsAndValues($args);
        $conn = $this->em->getConnection();
        $sql = "INSERT INTO $table (".implode(',',$columns).") VALUES ('".implode("','",$values)."')";
        $stmt = $conn->prepare($sql);
        $stmt->executeQuery();
    }

    public function delete($table, $id)
    {
        $conn = $this->em->getConnection();
        $sql = "DELETE FROM $table WHERE `id` = $id";
        $stmt = $conn->prepare($sql);
        $stmt->executeQuery();
    }

    public function update($table, $args, $conditions)
    {
        [$columns, $values] = $this->getColumnsAndValues($args);
        $conn = $this->em->getConnection();

        $fieldValues = array_combine($columns, $values);
        $values = [];
        foreach($fieldValues as $column => $value){
            $values[] = "$column = '$value'";
        }

        $conds = $this->getSqlConditions($conditions);
        $where = empty($conds) ? '' : 'WHERE '.implode(' AND ', $conds);

        $sql = "UPDATE $table SET ".implode(', ',$values)." $where";
        $stmt = $conn->prepare($sql);
        $stmt->executeQuery();
    }


    public function getTotal($table, array $conditions = [])
    {
        $conn = $this->em->getConnection();
        $conds = $this->getSqlConditions($conditions);
        $where = empty($conds) ? '' : 'WHERE '.implode(' AND ', $conds);

        // Test if column doesn't exist already
        $sql = "SELECT COUNT(1) AS TOTAL FROM $table $where";
     
        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery();
        $total = $result->fetchAssociative()['TOTAL'] ?? 0;
        return $total;
    }

}