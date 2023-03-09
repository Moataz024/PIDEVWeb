<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230307151524 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reservation_equipment (reservation_id INT NOT NULL, equipment_id INT NOT NULL, INDEX IDX_C97FB41CB83297E7 (reservation_id), INDEX IDX_C97FB41C517FE9FE (equipment_id), PRIMARY KEY(reservation_id, equipment_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reservation_equipment ADD CONSTRAINT FK_C97FB41CB83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reservation_equipment ADD CONSTRAINT FK_C97FB41C517FE9FE FOREIGN KEY (equipment_id) REFERENCES equipment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE equipment DROP FOREIGN KEY FK_D338D583B83297E7');
        $this->addSql('DROP INDEX IDX_D338D583B83297E7 ON equipment');
        $this->addSql('ALTER TABLE equipment DROP reservation_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation_equipment DROP FOREIGN KEY FK_C97FB41CB83297E7');
        $this->addSql('ALTER TABLE reservation_equipment DROP FOREIGN KEY FK_C97FB41C517FE9FE');
        $this->addSql('DROP TABLE reservation_equipment');
        $this->addSql('ALTER TABLE equipment ADD reservation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE equipment ADD CONSTRAINT FK_D338D583B83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id)');
        $this->addSql('CREATE INDEX IDX_D338D583B83297E7 ON equipment (reservation_id)');
    }
}
