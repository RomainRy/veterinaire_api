<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250322153039 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE consultation_traitement (consultation_id INT NOT NULL, traitement_id INT NOT NULL, INDEX IDX_14FDE8EA62FF6CDF (consultation_id), INDEX IDX_14FDE8EADDA344B6 (traitement_id), PRIMARY KEY(consultation_id, traitement_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE consultation_traitement ADD CONSTRAINT FK_14FDE8EA62FF6CDF FOREIGN KEY (consultation_id) REFERENCES consultation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE consultation_traitement ADD CONSTRAINT FK_14FDE8EADDA344B6 FOREIGN KEY (traitement_id) REFERENCES traitement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE consultation ADD animal_id INT NOT NULL, ADD assistant_id INT NOT NULL, ADD vétérinaire_id INT NOT NULL, ADD statut VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE consultation ADD CONSTRAINT FK_964685A68E962C16 FOREIGN KEY (animal_id) REFERENCES animal (id)');
        $this->addSql('ALTER TABLE consultation ADD CONSTRAINT FK_964685A6E05387EF FOREIGN KEY (assistant_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE consultation ADD CONSTRAINT FK_964685A61186E1FE FOREIGN KEY (vétérinaire_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_964685A68E962C16 ON consultation (animal_id)');
        $this->addSql('CREATE INDEX IDX_964685A6E05387EF ON consultation (assistant_id)');
        $this->addSql('CREATE INDEX IDX_964685A61186E1FE ON consultation (vétérinaire_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE consultation_traitement DROP FOREIGN KEY FK_14FDE8EA62FF6CDF');
        $this->addSql('ALTER TABLE consultation_traitement DROP FOREIGN KEY FK_14FDE8EADDA344B6');
        $this->addSql('DROP TABLE consultation_traitement');
        $this->addSql('ALTER TABLE consultation DROP FOREIGN KEY FK_964685A68E962C16');
        $this->addSql('ALTER TABLE consultation DROP FOREIGN KEY FK_964685A6E05387EF');
        $this->addSql('ALTER TABLE consultation DROP FOREIGN KEY FK_964685A61186E1FE');
        $this->addSql('DROP INDEX IDX_964685A68E962C16 ON consultation');
        $this->addSql('DROP INDEX IDX_964685A6E05387EF ON consultation');
        $this->addSql('DROP INDEX IDX_964685A61186E1FE ON consultation');
        $this->addSql('ALTER TABLE consultation DROP animal_id, DROP assistant_id, DROP vétérinaire_id, DROP statut');
    }
}
