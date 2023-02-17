<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230216165100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE sponsor_e (id INT AUTO_INCREMENT NOT NULL, nom_sponsor VARCHAR(255) NOT NULL, email_sponsor VARCHAR(255) NOT NULL, tel_sponsor VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sponsor_e_event (sponsor_e_id INT NOT NULL, event_id INT NOT NULL, INDEX IDX_ED8F5E6541E87EF (sponsor_e_id), INDEX IDX_ED8F5E671F7E88B (event_id), PRIMARY KEY(sponsor_e_id, event_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sponsor_e_event ADD CONSTRAINT FK_ED8F5E6541E87EF FOREIGN KEY (sponsor_e_id) REFERENCES sponsor_e (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sponsor_e_event ADD CONSTRAINT FK_ED8F5E671F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sponsor_e_event DROP FOREIGN KEY FK_ED8F5E6541E87EF');
        $this->addSql('ALTER TABLE sponsor_e_event DROP FOREIGN KEY FK_ED8F5E671F7E88B');
        $this->addSql('DROP TABLE sponsor_e');
        $this->addSql('DROP TABLE sponsor_e_event');
    }
}
