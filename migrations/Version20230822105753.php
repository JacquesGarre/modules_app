<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230822105753 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE table_filters (table_id INT NOT NULL, field_id INT NOT NULL, INDEX IDX_7A442682ECFF285C (table_id), INDEX IDX_7A442682443707B0 (field_id), PRIMARY KEY(table_id, field_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE table_filters ADD CONSTRAINT FK_7A442682ECFF285C FOREIGN KEY (table_id) REFERENCES `table` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE table_filters ADD CONSTRAINT FK_7A442682443707B0 FOREIGN KEY (field_id) REFERENCES field (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE table_filters DROP FOREIGN KEY FK_7A442682ECFF285C');
        $this->addSql('ALTER TABLE table_filters DROP FOREIGN KEY FK_7A442682443707B0');
        $this->addSql('DROP TABLE table_filters');
    }
}
