<?php

declare(strict_types=1);
namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261012000200 extends AbstractMigration
{
    public function getDescription(): string { return 'Modèles de fiche, parties et fiches ; conservation des wikis existants.'; }
    public function isTransactional(): bool { return false; }
    public function up(Schema $schema): void
    {
        $this->abortIf(!$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\AbstractMySQLPlatform, 'Migration réservée à MySQL/MariaDB.');
        $this->addSql('CREATE TABLE campaign (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, wiki_id INT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, notes LONGTEXT NOT NULL, journal JSON NOT NULL COMMENT \'(DC2Type:json)\', INDEX IDX_1F1512DD7E3C61F9 (owner_id), INDEX IDX_1F1512DDAA948DBE (wiki_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE character_sheet (id INT AUTO_INCREMENT NOT NULL, campaign_id INT NOT NULL, name VARCHAR(255) NOT NULL, notes LONGTEXT NOT NULL, template JSON NOT NULL COMMENT \'(DC2Type:json)\', field_values JSON NOT NULL COMMENT \'(DC2Type:json)\', template_version INT NOT NULL, INDEX IDX_79FF7680F639F774 (campaign_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE campaign ADD CONSTRAINT FK_1F1512DD7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE campaign ADD CONSTRAINT FK_1F1512DDAA948DBE FOREIGN KEY (wiki_id) REFERENCES wiki (id)');
        $this->addSql('ALTER TABLE character_sheet ADD CONSTRAINT FK_79FF7680F639F774 FOREIGN KEY (campaign_id) REFERENCES campaign (id)');
        $this->addSql('ALTER TABLE wiki ADD character_template JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', ADD template_version INT DEFAULT 1 NOT NULL, MODIFY content LONGTEXT NOT NULL');
    }
    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException('Restaurer une sauvegarde vérifiée pour revenir à cette version sans perdre de données.');
    }
}
