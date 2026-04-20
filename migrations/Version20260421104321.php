<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260421104321 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Setup initial database structure';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE agent (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, roles CLOB NOT NULL, team_id INTEGER DEFAULT NULL, CONSTRAINT FK_268B9C9D296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_268B9C9D296CD8AE ON agent (team_id)');
        $this->addSql('CREATE TABLE category (id BLOB NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE TABLE fact (id BLOB NOT NULL, text VARCHAR(255) DEFAULT NULL, author VARCHAR(10) NOT NULL, created_at DATETIME NOT NULL, category_id BLOB DEFAULT NULL, PRIMARY KEY (id), CONSTRAINT FK_6FA45B9512469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_6FA45B9512469DE2 ON fact (category_id)');
        $this->addSql('CREATE TABLE gift (id BLOB NOT NULL, name VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, author_email VARCHAR(255) NOT NULL, is_private BOOLEAN NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE TABLE laboratory (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE project (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, created_by_id INTEGER DEFAULT NULL, team_id INTEGER DEFAULT NULL, CONSTRAINT FK_2FB3D0EEB03A8386 FOREIGN KEY (created_by_id) REFERENCES agent (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_2FB3D0EE296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_2FB3D0EEB03A8386 ON project (created_by_id)');
        $this->addSql('CREATE INDEX IDX_2FB3D0EE296CD8AE ON project (team_id)');
        $this->addSql('CREATE TABLE project_agent (project_id INTEGER NOT NULL, agent_id INTEGER NOT NULL, PRIMARY KEY (project_id, agent_id), CONSTRAINT FK_35DE9503166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_35DE95033414710B FOREIGN KEY (agent_id) REFERENCES agent (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_35DE9503166D1F9C ON project_agent (project_id)');
        $this->addSql('CREATE INDEX IDX_35DE95033414710B ON project_agent (agent_id)');
        $this->addSql('CREATE TABLE team (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, manager_id INTEGER DEFAULT NULL, laboratory_id INTEGER DEFAULT NULL, CONSTRAINT FK_C4E0A61F783E3463 FOREIGN KEY (manager_id) REFERENCES agent (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_C4E0A61F2F2A371E FOREIGN KEY (laboratory_id) REFERENCES laboratory (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C4E0A61F783E3463 ON team (manager_id)');
        $this->addSql('CREATE INDEX IDX_C4E0A61F2F2A371E ON team (laboratory_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE agent');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE fact');
        $this->addSql('DROP TABLE gift');
        $this->addSql('DROP TABLE laboratory');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE project_agent');
        $this->addSql('DROP TABLE team');
    }
}
