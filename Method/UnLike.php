<?php
namespace GDO\Votes\Method;

use GDO\Core\GDT_CreatedBy;
use GDO\Core\GDT_Object;
use GDO\Core\GDT_String;
use GDO\Core\Method;
use GDO\Core\GDO;
use GDO\Core\GDT_Response;
use GDO\User\GDO_User;
use GDO\Util\Common;
use GDO\Votes\GDO_LikeTable;
use GDO\Votes\GDT_LikeButton;
use GDO\Core\Website;
use GDO\Core\Application;
use GDO\Votes\Module_Votes;

/**
 * The method to like an item.
 * @author gizmore
 * @version 7.0.1
 * @since 5.0.0
 */
class UnLike extends Method
{
    public function isCLI() : bool { return false; }
    public function isTrivial() : bool { return false; }
    public function isUserRequired() : bool { return true; }
    public function isShownInSitemap() : bool { return false; }
    
    public function gdoParameters() : array
	{
	    return [
	        GDT_String::make('gdo')->notNull(),
	        GDT_Object::make('id')->table($this->getLikeTable())->notNull(),
	    ];
	}
	
	public function getLikeTable() : GDO_LikeTable
	{
	    return call_user_func([$this->getLikeTableClass(), 'table']);
	}
	
	public function getLikeTableClass() : string
	{
	    return $this->gdoParameterVar('gdo');
	}
	
	public function execute()
	{
		$user = GDO_User::current();
		
		# Get LikeTable, e.g. GDO_CommentLike
		$class = $this->getLikeTableClass();
		if (!class_exists($class))
		{
			return $this->error('err_vote_gdo');
		}
		if (!is_subclass_of($class, 'GDO\\Vote\\GDO_LikeTable'))
		{
			return $this->error('err_vote_table');
		}
		$table = GDO::tableFor($class);
		$table instanceof GDO_LikeTable;
		
		if ( (!$user->isMember()) && (!$table->gdoLikeForGuests()) )
		{
			return $this->error('err_members_only');
		}
		
		# Get GDO table, e.g. Link
		$objects = $table->gdoLikeObjectTable();
		
		# Get GDO row, e.g. Link
		$object = $objects->find(Common::getRequestString('id'));
		
		# Check user count
		$count = $table->countWhere(sprintf("like_object=%s AND like_user='%s'", $object->getID(), $user->getID()));
		if ($count < 1)
		{
			return $this->error('err_not_liked');
		}

		# Delete like
		$deleted = $table->deleteWhere("like_object={$object->getID()} AND like_user={$user->getID()}");

		# Update cache
		$object->updateLikes();
		
		if ($deleted)
		{
    		# Update user likes
    		if ($otherUser = $object->gdoColumnOf(GDT_CreatedBy::class))
    		{
    		    $otherUser = $otherUser->getValue();
    		    Module_Votes::instance()->increaseUserSetting($otherUser, 'likes', -1);
    		}
		}
		
		Website::redirectBack();
		
		if (Application::instance()->isCLI())
		{
		    return $this->message('msg_disliked');
		}
		
		return GDT_Response::makeWith(
			GDT_LikeButton::make('likes')->gdo($object)
		);
		
	}
}
