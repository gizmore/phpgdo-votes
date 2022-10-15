<?php
namespace GDO\Votes\Method;

use GDO\Core\Application;
use GDO\Core\GDT_Object;
use GDO\Core\GDT_String;
use GDO\Core\Method;
use GDO\Core\GDO;
use GDO\Net\GDT_IP;
use GDO\Core\GDT_Response;
use GDO\UI\GDT_Redirect;
use GDO\User\GDO_User;
use GDO\Util\Common;
use GDO\Votes\GDO_LikeTable;
use GDO\Votes\GDT_LikeButton;
use GDO\Votes\Module_Votes;
use GDO\Date\Time;
use GDO\Core\GDT_CreatedBy;

/**
 * The method to like an item.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 5.0.0
 */
class Like extends Method
{
    public function isCLI() : bool { return false; }
    public function isTrivial() : bool { return false; }
    public function isUserRequired() : bool { return true; }
    public function isShownInSitemap() : bool { return false; }
    
    public function getMethodTitle() : string
    {
    	return t('votes');
    }
    
    public function gdoParameters() : array
	{
		return [
			GDT_String::make('gdo')->notNull(),
		    GDT_Object::make('id')->table($this->getLikeTable())->notNull(),
		];
	}

	/**
	 * @return GDO_LikeTable
	 */
	public function getLikeTable()
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
		
		if (!$object->gdoCanLike($user))
		{
		    return $this->error('err_cannot_like');
		}
		
		# Check IP count
		$count = $table->countWhere(sprintf("like_object=%s AND like_ip='%s'", $object->getID(), GDT_IP::current()));
		if ($count >= $table->gdoMaxLikeCount())
		{
			return $this->error('err_vote_ip');
		}
		
		# Check user count
		$count = $table->countWhere(sprintf("like_object=%s AND like_user='%s'", $object->getID(), $user->getID()));
		if ($count >= $table->gdoMaxLikeCount())
		{
			return $this->error('err_vote_count', [$table->gdoMaxLikeCount()]);
		}
		
		# Check last vote date against cooldown
		$lastVoteDate = $table->select('like_created')->
			where(sprintf("like_object=%s", $object->getID()))->
			where(sprintf("like_user=%s or like_ip='%s'", $user->getID(), GDT_IP::current()))->
			order('like_created DESC')->
			first()->exec()->fetchValue();
		if ( $lastVoteDate && (Time::getAgo($lastVoteDate) < $table->gdoLikeCooldown()) )
		{
			return $this->error('err_vote_frequency', [Time::humanDuration($table->gdoLikeCooldown())]);
		}
		
		# Vote
		$like = $class::blank(array(
			'like_user' => $user->getID(),
			'like_object' => $object->getID(),
			'like_ip' => GDT_IP::current(),
		));
		$like instanceof GDO_LikeTable;
		$like->insert();
		
		# Update cache
		$object->updateLikes();

		# Update user likes
		if ($otherUser = $object->gdoColumnOf(GDT_CreatedBy::class))
		{
		    $otherUser = $otherUser->getValue();
		    Module_Votes::instance()->increaseUserSetting($otherUser, 'likes');
		}
		
		if (Application::instance()->isCLI())
		{
		    return $this->message('msg_liked');
		}
		
		return GDT_Response::makeWith(
			GDT_LikeButton::make('likes')->gdo($object),
			GDT_Redirect::to(GDT_Redirect::hrefBack()),
		);
	}
}
