<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230812144931 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE html_element ADD module_table_id INT DEFAULT NULL, ADD module_form_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE html_element ADD CONSTRAINT FK_D51460B5883FFBA2 FOREIGN KEY (module_table_id) REFERENCES `table` (id)');
        $this->addSql('ALTER TABLE html_element ADD CONSTRAINT FK_D51460B5F48DA564 FOREIGN KEY (module_form_id) REFERENCES form (id)');
        $this->addSql('CREATE INDEX IDX_D51460B5883FFBA2 ON html_element (module_table_id)');
        $this->addSql('CREATE INDEX IDX_D51460B5F48DA564 ON html_element (module_form_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE html_element DROP FOREIGN KEY FK_D51460B5883FFBA2');
        $this->addSql('ALTER TABLE html_element DROP FOREIGN KEY FK_D51460B5F48DA564');
        $this->addSql('DROP INDEX IDX_D51460B5883FFBA2 ON html_element');
        $this->addSql('DROP INDEX IDX_D51460B5F48DA564 ON html_element');
        $this->addSql('ALTER TABLE html_element DROP module_table_id, DROP module_form_id');
    }
}
