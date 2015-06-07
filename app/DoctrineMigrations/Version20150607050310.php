<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150607050310 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE notifications (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, type ENUM(\'comment_reply\', \'comment\', \'complaint\') COMMENT \'(DC2Type:notification_type)\' NOT NULL COMMENT \'(DC2Type:notification_type)\', element_id INT NOT NULL, url VARCHAR(100) NOT NULL, is_read TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_6000B0D3A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D3A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE fos_user_user ADD notify_on_new_complaint TINYINT(1) DEFAULT NULL, ADD notify_on_new_comment TINYINT(1) DEFAULT NULL, ADD notify_on_reply_to_comment TINYINT(1) DEFAULT NULL');
        $this->addSql('UPDATE fos_user_user set notify_on_new_complaint=1, notify_on_new_comment=1, notify_on_reply_to_comment=1');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE notifications');
        $this->addSql('ALTER TABLE fos_user_user DROP notify_on_new_complaint, DROP notify_on_new_comment, DROP notify_on_reply_to_comment');
    }
}
