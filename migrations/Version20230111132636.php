<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230111132636 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE turnament (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', rounds INT NOT NULL, current_round INT NOT NULL, finished TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE turnament_goal (turnament_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', goal_id INT NOT NULL, INDEX IDX_68939B7E40B77CEA (turnament_id), INDEX IDX_68939B7E667D1AFE (goal_id), PRIMARY KEY(turnament_id, goal_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE turnament_goal ADD CONSTRAINT FK_68939B7E40B77CEA FOREIGN KEY (turnament_id) REFERENCES turnament (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE turnament_goal ADD CONSTRAINT FK_68939B7E667D1AFE FOREIGN KEY (goal_id) REFERENCES goal (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE turnament_goal DROP FOREIGN KEY FK_68939B7E40B77CEA');
        $this->addSql('ALTER TABLE turnament_goal DROP FOREIGN KEY FK_68939B7E667D1AFE');
        $this->addSql('DROP TABLE turnament');
        $this->addSql('DROP TABLE turnament_goal');
    }
}
