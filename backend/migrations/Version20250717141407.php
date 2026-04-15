<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250717141407 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cart (id INT AUTO_INCREMENT NOT NULL, user_id_id INT DEFAULT NULL, status VARCHAR(255) NOT NULL, INDEX IDX_BA388B79D86650F (user_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE item_cart (id INT AUTO_INCREMENT NOT NULL, cart_id_id INT NOT NULL, prod_id_id INT NOT NULL, quantity INT NOT NULL, INDEX IDX_C393CC4C20AEF35F (cart_id_id), INDEX IDX_C393CC4CF91A0F34 (prod_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B79D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE item_cart ADD CONSTRAINT FK_C393CC4C20AEF35F FOREIGN KEY (cart_id_id) REFERENCES cart (id)');
        $this->addSql('ALTER TABLE item_cart ADD CONSTRAINT FK_C393CC4CF91A0F34 FOREIGN KEY (prod_id_id) REFERENCES products (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B79D86650F');
        $this->addSql('ALTER TABLE item_cart DROP FOREIGN KEY FK_C393CC4C20AEF35F');
        $this->addSql('ALTER TABLE item_cart DROP FOREIGN KEY FK_C393CC4CF91A0F34');
        $this->addSql('DROP TABLE cart');
        $this->addSql('DROP TABLE item_cart');
    }
}
