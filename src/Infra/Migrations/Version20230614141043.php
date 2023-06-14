<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230614141043 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE favorite_recipe (user_id INT NOT NULL, recipe_id INT NOT NULL, PRIMARY KEY(user_id, recipe_id))');
        $this->addSql('CREATE INDEX IDX_FE3D4CDBA76ED395 ON favorite_recipe (user_id)');
        $this->addSql('CREATE INDEX IDX_FE3D4CDB59D8A214 ON favorite_recipe (recipe_id)');
        $this->addSql('ALTER TABLE favorite_recipe ADD CONSTRAINT FK_FE3D4CDBA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE favorite_recipe ADD CONSTRAINT FK_FE3D4CDB59D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE favorite_recipe DROP CONSTRAINT FK_FE3D4CDBA76ED395');
        $this->addSql('ALTER TABLE favorite_recipe DROP CONSTRAINT FK_FE3D4CDB59D8A214');
        $this->addSql('DROP TABLE favorite_recipe');
    }
}
