<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230813101345 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE field_listing DROP FOREIGN KEY FK_25246D74D4619D1A');
        $this->addSql('ALTER TABLE field_listing DROP FOREIGN KEY FK_25246D74443707B0');
        $this->addSql('DROP TABLE field_listing');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE field_listing (field_id INT NOT NULL, listing_id INT NOT NULL, INDEX IDX_25246D74D4619D1A (listing_id), INDEX IDX_25246D74443707B0 (field_id), PRIMARY KEY(field_id, listing_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE field_listing ADD CONSTRAINT FK_25246D74D4619D1A FOREIGN KEY (listing_id) REFERENCES listing (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE field_listing ADD CONSTRAINT FK_25246D74443707B0 FOREIGN KEY (field_id) REFERENCES field (id) ON DELETE CASCADE');
    }
}
