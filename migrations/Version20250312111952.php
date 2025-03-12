<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250312111952 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE order_product (id INT AUTO_INCREMENT NOT NULL, app_order_id INT NOT NULL, app_product_id INT NOT NULL, quantity INT NOT NULL, INDEX IDX_2530ADE6851F0D95 (app_order_id), INDEX IDX_2530ADE6139FCD26 (app_product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE order_product ADD CONSTRAINT FK_2530ADE6851F0D95 FOREIGN KEY (app_order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE order_product ADD CONSTRAINT FK_2530ADE6139FCD26 FOREIGN KEY (app_product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE order_products DROP FOREIGN KEY FK_5242B8EB139FCD26');
        $this->addSql('ALTER TABLE order_products DROP FOREIGN KEY FK_5242B8EB851F0D95');
        $this->addSql('DROP TABLE order_products');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE order_products (id INT AUTO_INCREMENT NOT NULL, app_order_id INT NOT NULL, app_product_id INT NOT NULL, quantity INT NOT NULL, INDEX IDX_5242B8EB851F0D95 (app_order_id), INDEX IDX_5242B8EB139FCD26 (app_product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE order_products ADD CONSTRAINT FK_5242B8EB139FCD26 FOREIGN KEY (app_product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE order_products ADD CONSTRAINT FK_5242B8EB851F0D95 FOREIGN KEY (app_order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE order_product DROP FOREIGN KEY FK_2530ADE6851F0D95');
        $this->addSql('ALTER TABLE order_product DROP FOREIGN KEY FK_2530ADE6139FCD26');
        $this->addSql('DROP TABLE order_product');
    }
}
