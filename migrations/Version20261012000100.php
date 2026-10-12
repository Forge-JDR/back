<?php

declare(strict_types=1);
namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261012000100 extends AbstractMigration
{
    public function getDescription(): string { return 'Schéma historique Forge JDR pour une base vierge.'; }
    public function isTransactional(): bool { return false; }
    public function up(Schema $schema): void
    {
        $this->abortIf(!$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\AbstractMySQLPlatform, 'Migration réservée à MySQL/MariaDB.');
        $this->addSql('CREATE TABLE bestiary (id INT AUTO_INCREMENT NOT NULL, wiki_id INT NOT NULL, name VARCHAR(255) NOT NULL, content VARCHAR(2048) NOT NULL, type VARCHAR(255) NOT NULL, image_url VARCHAR(512) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_946DE9FFAA948DBE (wiki_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE caracter (id INT AUTO_INCREMENT NOT NULL, image_file_id INT DEFAULT NULL, user_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_28D5DAC46DB2EB0 (image_file_id), INDEX IDX_28D5DAC4A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE job (id INT AUTO_INCREMENT NOT NULL, wiki_id INT NOT NULL, name VARCHAR(255) NOT NULL, content VARCHAR(2048) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_FBD8E0F8AA948DBE (wiki_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE picture (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, update_at DATETIME NOT NULL, public_path VARCHAR(255) NOT NULL, real_path VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE race (id INT AUTO_INCREMENT NOT NULL, wiki_id INT NOT NULL, name VARCHAR(255) NOT NULL, content VARCHAR(2048) NOT NULL, image_url VARCHAR(512) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_DA6FBBAFAA948DBE (wiki_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE refresh_tokens (id INT AUTO_INCREMENT NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid DATETIME NOT NULL, UNIQUE INDEX UNIQ_9BACE7E1C74F2195 (refresh_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE scenario (id INT AUTO_INCREMENT NOT NULL, wiki_id INT NOT NULL, name VARCHAR(255) NOT NULL, content VARCHAR(2048) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_3E45C8D8AA948DBE (wiki_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, uuid VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, pseudo VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_UUID (uuid), UNIQUE INDEX USERNAME (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wiki (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, image_file_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, content VARCHAR(2048) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', status VARCHAR(20) DEFAULT \'inProgress\' NOT NULL, UNIQUE INDEX UNIQ_22CDDC066DB2EB0 (image_file_id), INDEX IDX_22CDDC06A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bestiary ADD CONSTRAINT FK_946DE9FFAA948DBE FOREIGN KEY (wiki_id) REFERENCES wiki (id)');
        $this->addSql('ALTER TABLE caracter ADD CONSTRAINT FK_28D5DAC46DB2EB0 FOREIGN KEY (image_file_id) REFERENCES picture (id)');
        $this->addSql('ALTER TABLE caracter ADD CONSTRAINT FK_28D5DAC4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE job ADD CONSTRAINT FK_FBD8E0F8AA948DBE FOREIGN KEY (wiki_id) REFERENCES wiki (id)');
        $this->addSql('ALTER TABLE race ADD CONSTRAINT FK_DA6FBBAFAA948DBE FOREIGN KEY (wiki_id) REFERENCES wiki (id)');
        $this->addSql('ALTER TABLE scenario ADD CONSTRAINT FK_3E45C8D8AA948DBE FOREIGN KEY (wiki_id) REFERENCES wiki (id)');
        $this->addSql('ALTER TABLE wiki ADD CONSTRAINT FK_22CDDC06A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE wiki ADD CONSTRAINT FK_22CDDC066DB2EB0 FOREIGN KEY (image_file_id) REFERENCES picture (id)');
    }
    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException('Restaurer une sauvegarde vérifiée pour revenir à cette version sans perdre de données.');
    }
}
