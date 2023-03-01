<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230223160239 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE academy_back ADD coach VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE coach_back ADD academy_id INT DEFAULT NULL, DROP academy, CHANGE telephone telephone VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE coach_back ADD CONSTRAINT FK_EEF1A42B6D55ACAB FOREIGN KEY (academy_id) REFERENCES academy_back (id)');
        $this->addSql('CREATE INDEX IDX_EEF1A42B6D55ACAB ON coach_back (academy_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE academy_back DROP coach');
        $this->addSql('ALTER TABLE coach_back DROP FOREIGN KEY FK_EEF1A42B6D55ACAB');
        $this->addSql('DROP INDEX IDX_EEF1A42B6D55ACAB ON coach_back');
        $this->addSql('ALTER TABLE coach_back ADD academy VARCHAR(255) NOT NULL, DROP academy_id, CHANGE telephone telephone INT NOT NULL');
    }
}
