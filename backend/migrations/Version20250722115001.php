<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250722115001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE items ADD CONSTRAINT FK_E11EE94D82EA2E54 FOREIGN KEY (commande_id) REFERENCES `order` (id)');
        $this->addSql('CREATE INDEX IDX_E11EE94D82EA2E54 ON items (commande_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE items DROP FOREIGN KEY FK_E11EE94D82EA2E54');
        $this->addSql('DROP INDEX IDX_E11EE94D82EA2E54 ON items');
    }
}
