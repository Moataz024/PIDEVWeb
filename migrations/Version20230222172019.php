<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230222172019 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE terrain ADD owner_id INT NOT NULL');
        $this->addSql('ALTER TABLE terrain ADD CONSTRAINT FK_C87653B17E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_C87653B17E3C61F9 ON terrain (owner_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE terrain DROP FOREIGN KEY FK_C87653B17E3C61F9');
        $this->addSql('DROP INDEX IDX_C87653B17E3C61F9 ON terrain');
        $this->addSql('ALTER TABLE terrain DROP owner_id');
    }
}
