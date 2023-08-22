<?php

namespace App\EventListener;

use App\Entity\Module;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreRemoveEventArgs;
use Exception;

class ModuleEventListener
{

    public function prePersist(Module $module, PrePersistEventArgs $args): void
    {
        $conn = $args->getObjectManager()->getConnection();

        $table = 'app_'.$module->getSqlTable();
        $module->setSqlTable($table);

        // Test if table doesn't exist already
        $sql = "SHOW TABLES LIKE '$table'";
        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery();
        if(!empty($result->fetchAllAssociative())){
            throw new Exception("Sql table $table already exists");
        }

        $sql = "CREATE TABLE `$table` (
            id INT PRIMARY KEY NOT NULL AUTO_INCREMENT
        )";

        $stmt = $conn->prepare($sql);
        $stmt->execute();

    }

    public function preRemove(Module $module, PreRemoveEventArgs $args): void
    {
        $conn = $args->getObjectManager()->getConnection();

        $table = $module->getSqlTable();

        // Test if table doesn't exist already
        $sql = "DROP TABLE `$table`";
        $stmt = $conn->prepare($sql);
        $stmt->executeQuery();


    }

}