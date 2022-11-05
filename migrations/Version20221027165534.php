<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221027165534 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE task_calendar (id INT AUTO_INCREMENT NOT NULL, goal_id INT DEFAULT NULL, date DATETIME NOT NULL, is_done TINYINT(1) NOT NULL, INDEX IDX_2E694B0A667D1AFE (goal_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE task_calendar ADD CONSTRAINT FK_2E694B0A667D1AFE FOREIGN KEY (goal_id) REFERENCES goal (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE task_calendar DROP FOREIGN KEY FK_2E694B0A667D1AFE');
        $this->addSql('DROP TABLE task_calendar');
    }
}
