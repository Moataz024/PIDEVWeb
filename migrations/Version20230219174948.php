<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230219174948 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE coach DROP FOREIGN KEY FK_3F596DCC6D55ACAB');
        $this->addSql('DROP INDEX IDX_3F596DCC6D55ACAB ON coach');
        $this->addSql('ALTER TABLE coach CHANGE academy_id academy_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE coach ADD CONSTRAINT FK_3F596DCCAADF2142 FOREIGN KEY (academy_id_id) REFERENCES academy (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_3F596DCCAADF2142 ON coach (academy_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE coach DROP FOREIGN KEY FK_3F596DCCAADF2142');
        $this->addSql('DROP INDEX IDX_3F596DCCAADF2142 ON coach');
        $this->addSql('ALTER TABLE coach CHANGE academy_id_id academy_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE coach ADD CONSTRAINT FK_3F596DCC6D55ACAB FOREIGN KEY (academy_id) REFERENCES academy (id)');
        $this->addSql('CREATE INDEX IDX_3F596DCC6D55ACAB ON coach (academy_id)');
    }
}
