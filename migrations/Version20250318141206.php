<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250318141206 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, prénom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE animal ADD photo_id INT NOT NULL, ADD propriétaire_id INT NOT NULL');
        $this->addSql('ALTER TABLE animal ADD CONSTRAINT FK_6AAB231F7E9E4C8C FOREIGN KEY (photo_id) REFERENCES media (id)');
        $this->addSql('ALTER TABLE animal ADD CONSTRAINT FK_6AAB231FF917CCFC FOREIGN KEY (propriétaire_id) REFERENCES client (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6AAB231F7E9E4C8C ON animal (photo_id)');
        $this->addSql('CREATE INDEX IDX_6AAB231FF917CCFC ON animal (propriétaire_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE animal DROP FOREIGN KEY FK_6AAB231FF917CCFC');
        $this->addSql('DROP TABLE client');
        $this->addSql('ALTER TABLE animal DROP FOREIGN KEY FK_6AAB231F7E9E4C8C');
        $this->addSql('DROP INDEX UNIQ_6AAB231F7E9E4C8C ON animal');
        $this->addSql('DROP INDEX IDX_6AAB231FF917CCFC ON animal');
        $this->addSql('ALTER TABLE animal DROP photo_id, DROP propriétaire_id');
    }
}
