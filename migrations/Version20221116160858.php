<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221116160858 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE contest (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, current_step INT DEFAULT NULL, max_steps INT DEFAULT NULL, active TINYINT(1) NOT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_1A95CB512469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contest_games (id INT AUTO_INCREMENT NOT NULL, contest_id INT NOT NULL, first_goal_id INT NOT NULL, second_goal_id INT NOT NULL, result INT DEFAULT NULL, step INT DEFAULT NULL, INDEX IDX_F35E5B191CD0F0DE (contest_id), INDEX IDX_F35E5B1975F17602 (first_goal_id), INDEX IDX_F35E5B19713F9A93 (second_goal_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE contest ADD CONSTRAINT FK_1A95CB512469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE contest_games ADD CONSTRAINT FK_F35E5B191CD0F0DE FOREIGN KEY (contest_id) REFERENCES contest (id)');
        $this->addSql('ALTER TABLE contest_games ADD CONSTRAINT FK_F35E5B1975F17602 FOREIGN KEY (first_goal_id) REFERENCES goal (id)');
        $this->addSql('ALTER TABLE contest_games ADD CONSTRAINT FK_F35E5B19713F9A93 FOREIGN KEY (second_goal_id) REFERENCES goal (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contest DROP FOREIGN KEY FK_1A95CB512469DE2');
        $this->addSql('ALTER TABLE contest_games DROP FOREIGN KEY FK_F35E5B191CD0F0DE');
        $this->addSql('ALTER TABLE contest_games DROP FOREIGN KEY FK_F35E5B1975F17602');
        $this->addSql('ALTER TABLE contest_games DROP FOREIGN KEY FK_F35E5B19713F9A93');
        $this->addSql('DROP TABLE contest');
        $this->addSql('DROP TABLE contest_games');
    }
}
