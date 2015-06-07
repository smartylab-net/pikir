<?php
namespace Info\NotificationBundle\DBAL;

use Strokit\CoreBundle\DBAL\EnumType;

class NotificationTypeEnum extends EnumType
{
    const COMMENT_REPLY = 'comment_reply';

    const COMMENT_TO_COMPLAINT = 'comment';

    const COMPLAINT_TO_COMPANY = 'complaint';
    protected $name = 'notification_type';
    protected $values = array(self::COMMENT_REPLY, self::COMMENT_TO_COMPLAINT, self::COMPLAINT_TO_COMPANY);

    public static function getArray()
    {
        return array(self::COMMENT_REPLY => self::COMMENT_REPLY, self::COMMENT_TO_COMPLAINT => self::COMMENT_TO_COMPLAINT, self::COMPLAINT_TO_COMPANY => self::COMPLAINT_TO_COMPANY);
    }
}