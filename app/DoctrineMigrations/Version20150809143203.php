<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150809143203 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE versions (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, version LONGTEXT NOT NULL, created_at DATETIME NOT NULL, targetComment_id INT DEFAULT NULL, targetComplaint_id INT DEFAULT NULL, INDEX IDX_19DC40D2CE1CADF2 (targetComment_id), INDEX IDX_19DC40D22C183E82 (targetComplaint_id), INDEX IDX_19DC40D2A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE versions ADD CONSTRAINT FK_19DC40D2CE1CADF2 FOREIGN KEY (targetComment_id) REFERENCES Comment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE versions ADD CONSTRAINT FK_19DC40D22C183E82 FOREIGN KEY (targetComplaint_id) REFERENCES Complaint (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE versions ADD CONSTRAINT FK_19DC40D2A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE versions');
    }
}
