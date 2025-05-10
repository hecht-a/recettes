<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250510160941 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SEQUENCE shopping_list_id_seq INCREMENT BY 1 MINVALUE 1 START 1
        SQL);
        $this->addSql(<<<'SQL'
            CREATE SEQUENCE shopping_list_ingredient_id_seq INCREMENT BY 1 MINVALUE 1 START 1
        SQL);
        $this->addSql(<<<'SQL'
            CREATE SEQUENCE shopping_list_recipe_id_seq INCREMENT BY 1 MINVALUE 1 START 1
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE shopping_list (id INT NOT NULL, user_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_3DC1A459A76ED395 ON shopping_list (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN shopping_list.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN shopping_list.updated_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE shopping_list_ingredient (id INT NOT NULL, shopping_list_id INT DEFAULT NULL, ingredient_id INT DEFAULT NULL, quantity DOUBLE PRECISION NOT NULL, is_checked BOOLEAN DEFAULT false NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_1AD090A523245BF9 ON shopping_list_ingredient (shopping_list_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_1AD090A5933FE08C ON shopping_list_ingredient (ingredient_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN shopping_list_ingredient.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN shopping_list_ingredient.updated_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE shopping_list_recipe (id INT NOT NULL, shopping_list_id INT DEFAULT NULL, recipe_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_C14F483F23245BF9 ON shopping_list_recipe (shopping_list_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_C14F483F59D8A214 ON shopping_list_recipe (recipe_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN shopping_list_recipe.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN shopping_list_recipe.updated_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE shopping_list ADD CONSTRAINT FK_3DC1A459A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE shopping_list_ingredient ADD CONSTRAINT FK_1AD090A523245BF9 FOREIGN KEY (shopping_list_id) REFERENCES shopping_list (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE shopping_list_ingredient ADD CONSTRAINT FK_1AD090A5933FE08C FOREIGN KEY (ingredient_id) REFERENCES ingredient (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE shopping_list_recipe ADD CONSTRAINT FK_C14F483F23245BF9 FOREIGN KEY (shopping_list_id) REFERENCES shopping_list (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE shopping_list_recipe ADD CONSTRAINT FK_C14F483F59D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            DROP SEQUENCE shopping_list_id_seq CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            DROP SEQUENCE shopping_list_ingredient_id_seq CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            DROP SEQUENCE shopping_list_recipe_id_seq CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE shopping_list DROP CONSTRAINT FK_3DC1A459A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE shopping_list_ingredient DROP CONSTRAINT FK_1AD090A523245BF9
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE shopping_list_ingredient DROP CONSTRAINT FK_1AD090A5933FE08C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE shopping_list_recipe DROP CONSTRAINT FK_C14F483F23245BF9
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE shopping_list_recipe DROP CONSTRAINT FK_C14F483F59D8A214
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE shopping_list
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE shopping_list_ingredient
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE shopping_list_recipe
        SQL);
    }
}
