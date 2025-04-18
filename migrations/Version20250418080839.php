<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250418080839 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE booking (id SERIAL NOT NULL, house_id INT NOT NULL, client_name VARCHAR(255) NOT NULL, client_email VARCHAR(255) NOT NULL, client_phone VARCHAR(20) DEFAULT NULL, check_in_date DATE NOT NULL, check_out_date DATE NOT NULL, number_of_guests INT NOT NULL, total_price DOUBLE PRECISION NOT NULL, status VARCHAR(20) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_E00CEDDE6BB74515 ON booking (house_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE house (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(1000) DEFAULT NULL, price_per_night DOUBLE PRECISION NOT NULL, capacity INT NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE summer_house (id INT NOT NULL, bedrooms INT DEFAULT NULL, distance_from_sea INT DEFAULT NULL, has_shower BOOLEAN DEFAULT NULL, has_bathroom BOOLEAN DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE6BB74515 FOREIGN KEY (house_id) REFERENCES house (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE summer_house ADD CONSTRAINT FK_91929996BF396750 FOREIGN KEY (id) REFERENCES house (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE booking DROP CONSTRAINT FK_E00CEDDE6BB74515
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE summer_house DROP CONSTRAINT FK_91929996BF396750
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE booking
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE house
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE summer_house
        SQL);
    }
}
