<?php

namespace App\EventListener;

use App\Entity\Field;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreRemoveEventArgs;
use Exception;

class FieldEventListener
{

    public function prePersist(Field $field, PrePersistEventArgs $args): void
    {
        $conn = $args->getObjectManager()->getConnection();

        $table = $field->getModule()->getSqlTable();
        $column = $field->getName();

        if($field->getType() !== 'manytomany'){

            // Test if column doesn't exist already
            $sql = "SHOW COLUMNS FROM `$table` LIKE '$column'";
            $stmt = $conn->prepare($sql);
            $result = $stmt->executeQuery();
            if(!empty($result->fetchAllAssociative())){
                throw new Exception("Sql column $column already exists in $table");
            }

            // define sql type depending on field type
            switch($field->getType()){
                case 'text':
                case 'select':
                    $type = "VARCHAR(255)";
                break;
                default:
                    $type = "VARCHAR(255)";
            }

            $sql = "ALTER TABLE $table ADD $column $type;";
            $stmt = $conn->prepare($sql);
            $stmt->execute();

        } else {

            $this->createRelationnalTable($conn, $field);

        }

    }

    public function createRelationnalTable($conn, Field $field)
    {       
        $currentTable = $field->getModule()->getSqlTable();
        $currentTableKey = $currentTable.'ID';
        $foreignTable = $field->getEntity()->getSqlTable();
        $foreignTableKey = $foreignTable.'ID';
        $table = $currentTable.'_to_'.$foreignTable;

        // Test if table doesn't exist already
        $sql = "SHOW TABLES LIKE '$table'";
        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery();
        if(!empty($result->fetchAllAssociative())){
            throw new Exception("Sql table $table already exists");
        }

        $sql = "CREATE TABLE `$table` (
            id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
            $currentTableKey INT NOT NULL,
            $foreignTableKey INT NOT NULL,
            FOREIGN KEY (`$currentTableKey`) REFERENCES $currentTable(`id`),
            FOREIGN KEY (`$foreignTableKey`) REFERENCES $foreignTable(`id`)
        )";

        $stmt = $conn->prepare($sql);
        $stmt->execute();
    }

    public function preRemove(Field $field, PreRemoveEventArgs $args): void
    {
        $conn = $args->getObjectManager()->getConnection();
        $table = $field->getModule()->getSqlTable();
        $column = $field->getName();

        $sql = "ALTER TABLE $table DROP COLUMN $column;";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
    }

}