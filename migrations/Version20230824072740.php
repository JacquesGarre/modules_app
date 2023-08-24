<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230824072740 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE layout (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE html_element ADD layout_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE html_element ADD CONSTRAINT FK_D51460B58C22AA1A FOREIGN KEY (layout_id) REFERENCES layout (id)');
        $this->addSql('CREATE INDEX IDX_D51460B58C22AA1A ON html_element (layout_id)');
        $this->addSql('ALTER TABLE page ADD layout_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE page ADD CONSTRAINT FK_140AB6208C22AA1A FOREIGN KEY (layout_id) REFERENCES layout (id)');
        $this->addSql('CREATE INDEX IDX_140AB6208C22AA1A ON page (layout_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE html_element DROP FOREIGN KEY FK_D51460B58C22AA1A');
        $this->addSql('ALTER TABLE page DROP FOREIGN KEY FK_140AB6208C22AA1A');
        $this->addSql('DROP TABLE layout');
        $this->addSql('DROP INDEX IDX_D51460B58C22AA1A ON html_element');
        $this->addSql('ALTER TABLE html_element DROP layout_id');
        $this->addSql('DROP INDEX IDX_140AB6208C22AA1A ON page');
        $this->addSql('ALTER TABLE page DROP layout_id');
    }
}
