<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210614064507 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_F5299398A76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__order AS SELECT id, user_id, status, status_date, created_date, client_name, client_city, client_street, client_post_code, client_country FROM "order"');
        $this->addSql('DROP TABLE "order"');
        $this->addSql('CREATE TABLE "order" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER DEFAULT NULL, status INTEGER NOT NULL, status_date DATETIME DEFAULT NULL, created_date DATETIME DEFAULT NULL, client_name VARCHAR(150) DEFAULT NULL COLLATE BINARY, client_city VARCHAR(200) DEFAULT NULL COLLATE BINARY, client_street VARCHAR(150) DEFAULT NULL COLLATE BINARY, client_post_code VARCHAR(9) DEFAULT NULL COLLATE BINARY, client_country VARCHAR(2) DEFAULT NULL COLLATE BINARY, CONSTRAINT FK_F5299398A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO "order" (id, user_id, status, status_date, created_date, client_name, client_city, client_street, client_post_code, client_country) SELECT id, user_id, status, status_date, created_date, client_name, client_city, client_street, client_post_code, client_country FROM __temp__order');
        $this->addSql('DROP TABLE __temp__order');
        $this->addSql('CREATE INDEX IDX_F5299398A76ED395 ON "order" (user_id)');
        $this->addSql('DROP INDEX IDX_2530ADE6A5862391');
        $this->addSql('DROP INDEX IDX_2530ADE64584665A');
        $this->addSql('CREATE TEMPORARY TABLE __temp__order_product AS SELECT id, product_id, order_obj_id, quantity FROM order_product');
        $this->addSql('DROP TABLE order_product');
        $this->addSql('CREATE TABLE order_product (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, order_obj_id INTEGER NOT NULL, product_id INTEGER DEFAULT NULL, quantity INTEGER NOT NULL, product_name VARCHAR(150) NOT NULL, price_product NUMERIC(10, 2) NOT NULL, CONSTRAINT FK_2530ADE64584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_2530ADE6A5862391 FOREIGN KEY (order_obj_id) REFERENCES "order" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO order_product (id, product_id, order_obj_id, quantity) SELECT id, product_id, order_obj_id, quantity FROM __temp__order_product');
        $this->addSql('DROP TABLE __temp__order_product');
        $this->addSql('CREATE INDEX IDX_2530ADE6A5862391 ON order_product (order_obj_id)');
        $this->addSql('CREATE INDEX IDX_2530ADE64584665A ON order_product (product_id)');
        $this->addSql('DROP INDEX IDX_D34A04AD12469DE2');
        $this->addSql('CREATE TEMPORARY TABLE __temp__product AS SELECT id, category_id, name, description, image_file_name, price FROM product');
        $this->addSql('DROP TABLE product');
        $this->addSql('CREATE TABLE product (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, category_id INTEGER NOT NULL, name VARCHAR(150) NOT NULL COLLATE BINARY, description CLOB NOT NULL COLLATE BINARY, image_file_name VARCHAR(200) DEFAULT NULL COLLATE BINARY, price NUMERIC(10, 2) NOT NULL, CONSTRAINT FK_D34A04AD12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO product (id, category_id, name, description, image_file_name, price) SELECT id, category_id, name, description, image_file_name, price FROM __temp__product');
        $this->addSql('DROP TABLE __temp__product');
        $this->addSql('CREATE INDEX IDX_D34A04AD12469DE2 ON product (category_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_F5299398A76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__order AS SELECT id, user_id, status, created_date, status_date, client_name, client_city, client_street, client_post_code, client_country FROM "order"');
        $this->addSql('DROP TABLE "order"');
        $this->addSql('CREATE TABLE "order" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER DEFAULT NULL, status INTEGER NOT NULL, created_date DATETIME DEFAULT NULL, status_date DATETIME DEFAULT NULL, client_name VARCHAR(150) DEFAULT NULL, client_city VARCHAR(200) DEFAULT NULL, client_street VARCHAR(150) DEFAULT NULL, client_post_code VARCHAR(9) DEFAULT NULL, client_country VARCHAR(2) DEFAULT NULL)');
        $this->addSql('INSERT INTO "order" (id, user_id, status, created_date, status_date, client_name, client_city, client_street, client_post_code, client_country) SELECT id, user_id, status, created_date, status_date, client_name, client_city, client_street, client_post_code, client_country FROM __temp__order');
        $this->addSql('DROP TABLE __temp__order');
        $this->addSql('CREATE INDEX IDX_F5299398A76ED395 ON "order" (user_id)');
        $this->addSql('DROP INDEX IDX_2530ADE64584665A');
        $this->addSql('DROP INDEX IDX_2530ADE6A5862391');
        $this->addSql('CREATE TEMPORARY TABLE __temp__order_product AS SELECT id, product_id, order_obj_id, quantity FROM order_product');
        $this->addSql('DROP TABLE order_product');
        $this->addSql('CREATE TABLE order_product (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, order_obj_id INTEGER NOT NULL, quantity INTEGER NOT NULL, product_id INTEGER NOT NULL)');
        $this->addSql('INSERT INTO order_product (id, product_id, order_obj_id, quantity) SELECT id, product_id, order_obj_id, quantity FROM __temp__order_product');
        $this->addSql('DROP TABLE __temp__order_product');
        $this->addSql('CREATE INDEX IDX_2530ADE64584665A ON order_product (product_id)');
        $this->addSql('CREATE INDEX IDX_2530ADE6A5862391 ON order_product (order_obj_id)');
        $this->addSql('DROP INDEX IDX_D34A04AD12469DE2');
        $this->addSql('CREATE TEMPORARY TABLE __temp__product AS SELECT id, category_id, name, description, price, image_file_name FROM product');
        $this->addSql('DROP TABLE product');
        $this->addSql('CREATE TABLE product (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, category_id INTEGER NOT NULL, name VARCHAR(150) NOT NULL, description CLOB NOT NULL, price NUMERIC(10, 2) NOT NULL, image_file_name VARCHAR(200) DEFAULT NULL)');
        $this->addSql('INSERT INTO product (id, category_id, name, description, price, image_file_name) SELECT id, category_id, name, description, price, image_file_name FROM __temp__product');
        $this->addSql('DROP TABLE __temp__product');
        $this->addSql('CREATE INDEX IDX_D34A04AD12469DE2 ON product (category_id)');
    }
}
