<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230811152355 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE form (id INT AUTO_INCREMENT NOT NULL, module_id INT NOT NULL, title VARCHAR(255) NOT NULL, action VARCHAR(255) NOT NULL, INDEX IDX_5288FD4FAFC2B591 (module_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE form_field (form_id INT NOT NULL, field_id INT NOT NULL, INDEX IDX_D8B2E19B5FF69B7D (form_id), INDEX IDX_D8B2E19B443707B0 (field_id), PRIMARY KEY(form_id, field_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE form ADD CONSTRAINT FK_5288FD4FAFC2B591 FOREIGN KEY (module_id) REFERENCES module (id)');
        $this->addSql('ALTER TABLE form_field ADD CONSTRAINT FK_D8B2E19B5FF69B7D FOREIGN KEY (form_id) REFERENCES form (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE form_field ADD CONSTRAINT FK_D8B2E19B443707B0 FOREIGN KEY (field_id) REFERENCES field (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE form DROP FOREIGN KEY FK_5288FD4FAFC2B591');
        $this->addSql('ALTER TABLE form_field DROP FOREIGN KEY FK_D8B2E19B5FF69B7D');
        $this->addSql('ALTER TABLE form_field DROP FOREIGN KEY FK_D8B2E19B443707B0');
        $this->addSql('DROP TABLE form');
        $this->addSql('DROP TABLE form_field');
    }
}
