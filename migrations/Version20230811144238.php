<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230811144238 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE field ADD pattern_module_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE field ADD CONSTRAINT FK_5BF545589D4A17AB FOREIGN KEY (pattern_module_id) REFERENCES module (id)');
        $this->addSql('CREATE INDEX IDX_5BF545589D4A17AB ON field (pattern_module_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE field DROP FOREIGN KEY FK_5BF545589D4A17AB');
        $this->addSql('DROP INDEX IDX_5BF545589D4A17AB ON field');
        $this->addSql('ALTER TABLE field DROP pattern_module_id');
    }
}
