<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250305141152 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE order_products (id INT AUTO_INCREMENT NOT NULL, app_order_id INT NOT NULL, app_product_id INT NOT NULL, quantity INT NOT NULL, INDEX IDX_5242B8EB851F0D95 (app_order_id), INDEX IDX_5242B8EB139FCD26 (app_product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE order_products ADD CONSTRAINT FK_5242B8EB851F0D95 FOREIGN KEY (app_order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE order_products ADD CONSTRAINT FK_5242B8EB139FCD26 FOREIGN KEY (app_product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE order_product DROP FOREIGN KEY FK_2530ADE64584665A');
        $this->addSql('ALTER TABLE order_product DROP FOREIGN KEY FK_2530ADE68D9F6D38');
        $this->addSql('DROP TABLE order_product');
        $this->addSql('ALTER TABLE bill DROP FOREIGN KEY FK_7A2119E312854AC3');
        $this->addSql('DROP INDEX UNIQ_7A2119E312854AC3 ON bill');
        $this->addSql('ALTER TABLE bill ADD bill_total_vat NUMERIC(6, 2) NOT NULL, DROP order_reference_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE order_product (order_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_2530ADE68D9F6D38 (order_id), INDEX IDX_2530ADE64584665A (product_id), PRIMARY KEY(order_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE order_product ADD CONSTRAINT FK_2530ADE64584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_product ADD CONSTRAINT FK_2530ADE68D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_products DROP FOREIGN KEY FK_5242B8EB851F0D95');
        $this->addSql('ALTER TABLE order_products DROP FOREIGN KEY FK_5242B8EB139FCD26');
        $this->addSql('DROP TABLE order_products');
        $this->addSql('ALTER TABLE bill ADD order_reference_id INT NOT NULL, DROP bill_total_vat');
        $this->addSql('ALTER TABLE bill ADD CONSTRAINT FK_7A2119E312854AC3 FOREIGN KEY (order_reference_id) REFERENCES `order` (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7A2119E312854AC3 ON bill (order_reference_id)');
    }
}
