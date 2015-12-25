<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150725031358 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("ALTER TABLE `notifications` ALTER `type` DROP DEFAULT");
        $this->addSql("ALTER TABLE `notifications` CHANGE COLUMN `type` `type` ENUM('comment_reply','comment','complaint','comment_report','complaint_report','complaint_vote') NOT NULL COMMENT '(DC2Type:notification_type)' COLLATE 'utf8_unicode_ci' AFTER `user_id`");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("ALTER TABLE `notifications` ALTER `type` DROP DEFAULT");
        $this->addSql("ALTER TABLE `notifications` CHANGE COLUMN `type` `type` ENUM('comment_reply','comment','complaint','comment_report','complaint_report') NOT NULL COMMENT '(DC2Type:notification_type)' COLLATE 'utf8_unicode_ci' AFTER `user_id`");
    }
}
