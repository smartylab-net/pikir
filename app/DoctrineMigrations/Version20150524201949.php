<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150524201949 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ComplaintsCommentRating DROP FOREIGN KEY FK_49113F05EDAE188E');
        $this->addSql('ALTER TABLE ComplaintsCommentRating DROP FOREIGN KEY FK_49113F05F8697D13');
        $this->addSql('DROP INDEX IDX_49113F05EDAE188E ON ComplaintsCommentRating');
        $this->addSql('DROP INDEX IDX_49113F05F8697D13 ON ComplaintsCommentRating');
        $this->addSql('ALTER TABLE ComplaintsCommentRating ADD element_id INT NOT NULL, ADD type VARCHAR(255) NOT NULL');
        $this->addSql('Update ComplaintsCommentRating c set type="comment", element_id=comment_id where c.comment_id IS NOT NULL');
        $this->addSql('Update ComplaintsCommentRating c set type="complaint", element_id=complaint_id where c.complaint_id IS NOT NULL');
        $this->addSql('ALTER TABLE ComplaintsCommentRating DROP complaint_id, DROP comment_id');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_800230D3989D9B62 ON Company');
        $this->addSql('ALTER TABLE ComplaintsCommentRating ADD complaint_id INT DEFAULT NULL, ADD comment_id INT DEFAULT NULL');
        $this->addSql('Update ComplaintsCommentRating c comment_id=element_id where type="comment"');
        $this->addSql('Update ComplaintsCommentRating c complaint=element_id where type="complaint"');
        $this->addSql('ALTER TABLE ComplaintsCommentRating DROP element_id, DROP type');
        $this->addSql('ALTER TABLE ComplaintsCommentRating ADD CONSTRAINT FK_49113F05EDAE188E FOREIGN KEY (complaint_id) REFERENCES Complaint (id)');
        $this->addSql('ALTER TABLE ComplaintsCommentRating ADD CONSTRAINT FK_49113F05F8697D13 FOREIGN KEY (comment_id) REFERENCES Comment (id)');
    }
}
