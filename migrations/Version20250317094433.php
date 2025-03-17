<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250317094433 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE recipient DROP FOREIGN KEY FK_6804FB4922DB1917');
        $this->addSql('DROP INDEX IDX_6804FB4922DB1917 ON recipient');
        $this->addSql('ALTER TABLE recipient DROP newsletter_id');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON recipient (recipient_email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_IDENTIFIER_EMAIL ON recipient');
        $this->addSql('ALTER TABLE recipient ADD newsletter_id INT NOT NULL');
        $this->addSql('ALTER TABLE recipient ADD CONSTRAINT FK_6804FB4922DB1917 FOREIGN KEY (newsletter_id) REFERENCES newsletter (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_6804FB4922DB1917 ON recipient (newsletter_id)');
    }
}
