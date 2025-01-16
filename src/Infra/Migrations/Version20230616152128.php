<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230616152128 extends AbstractMigration
{
    #[\Override]
    public function getDescription(): string
    {
        return '';
    }

    #[\Override]
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE recipe ADD amount_persons INT NOT NULL');
        $this->addSql('ALTER INDEX idx_fe3d4cdba76ed395 RENAME TO IDX_E63DDDCDA76ED395');
        $this->addSql('ALTER INDEX idx_fe3d4cdb59d8a214 RENAME TO IDX_E63DDDCD59D8A214');
    }

    #[\Override]
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE recipe DROP amount_persons');
        $this->addSql('ALTER INDEX idx_e63dddcd59d8a214 RENAME TO idx_fe3d4cdb59d8a214');
        $this->addSql('ALTER INDEX idx_e63dddcda76ed395 RENAME TO idx_fe3d4cdba76ed395');
    }
}
