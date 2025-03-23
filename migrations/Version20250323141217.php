<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250323141217 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE animal DROP FOREIGN KEY FK_6AAB231FF917CCFC');
        $this->addSql('DROP INDEX IDX_6AAB231FF917CCFC ON animal');
        $this->addSql('ALTER TABLE animal CHANGE propriétaire_id proprietaire_id INT NOT NULL, CHANGE espèce espece VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE animal ADD CONSTRAINT FK_6AAB231F76C50E4A FOREIGN KEY (proprietaire_id) REFERENCES client (id)');
        $this->addSql('CREATE INDEX IDX_6AAB231F76C50E4A ON animal (proprietaire_id)');
        $this->addSql('ALTER TABLE client ADD email VARCHAR(255) DEFAULT NULL, ADD telephone VARCHAR(20) DEFAULT NULL, ADD adresse VARCHAR(255) DEFAULT NULL, CHANGE prénom prenom VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE consultation DROP FOREIGN KEY FK_964685A61186E1FE');
        $this->addSql('DROP INDEX IDX_964685A61186E1FE ON consultation');
        $this->addSql('ALTER TABLE consultation ADD veterinaire_id INT DEFAULT NULL, ADD date_creation DATETIME NOT NULL, ADD date_consultation DATETIME NOT NULL, ADD est_paye TINYINT(1) NOT NULL, ADD montant_total NUMERIC(10, 2) DEFAULT NULL, ADD notes LONGTEXT DEFAULT NULL, DROP vétérinaire_id, DROP date_création, DROP date_rendez_vous, CHANGE statut statut VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE consultation ADD CONSTRAINT FK_964685A65C80924 FOREIGN KEY (veterinaire_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_964685A65C80924 ON consultation (veterinaire_id)');
        $this->addSql('ALTER TABLE media ADD consultation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10C62FF6CDF FOREIGN KEY (consultation_id) REFERENCES consultation (id)');
        $this->addSql('CREATE INDEX IDX_6A2CA10C62FF6CDF ON media (consultation_id)');
        $this->addSql('ALTER TABLE traitement ADD consultation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE traitement ADD CONSTRAINT FK_2A356D2762FF6CDF FOREIGN KEY (consultation_id) REFERENCES consultation (id)');
        $this->addSql('CREATE INDEX IDX_2A356D2762FF6CDF ON traitement (consultation_id)');
        $this->addSql('ALTER TABLE user ADD date_creation DATETIME NOT NULL, CHANGE roles roles VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE animal DROP FOREIGN KEY FK_6AAB231F76C50E4A');
        $this->addSql('DROP INDEX IDX_6AAB231F76C50E4A ON animal');
        $this->addSql('ALTER TABLE animal CHANGE proprietaire_id propriétaire_id INT NOT NULL, CHANGE espece espèce VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE animal ADD CONSTRAINT FK_6AAB231FF917CCFC FOREIGN KEY (propriétaire_id) REFERENCES client (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_6AAB231FF917CCFC ON animal (propriétaire_id)');
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10C62FF6CDF');
        $this->addSql('DROP INDEX IDX_6A2CA10C62FF6CDF ON media');
        $this->addSql('ALTER TABLE media DROP consultation_id');
        $this->addSql('ALTER TABLE client DROP email, DROP telephone, DROP adresse, CHANGE prenom prénom VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE consultation DROP FOREIGN KEY FK_964685A65C80924');
        $this->addSql('DROP INDEX IDX_964685A65C80924 ON consultation');
        $this->addSql('ALTER TABLE consultation ADD vétérinaire_id INT NOT NULL, ADD date_création DATETIME NOT NULL, ADD date_rendez_vous DATETIME NOT NULL, DROP veterinaire_id, DROP date_creation, DROP date_consultation, DROP est_paye, DROP montant_total, DROP notes, CHANGE statut statut VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE consultation ADD CONSTRAINT FK_964685A61186E1FE FOREIGN KEY (vétérinaire_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_964685A61186E1FE ON consultation (vétérinaire_id)');
        $this->addSql('ALTER TABLE traitement DROP FOREIGN KEY FK_2A356D2762FF6CDF');
        $this->addSql('DROP INDEX IDX_2A356D2762FF6CDF ON traitement');
        $this->addSql('ALTER TABLE traitement DROP consultation_id');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74 ON user');
        $this->addSql('ALTER TABLE user DROP date_creation, CHANGE roles roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\'');
    }
}
