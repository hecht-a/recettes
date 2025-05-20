<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250511174355 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Unique name for shopping list';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_3DC1A4595E237E06 ON shopping_list (name)
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_3DC1A4595E237E06
        SQL);
    }
}
