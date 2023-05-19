<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230519122824 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ingredient_recipe DROP CONSTRAINT FK_36F27176933FE08C');
        $this->addSql('ALTER TABLE ingredient_recipe DROP CONSTRAINT FK_36F2717659D8A214');
        $this->addSql('ALTER TABLE ingredient_recipe ADD quantity DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE ingredient_recipe ADD CONSTRAINT FK_36F27176933FE08C FOREIGN KEY (ingredient_id) REFERENCES ingredient (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ingredient_recipe ADD CONSTRAINT FK_36F2717659D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE ingredient_recipe DROP CONSTRAINT fk_36f27176933fe08c');
        $this->addSql('ALTER TABLE ingredient_recipe DROP CONSTRAINT fk_36f2717659d8a214');
        $this->addSql('ALTER TABLE ingredient_recipe DROP quantity');
        $this->addSql('ALTER TABLE ingredient_recipe ADD CONSTRAINT fk_36f27176933fe08c FOREIGN KEY (ingredient_id) REFERENCES ingredient (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ingredient_recipe ADD CONSTRAINT fk_36f2717659d8a214 FOREIGN KEY (recipe_id) REFERENCES recipe (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
