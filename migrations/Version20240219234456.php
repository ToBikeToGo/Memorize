<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240219234456 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__card AS SELECT id, question, answer, category, tag FROM card');
        $this->addSql('DROP TABLE card');
        $this->addSql('CREATE TABLE card (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, question CLOB NOT NULL, answer CLOB NOT NULL, category VARCHAR(255) NOT NULL, tag VARCHAR(255) NOT NULL, last_reviewed_at DATETIME DEFAULT NULL, CONSTRAINT FK_161498D3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO card (id, question, answer, category, tag) SELECT id, question, answer, category, tag FROM __temp__card');
        $this->addSql('DROP TABLE __temp__card');
        $this->addSql('CREATE INDEX IDX_161498D3A76ED395 ON card (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__card AS SELECT id, question, answer, category, tag FROM card');
        $this->addSql('DROP TABLE card');
        $this->addSql('CREATE TABLE card (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, question CLOB NOT NULL, answer CLOB NOT NULL, category VARCHAR(255) NOT NULL, tag VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO card (id, question, answer, category, tag) SELECT id, question, answer, category, tag FROM __temp__card');
        $this->addSql('DROP TABLE __temp__card');
    }
}
