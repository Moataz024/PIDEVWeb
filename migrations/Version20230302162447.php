<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230302162447 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE academy (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_by VARCHAR(255) DEFAULT NULL, category VARCHAR(255) NOT NULL, age INT DEFAULT NULL, telephone INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE coach (id INT AUTO_INCREMENT NOT NULL, academy_id_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, created_by VARCHAR(255) DEFAULT NULL, email VARCHAR(255) NOT NULL, telephone VARCHAR(255) NOT NULL, INDEX IDX_3F596DCCAADF2142 (academy_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rami (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, age VARCHAR(255) NOT NULL, field VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE test (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE coach ADD CONSTRAINT FK_3F596DCCAADF2142 FOREIGN KEY (academy_id_id) REFERENCES academy (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE coach DROP FOREIGN KEY FK_3F596DCCAADF2142');
        $this->addSql('DROP TABLE academy');
        $this->addSql('DROP TABLE coach');
        $this->addSql('DROP TABLE rami');
        $this->addSql('DROP TABLE test');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
