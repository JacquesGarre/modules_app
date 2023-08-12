<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230812084901 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE field (id INT AUTO_INCREMENT NOT NULL, module_id INT NOT NULL, label VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, required TINYINT(1) NOT NULL, disabled TINYINT(1) NOT NULL, name VARCHAR(255) NOT NULL, readonly TINYINT(1) NOT NULL, value VARCHAR(255) DEFAULT NULL, foreign_table VARCHAR(255) DEFAULT NULL, multiple TINYINT(1) DEFAULT NULL, INDEX IDX_5BF54558AFC2B591 (module_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE field_listing (field_id INT NOT NULL, listing_id INT NOT NULL, INDEX IDX_25246D74443707B0 (field_id), INDEX IDX_25246D74D4619D1A (listing_id), PRIMARY KEY(field_id, listing_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE form (id INT AUTO_INCREMENT NOT NULL, module_id INT NOT NULL, title VARCHAR(255) NOT NULL, action VARCHAR(255) NOT NULL, INDEX IDX_5288FD4FAFC2B591 (module_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE form_field (form_id INT NOT NULL, field_id INT NOT NULL, INDEX IDX_D8B2E19B5FF69B7D (form_id), INDEX IDX_D8B2E19B443707B0 (field_id), PRIMARY KEY(form_id, field_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE listing (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, value VARCHAR(255) NOT NULL, color_class VARCHAR(255) NOT NULL, bg_class VARCHAR(255) NOT NULL, list VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE module (id INT AUTO_INCREMENT NOT NULL, label_plural VARCHAR(255) NOT NULL, label_singular VARCHAR(255) NOT NULL, sql_table VARCHAR(255) NOT NULL, pattern VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE field ADD CONSTRAINT FK_5BF54558AFC2B591 FOREIGN KEY (module_id) REFERENCES module (id)');
        $this->addSql('ALTER TABLE field_listing ADD CONSTRAINT FK_25246D74443707B0 FOREIGN KEY (field_id) REFERENCES field (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE field_listing ADD CONSTRAINT FK_25246D74D4619D1A FOREIGN KEY (listing_id) REFERENCES listing (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE form ADD CONSTRAINT FK_5288FD4FAFC2B591 FOREIGN KEY (module_id) REFERENCES module (id)');
        $this->addSql('ALTER TABLE form_field ADD CONSTRAINT FK_D8B2E19B5FF69B7D FOREIGN KEY (form_id) REFERENCES form (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE form_field ADD CONSTRAINT FK_D8B2E19B443707B0 FOREIGN KEY (field_id) REFERENCES field (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE field DROP FOREIGN KEY FK_5BF54558AFC2B591');
        $this->addSql('ALTER TABLE field_listing DROP FOREIGN KEY FK_25246D74443707B0');
        $this->addSql('ALTER TABLE field_listing DROP FOREIGN KEY FK_25246D74D4619D1A');
        $this->addSql('ALTER TABLE form DROP FOREIGN KEY FK_5288FD4FAFC2B591');
        $this->addSql('ALTER TABLE form_field DROP FOREIGN KEY FK_D8B2E19B5FF69B7D');
        $this->addSql('ALTER TABLE form_field DROP FOREIGN KEY FK_D8B2E19B443707B0');
        $this->addSql('DROP TABLE field');
        $this->addSql('DROP TABLE field_listing');
        $this->addSql('DROP TABLE form');
        $this->addSql('DROP TABLE form_field');
        $this->addSql('DROP TABLE listing');
        $this->addSql('DROP TABLE module');
    }
}
