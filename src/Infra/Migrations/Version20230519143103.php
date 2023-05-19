<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230519143103 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category_recipe (category_id INT NOT NULL, recipe_id INT NOT NULL, PRIMARY KEY(category_id, recipe_id))');
        $this->addSql('CREATE INDEX IDX_D5607B4C12469DE2 ON category_recipe (category_id)');
        $this->addSql('CREATE INDEX IDX_D5607B4C59D8A214 ON category_recipe (recipe_id)');
        $this->addSql('ALTER TABLE category_recipe ADD CONSTRAINT FK_D5607B4C12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE category_recipe ADD CONSTRAINT FK_D5607B4C59D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ingredient ADD image_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE recipe ADD image_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE utensil ADD image_name VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE category_recipe DROP CONSTRAINT FK_D5607B4C12469DE2');
        $this->addSql('ALTER TABLE category_recipe DROP CONSTRAINT FK_D5607B4C59D8A214');
        $this->addSql('DROP TABLE category_recipe');
        $this->addSql('ALTER TABLE recipe DROP image_name');
        $this->addSql('ALTER TABLE ingredient DROP image_name');
        $this->addSql('ALTER TABLE utensil DROP image_name');
    }
}
