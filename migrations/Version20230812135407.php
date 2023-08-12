<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230812135407 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE html_element (id INT AUTO_INCREMENT NOT NULL, page_id INT DEFAULT NULL, parent_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, size_class VARCHAR(255) DEFAULT NULL, additionnal_classes VARCHAR(255) DEFAULT NULL, INDEX IDX_D51460B5C4663E4 (page_id), INDEX IDX_D51460B5727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE html_element ADD CONSTRAINT FK_D51460B5C4663E4 FOREIGN KEY (page_id) REFERENCES page (id)');
        $this->addSql('ALTER TABLE html_element ADD CONSTRAINT FK_D51460B5727ACA70 FOREIGN KEY (parent_id) REFERENCES html_element (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE html_element DROP FOREIGN KEY FK_D51460B5C4663E4');
        $this->addSql('ALTER TABLE html_element DROP FOREIGN KEY FK_D51460B5727ACA70');
        $this->addSql('DROP TABLE html_element');
    }
}
