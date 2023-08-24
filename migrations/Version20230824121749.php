<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230824121749 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE page ADD page_layout_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE page ADD CONSTRAINT FK_140AB620B7CC15D5 FOREIGN KEY (page_layout_id) REFERENCES layout (id)');
        $this->addSql('CREATE INDEX IDX_140AB620B7CC15D5 ON page (page_layout_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE page DROP FOREIGN KEY FK_140AB620B7CC15D5');
        $this->addSql('DROP INDEX IDX_140AB620B7CC15D5 ON page');
        $this->addSql('ALTER TABLE page DROP page_layout_id');
    }
}
