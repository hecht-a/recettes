<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230612094401 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE allergen ADD slug VARCHAR(128) DEFAULT \'\' NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_25BF08CE989D9B62 ON allergen (slug)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9F283CBC5E237E06 ON utensil (name)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX UNIQ_9F283CBC5E237E06');
        $this->addSql('DROP INDEX UNIQ_25BF08CE989D9B62');
        $this->addSql('ALTER TABLE allergen DROP slug');
    }
}
