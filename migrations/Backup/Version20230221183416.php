<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230221183416 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE rami (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, age VARCHAR(255) NOT NULL, field VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE test (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7D936B2FA');
        $this->addSql('ALTER TABLE event_user DROP FOREIGN KEY FK_92589AE271F7E88B');
        $this->addSql('ALTER TABLE event_user DROP FOREIGN KEY FK_92589AE2A76ED395');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC27BCF5E72D');
        $this->addSql('ALTER TABLE sponsor_e_event DROP FOREIGN KEY FK_ED8F5E6541E87EF');
        $this->addSql('ALTER TABLE sponsor_e_event DROP FOREIGN KEY FK_ED8F5E671F7E88B');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE event_user');
        $this->addSql('DROP TABLE produit');
        $this->addSql('DROP TABLE sponsor_e');
        $this->addSql('DROP TABLE sponsor_e_event');
        $this->addSql('DROP TABLE user');
        $this->addSql('ALTER TABLE coach DROP FOREIGN KEY FK_3F596DCC6D55ACAB');
        $this->addSql('DROP INDEX IDX_3F596DCC6D55ACAB ON coach');
        $this->addSql('ALTER TABLE coach CHANGE academy_id academy_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE coach ADD CONSTRAINT FK_3F596DCCAADF2142 FOREIGN KEY (academy_id_id) REFERENCES academy (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_3F596DCCAADF2142 ON coach (academy_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categorie (id INT AUTO_INCREMENT NOT NULL, nom_cat VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, organisateur_id INT DEFAULT NULL, nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, category VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_3BAE0AA7D936B2FA (organisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE event_user (event_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_92589AE2A76ED395 (user_id), INDEX IDX_92589AE271F7E88B (event_id), PRIMARY KEY(event_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE produit (id INT AUTO_INCREMENT NOT NULL, categorie_id INT NOT NULL, libelle VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, prix DOUBLE PRECISION NOT NULL, stock INT NOT NULL, ref VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_29A5EC27BCF5E72D (categorie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE sponsor_e (id INT AUTO_INCREMENT NOT NULL, nom_sponsor VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, email_sponsor VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, tel_sponsor VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE sponsor_e_event (sponsor_e_id INT NOT NULL, event_id INT NOT NULL, INDEX IDX_ED8F5E6541E87EF (sponsor_e_id), INDEX IDX_ED8F5E671F7E88B (event_id), PRIMARY KEY(sponsor_e_id, event_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\', password VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, username VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, phone VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, firstname VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, lastname VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7D936B2FA FOREIGN KEY (organisateur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE event_user ADD CONSTRAINT FK_92589AE271F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_user ADD CONSTRAINT FK_92589AE2A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)');
        $this->addSql('ALTER TABLE sponsor_e_event ADD CONSTRAINT FK_ED8F5E6541E87EF FOREIGN KEY (sponsor_e_id) REFERENCES sponsor_e (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sponsor_e_event ADD CONSTRAINT FK_ED8F5E671F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE rami');
        $this->addSql('DROP TABLE test');
        $this->addSql('ALTER TABLE coach DROP FOREIGN KEY FK_3F596DCCAADF2142');
        $this->addSql('DROP INDEX IDX_3F596DCCAADF2142 ON coach');
        $this->addSql('ALTER TABLE coach CHANGE academy_id_id academy_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE coach ADD CONSTRAINT FK_3F596DCC6D55ACAB FOREIGN KEY (academy_id) REFERENCES academy (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_3F596DCC6D55ACAB ON coach (academy_id)');
    }
}
