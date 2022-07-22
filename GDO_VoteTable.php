<?php
namespace GDO\Votes;

use GDO\Core\GDO;
use GDO\Core\GDT_CreatedAt;
use GDO\Core\GDT_Int;
use GDO\Core\GDT_Object;
use GDO\Net\GDT_IP;
use GDO\User\GDT_User;
use GDO\User\GDO_User;

/**
 * Override gdoVoteObjectTable with, e.g. return SomeGDO::table();
 * 
 * @author gizmore
 */
class GDO_VoteTable extends GDO
{
	/**
	 * @return GDO
	 */
	public function gdoVoteObjectTable() {}
	public function gdoVoteMax() { return 5; }
	public function gdoVotesBeforeOutcome() { return 3; }
	public function gdoVoteCooldown() { return 60*60*24; }
	public function gdoVoteGuests() { return true; }
	public function gdoCached() : bool { return false; }
	public function gdoAbstract() : bool { return $this->gdoVoteObjectTable() === null; }
	public function gdoColumns() : array
	{
		return array(
			GDT_User::make('vote_user')->primary(),
			GDT_Object::make('vote_object')->table($this->gdoVoteObjectTable())->primary(),
			GDT_IP::make('vote_ip')->notNull(),
			GDT_Int::make('vote_value')->notNull()->unsigned()->bytes(1),
			GDT_CreatedAt::make('vote_created'),
		);
	}
	/**
	 * @return GDO_User
	 */
	public function getUser() { return $this->gdoValue('vote_user'); }
	public function getUserID() { return $this->gdoVar('vote_user'); }
	/**
	 * @return GDO
	 */
	public function getObject() { return $this->gdoValue('vote_object'); }
	public function getObjectID() { return $this->gdoVar('vote_object'); }
	public function getIP() { return $this->gdoVar('vote_ip'); }
	public function getScore() { return $this->gdoVar('vote_value'); }

	################
	### Factory ###
	###############
	/**
	 * @param GDO_User $user
	 * @param GDO $object
	 * @return self
	 */
	public function getVote(GDO_User $user, GDO $object)
	{
		return self::getById($user->getID(), $object->getID());
	}
	
}