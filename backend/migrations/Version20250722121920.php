<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250722121920 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
//        $this->addSql('ALTER TABLE items ADD orders_id INT DEFAULT NULL, DROP commande_id');
//        $this->addSql('ALTER TABLE items ADD CONSTRAINT FK_E11EE94DCFFE9AD6 FOREIGN KEY (orders_id) REFERENCES `order` (id)');
//        $this->addSql('CREATE INDEX IDX_E11EE94DCFFE9AD6 ON items (orders_id)');
//        $this->addSql('ALTER TABLE `order` ADD adress_order VARCHAR(255) NOT NULL, ADD delivery VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
//        $this->addSql('ALTER TABLE items DROP FOREIGN KEY FK_E11EE94DCFFE9AD6');
//        $this->addSql('DROP INDEX IDX_E11EE94DCFFE9AD6 ON items');
//        $this->addSql('ALTER TABLE items ADD commande_id INT NOT NULL, DROP orders_id');
//        $this->addSql('ALTER TABLE `order` DROP adress_order, DROP delivery');
    }
}
