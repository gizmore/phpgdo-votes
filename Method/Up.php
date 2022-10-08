<?php
namespace GDO\Votes\Method;

use GDO\Core\GDT_String;
use GDO\Core\Method;
use GDO\Core\GDO;
use GDO\Net\GDT_IP;
use GDO\Core\GDT_Response;
use GDO\User\GDO_User;
use GDO\Core\Application;
use GDO\Core\Website;
use GDO\Date\Time;
use GDO\Core\GDT_JSON;
use GDO\Core\GDT_UInt;
use GDO\Votes\GDO_VoteTable;

/**
 * Vote on an item.
 * Check for IP duplicates.
 * @author gizmore
 * @version 7.0.1
 * @since 5.0.0
 */
final class Up extends Method
{
    public function isShownInSitemap() : bool { return false; }
    public function isUserRequired() : bool { return true; }
    
    public function getMethodTitle() : string
    {
    	return t('votes');
    }
    
	public function gdoParameters() : array
	{
		return [
			GDT_String::make('gdo')->notNull(),
			GDT_UInt::make('id')->notNull(),
			GDT_UInt::make('rate')->min(1)->max(10)->notNull(),
		];
	}
	
	public function execute()
	{
		$user = GDO_User::current();
		
		# Get VoteTable, e.g. LinkVote
		$class = $this->gdoParameterVar('gdo');
		if (!@class_exists($class, true))
		{
			return $this->error('err_vote_gdo');
		}
		if (!is_subclass_of($class, GDO_VoteTable::class))
		{
			return $this->error('err_vote_table');
		}
		$table = GDO::tableFor($class);
// 		$table instanceof GDO_VoteTable;
		
		# Get GDO table, e.g. Link
		$objects = $table->gdoVoteObjectTable();
// 		$objects instanceof GDO;
		
		# Get GDO row, e.g. Link
		/**
		 * @var GDO $object
		 */
		$object = $objects->find($this->gdoParameterVar('id'));
		
		if ($user->isGuest() && (!$table->gdoVoteGuests()))
		{
		    return $this->error('err_vote_guest');
		}
		
		if (!$object->gdoVoteAllowed($user))
		{
		    return $this->error('err_vote_not_allowed')->addField(GDT_Redirect::toBack(5));
		}
		
		# Check rate value
		if ( (!($value = $this->gdoParameterValue('rate'))) ||
			 (($value < 1) || ($value > $table->gdoVoteMax())) )
		{
			return $this->error('err_rate_param_between', [1, $table->gdoVoteMax()]);
		}
		
		$cooldown = Time::getDate(Application::$TIME - $table->gdoVoteCooldown());
		$where = sprintf("vote_object=%s AND vote_ip='%s' AND vote_user!=%s AND vote_created>='%s'",
			$object->getID(), GDT_IP::current(), $user->getID(), $cooldown);
		$count = $table->countWhere($where);
		
		if ($count === 0)
		{
			# Vote
			$vote = $class::blank([
				'vote_user' => $user->persistent()->getID(),
				'vote_object' => $object->getID(),
				'vote_ip' => GDT_IP::current(),
				'vote_value' => $value,
			]);
// 			$vote instanceof GDO_VoteTable;
			$vote->replace();
			
			# Update cache
			$object->setVar('own_vote', $value);
			$object->updateVotes();
// 			$object instanceof WithVotes;
			$rateColumn = $object->getVoteRatingColumn();
			
			if (Application::instance()->isAjax())
			{
				$enough = $object->hasEnoughVotes();
				$count = $enough ? $object->displayVoteCount() : -1;
				$rating = $enough ? $object->displayVoteRating() : -1;
				return GDT_Response::makeWith(
					GDT_JSON::make()->value([
						'message' => t('msg_voted'),
					    'outcome' => $rateColumn->render() . $object->getVoteCountColumn()->render(),
						'outcomeId' => $object->getVoteOutcomeId(),
						'enough' => $enough,
						'count' => $count,
						'rating' => $rating,
					])
				);
			}
			return $this->redirectMessage('msg_voted');
		}
		return $this->error('err_vote_ip');
	}
	
}
