<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230217143500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE coach DROP FOREIGN KEY FK_3F596DCC6D55ACAB');
        $this->addSql('ALTER TABLE coach ADD CONSTRAINT FK_3F596DCC6D55ACAB FOREIGN KEY (academy_id) REFERENCES academy (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE coach DROP FOREIGN KEY FK_3F596DCC6D55ACAB');
        $this->addSql('ALTER TABLE coach ADD CONSTRAINT FK_3F596DCC6D55ACAB FOREIGN KEY (academy_id) REFERENCES academy (id)');
    }
}
