<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250519221022 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Slug in shopping list';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE shopping_list ADD slug VARCHAR(128) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_3DC1A459989D9B62 ON shopping_list (slug)
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_3DC1A459989D9B62
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE shopping_list DROP slug
        SQL);
    }
}
