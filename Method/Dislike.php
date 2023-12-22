<?php
namespace GDO\Votes\Method;

use GDO\Core\Application;
use GDO\Core\GDO;
use GDO\Core\GDT;
use GDO\Core\GDT_Checkbox;
use GDO\Core\GDT_CreatedBy;
use GDO\Core\GDT_Enum;
use GDO\Core\GDT_Object;
use GDO\Core\GDT_Response;
use GDO\Core\GDT_String;
use GDO\Core\Method;
use GDO\Date\Time;
use GDO\Net\GDT_IP;
use GDO\UI\GDT_Redirect;
use GDO\User\GDO_User;
use GDO\Util\Common;
use GDO\Votes\GDO_LikeTable;
use GDO\Votes\GDT_LikeButton;
use GDO\Votes\Module_Votes;

/**
 * The method to like an item.
 *
 * @version 7.0.1
 * @since 5.0.0
 * @author gizmore
 */
class Dislike extends Like
{

    protected function getLikeScore(): int
    {
        return -1;
    }

}
