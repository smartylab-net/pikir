<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150628162235 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE Complaint DROP FOREIGN KEY FK_DDD6B016472B783A');
        $this->addSql('ALTER TABLE Complaint ADD CONSTRAINT FK_DDD6B016472B783A FOREIGN KEY (gallery) REFERENCES media__gallery (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE Comment DROP FOREIGN KEY FK_5BC96BF0EDAE188E');
        $this->addSql('ALTER TABLE Comment ADD CONSTRAINT FK_5BC96BF0EDAE188E FOREIGN KEY (complaint_id) REFERENCES Complaint (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE Comment DROP FOREIGN KEY FK_5BC96BF0EDAE188E');
        $this->addSql('ALTER TABLE Comment ADD CONSTRAINT FK_5BC96BF0EDAE188E FOREIGN KEY (complaint_id) REFERENCES Complaint (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE Complaint DROP FOREIGN KEY FK_DDD6B016472B783A');
        $this->addSql('ALTER TABLE Complaint ADD CONSTRAINT FK_DDD6B016472B783A FOREIGN KEY (gallery) REFERENCES media__gallery (id)');
    }
}
