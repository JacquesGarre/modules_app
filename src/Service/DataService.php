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
use App\Repository\ModuleRepository;

class DataService
{
    public function __construct(
        private EntityManagerInterface $em,
        private ModuleRepository $moduleRepository
    )
    {
        $this->em = $em;
        $this->moduleRepository = $moduleRepository;
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

    public function getSqlConditions($conditions, $module)
    {   

        $conditions = array_filter($conditions);

        $fieldNames = array_map(function($field){
            return $field->getName();
        }, $module->getFields()->toArray());

        $fields = array_combine($fieldNames, $module->getFields()->toArray());
        $conds = [];

        foreach($conditions as $field => $value){
            if(!array_key_exists($field, $fields)){
                $conds[] = $field." = '".$value."'";
            } else {
                switch($fields[$field]->getType()){
                    case 'text':
                        $conds[] = "`t`.`$field` LIKE '%".$value."%'";
                    break;
                    case 'listing':
                        if($fields[$field]->isMultiple()){
                            if(is_array($value)){
                                $conds[] = "`t`.`$field` RLIKE '".implode('|', $value)."'";
                            } else {
                                $conds[] = "`t`.`$field` LIKE '%".$value."%'";
                            }
                        } else {
                            if(is_array($value)){
                                $conds[] = "`t`.`$field` RLIKE '".implode('|', $value)."'";
                            } else {
                                $conds[] = "`t`.`$field` LIKE '".$value."'";
                            }
                        }

                    break;
                    case 'manytoone':
                        $conds[] = "`t`.`$field` = '".$value."'";
                    break;
                    case 'manytomany':
                        $conds[] = "`t`.`$field` LIKE '%".$value."%'";
                    break;
                }
            }

        }        
        return $conds;
    }

    public function getTotal($table, array $conditions = [])
    {

        $module = $this->moduleRepository->findOneBy(['sqlTable' => $table]);
        $moduleFieldIDS = [];
        $selectedColumns = [];
        foreach($module->getFields() as $field){
            $selectedColumns[] = $field->getName();
            $moduleFieldIDS[$field->getName()] = $field;
        }

        $conn = $this->em->getConnection();
        $conds = $this->getSqlConditions($conditions, $module);
        $where = empty($conds) ? '' : 'WHERE '.implode(' AND ', $conds);


        $allColumns = [];
        $currentTableColumns = [];
        $externalTableColumns = [];
        foreach($selectedColumns as $fieldID){
            $allColumns[] = $fieldID;
            $field = $moduleFieldIDS[$fieldID];
            if($field->getType() !== 'manytomany'){
                $currentTableColumns[] = "$table.`$fieldID` as $fieldID";
            } else {
                $externalTableColumns[$fieldID] = $field;
            }
        }

        $currentTableColumns = empty($currentTableColumns) ? '*' : "`$table`.`id` as id, ".implode(',', $currentTableColumns);

        $groupConcats = [];
        $leftJoins = [];
        foreach($externalTableColumns as $fieldID => $field){
            $currentTable = $table;
            $currentTableKey = $currentTable.'ID';
            $foreignTable = $field->getEntity()->getSqlTable();
            $foreignTableKey = $foreignTable.'ID';
            $externalTable = $currentTable.'_to_'.$foreignTable;
            $groupConcats[] = "CONCAT('[\"', GROUP_CONCAT($externalTable.$foreignTableKey SEPARATOR '\",\"'),'\"]') as $fieldID ";
            $leftJoins[] = "LEFT JOIN $externalTable ON $externalTable.$currentTableKey = $table.id";
        }

        $columns = !empty($allColumns) ? "`id`, `".implode("`,`", $allColumns)."`" : "*";

        $sql = "SELECT $columns, COUNT(1) AS TOTAL FROM (";
        $sql .= "SELECT $currentTableColumns ".(!empty($groupConcats) ? ','.implode(",", $groupConcats) : '');
        $sql .= " FROM $table ".(!empty($leftJoins) ? implode(",", $leftJoins) : '')." ";
        $sql .= " GROUP BY $table.`id` ";
        $sql .= ") t ";
        $sql .= $where;


        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery();
        $total = $result->fetchAssociative()['TOTAL'] ?? 0;

        return $total;
    }

    public function get($table, array $selectedColumns = [], array $conditions = [], $limit = null, $page = null)
    {

        $module = $this->moduleRepository->findOneBy(['sqlTable' => $table]);
        $moduleFieldIDS = [];
        foreach($module->getFields() as $field){
            $moduleFieldIDS[$field->getName()] = $field;           
        }

        $selectedColumns = $selectedColumns ?: array_keys($moduleFieldIDS);

        $conn = $this->em->getConnection();
        $conds = $this->getSqlConditions($conditions, $module);
        $where = empty($conds) ? '' : 'WHERE '.implode(' AND ', $conds);
        $offset = $page > 1 ? "OFFSET ".(intval($page) - 1) * intval($limit) : '';
        $limit = empty($limit) ? '' : "LIMIT $limit";

        $allColumns = [];
        $currentTableColumns = [];
        $externalTableColumns = [];
        foreach($selectedColumns as $fieldID){
            $allColumns[] = $fieldID;
            $field = $moduleFieldIDS[$fieldID];
            if($field->getType() !== 'manytomany'){
                $currentTableColumns[] = "$table.`$fieldID` as $fieldID";
            } else {
                $externalTableColumns[$fieldID] = $field;
            }
        }

        $currentTableColumns = empty($currentTableColumns) ? '*' : "`$table`.`id` as id, ".implode(',', $currentTableColumns);

        $groupConcats = [];
        $leftJoins = [];
        foreach($externalTableColumns as $fieldID => $field){
            $currentTable = $table;
            $currentTableKey = $currentTable.'ID';
            $foreignTable = $field->getEntity()->getSqlTable();
            $foreignTableKey = $foreignTable.'ID';
            $externalTable = $currentTable.'_to_'.$foreignTable;
            $groupConcats[] = "CONCAT('[\"', GROUP_CONCAT($externalTable.$foreignTableKey SEPARATOR '\",\"'),'\"]') as $fieldID ";
            $leftJoins[] = "LEFT JOIN $externalTable ON $externalTable.$currentTableKey = $table.id";
        }

        $columns = !empty($allColumns) ? "`id`, `".implode("`,`", $allColumns)."`" : "*";

        $sql = "SELECT $columns FROM (";
        $sql .= "SELECT $currentTableColumns ".(!empty($groupConcats) ? ','.implode(",", $groupConcats) : '');
        $sql .= " FROM $table ".(!empty($leftJoins) ? implode(",", $leftJoins) : '')." ";
        $sql .= " GROUP BY $table.`id` ";
        $sql .= ") t ";
        $sql .= $where;
        $sql .= "ORDER BY t.`id` DESC $limit $offset";



        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery();
        $results = $result->fetchAllAssociative();

        foreach($results as $key => $result){
            $results[$key]['titlePattern'] = $module->getPattern();
            foreach($result as $fieldID => $value){
                $results[$key]['titlePattern'] = str_replace($fieldID, $value, $results[$key]['titlePattern']);
            }
        }
        return $results;
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

        // Get many to many relationships
        $module = $this->moduleRepository->findOneBy(['sqlTable' => $table]);
        $manyToMany = [];
        foreach($module->getFields() as $field){
            if($field->getType() == 'manytomany' && in_array($field->getName(), $columns)){
                $index = array_search($field->getName(), $columns);
                $value = $values[$index];
                unset($columns[$index]);
                unset($values[$index]);
                $manyToMany[$field->getName()] = [
                    'field' => $field,
                    'ids' => json_decode($value)
                ];
            }
        }

        // Insert normal entity
        $conn = $this->em->getConnection();
        $sql = "INSERT INTO $table (".implode(',',$columns).") VALUES ('".implode("','",$values)."')";
        $stmt = $conn->prepare($sql);
        $stmt->executeQuery();

        // Insert many to many relations
        $id = $conn->lastInsertId();
        foreach($manyToMany as $fieldID => $data){
            $externalIDS = $data['ids'];
            $field = $data['field'];
            $currentTable = $field->getModule()->getSqlTable();
            $currentTableKey = $currentTable.'ID';
            $foreignTable = $field->getEntity()->getSqlTable();
            $foreignTableKey = $foreignTable.'ID';
            $table = $currentTable.'_to_'.$foreignTable;
            foreach($externalIDS as $externalID){
                $sql = "INSERT INTO $table (`id`, $currentTableKey, $foreignTableKey) VALUES (NULL, '$id', '$externalID')";
                $stmt = $conn->prepare($sql);
                $stmt->executeQuery();
            }
        }


    }

    public function delete($table, $id)
    {
        $conn = $this->em->getConnection();
        $sql = "DELETE FROM $table WHERE `id` = $id";
        $stmt = $conn->prepare($sql);
        $stmt->executeQuery();
    }

    public function update($table, $args, $id)
    {
        
        [$columns, $values] = $this->getColumnsAndValues($args);

        // Get many to many relationships
        $module = $this->moduleRepository->findOneBy(['sqlTable' => $table]);
        $manyToMany = [];
        foreach($module->getFields() as $field){
            if($field->getType() == 'manytomany' && in_array($field->getName(), $columns)){
                $index = array_search($field->getName(), $columns);
                $value = $values[$index];
                unset($columns[$index]);
                unset($values[$index]);
                $manyToMany[$field->getName()] = [
                    'field' => $field,
                    'ids' => json_decode($value)
                ];
            }
        }

        // Update normal entity
        $conn = $this->em->getConnection();
        $fieldValues = array_combine($columns, $values);
        $values = [];
        foreach($fieldValues as $column => $value){
            $values[] = "$column = '$value'";
        }
        $sql = "UPDATE $table SET ".implode(', ',$values)." WHERE `id` = $id";
        $stmt = $conn->prepare($sql);
        $stmt->executeQuery();

        // Update many to many relationships
        foreach($manyToMany as $fieldID => $data){
            $externalIDS = $data['ids'];
            $field = $data['field'];
            $currentTable = $field->getModule()->getSqlTable();
            $currentTableKey = $currentTable.'ID';
            $foreignTable = $field->getEntity()->getSqlTable();
            $foreignTableKey = $foreignTable.'ID';
            $table = $currentTable.'_to_'.$foreignTable;

            // Delete old relations
            $sql = "DELETE FROM $table WHERE $currentTableKey = $id";
            $stmt = $conn->prepare($sql);
            $stmt->executeQuery();

            // Insert new ones
            foreach($externalIDS as $externalID){
                $sql = "INSERT INTO $table (`id`, $currentTableKey, $foreignTableKey) VALUES (NULL, '$id', '$externalID')";
                $stmt = $conn->prepare($sql);
                $stmt->executeQuery();
            }
        }

    }


 

}