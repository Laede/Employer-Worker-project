<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180519065518 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE apply (id INT AUTO_INCREMENT NOT NULL, worker_id INT NOT NULL, project_id INT NOT NULL, status VARCHAR(16) NOT NULL, INDEX IDX_BD2F8C1F6B20BA36 (worker_id), INDEX IDX_BD2F8C1F166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, surname VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles VARCHAR(255) NOT NULL, created DATETIME NOT NULL, updated DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messages (id INT AUTO_INCREMENT NOT NULL, apply_id INT NOT NULL, user_id INT NOT NULL, timestamp DATETIME NOT NULL, message VARCHAR(512) NOT NULL, INDEX IDX_DB021E964DDCCBDE (apply_id), INDEX IDX_DB021E96A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, start_date DATE NOT NULL, crew_count SMALLINT NOT NULL, budget VARCHAR(255) NOT NULL, register_deadline DATE NOT NULL, INDEX IDX_2FB3D0EEA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE skills (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE skills_worker (skills_id INT NOT NULL, worker_id INT NOT NULL, INDEX IDX_D2ADB1D17FF61858 (skills_id), INDEX IDX_D2ADB1D16B20BA36 (worker_id), PRIMARY KEY(skills_id, worker_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE skills_project (skills_id INT NOT NULL, project_id INT NOT NULL, INDEX IDX_7D962DD67FF61858 (skills_id), INDEX IDX_7D962DD6166D1F9C (project_id), PRIMARY KEY(skills_id, project_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE worker (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, cv VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_9FB2BF62A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE apply ADD CONSTRAINT FK_BD2F8C1F6B20BA36 FOREIGN KEY (worker_id) REFERENCES worker (id)');
        $this->addSql('ALTER TABLE apply ADD CONSTRAINT FK_BD2F8C1F166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E964DDCCBDE FOREIGN KEY (apply_id) REFERENCES apply (id)');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E96A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE skills_worker ADD CONSTRAINT FK_D2ADB1D17FF61858 FOREIGN KEY (skills_id) REFERENCES skills (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE skills_worker ADD CONSTRAINT FK_D2ADB1D16B20BA36 FOREIGN KEY (worker_id) REFERENCES worker (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE skills_project ADD CONSTRAINT FK_7D962DD67FF61858 FOREIGN KEY (skills_id) REFERENCES skills (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE skills_project ADD CONSTRAINT FK_7D962DD6166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE worker ADD CONSTRAINT FK_9FB2BF62A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE messages DROP FOREIGN KEY FK_DB021E964DDCCBDE');
        $this->addSql('ALTER TABLE messages DROP FOREIGN KEY FK_DB021E96A76ED395');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EEA76ED395');
        $this->addSql('ALTER TABLE worker DROP FOREIGN KEY FK_9FB2BF62A76ED395');
        $this->addSql('ALTER TABLE apply DROP FOREIGN KEY FK_BD2F8C1F166D1F9C');
        $this->addSql('ALTER TABLE skills_project DROP FOREIGN KEY FK_7D962DD6166D1F9C');
        $this->addSql('ALTER TABLE skills_worker DROP FOREIGN KEY FK_D2ADB1D17FF61858');
        $this->addSql('ALTER TABLE skills_project DROP FOREIGN KEY FK_7D962DD67FF61858');
        $this->addSql('ALTER TABLE apply DROP FOREIGN KEY FK_BD2F8C1F6B20BA36');
        $this->addSql('ALTER TABLE skills_worker DROP FOREIGN KEY FK_D2ADB1D16B20BA36');
        $this->addSql('DROP TABLE apply');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messages');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE skills');
        $this->addSql('DROP TABLE skills_worker');
        $this->addSql('DROP TABLE skills_project');
        $this->addSql('DROP TABLE worker');
    }
}
