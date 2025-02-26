<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250226132518 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bill ADD order_reference_id INT NOT NULL');
        $this->addSql('ALTER TABLE bill ADD CONSTRAINT FK_7A2119E312854AC3 FOREIGN KEY (order_reference_id) REFERENCES `order` (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7A2119E312854AC3 ON bill (order_reference_id)');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993981A8C12F5');
        $this->addSql('DROP INDEX UNIQ_F52993981A8C12F5 ON `order`');
        $this->addSql('ALTER TABLE `order` DROP bill_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` ADD bill_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993981A8C12F5 FOREIGN KEY (bill_id) REFERENCES bill (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F52993981A8C12F5 ON `order` (bill_id)');
        $this->addSql('ALTER TABLE bill DROP FOREIGN KEY FK_7A2119E312854AC3');
        $this->addSql('DROP INDEX UNIQ_7A2119E312854AC3 ON bill');
        $this->addSql('ALTER TABLE bill DROP order_reference_id');
    }
}
