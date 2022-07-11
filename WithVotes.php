<?php
namespace GDO\Votes;

use GDO\User\GDO_User;

/**
 * To make a GDO votable do
 * 1. Create a table extend GDO_VoteTable
 * 2. Implement gdoVoteTable()
 * 
 * @author gizmore
 * @see GDO_VoteTable
 */
trait WithVotes
{
//	 public function gdoVoteTable()
//	 {
//	 }

    public function gdoVoteAllowed(GDO_User $user)
    {
        return true;
    }
    
	public function hasVoted(GDO_User $user)
	{
		return !!$this->getVote($user);
	}
	
	public function getVote(GDO_User $user)
	{
		$votes = $this->gdoVoteTable();
		$votes instanceof GDO_VoteTable;
		return $votes->getVote($user, $this);
	}
	
	public function updateVotes()
	{
		$vars = [];
		foreach ($this->gdoColumnsCache() as $gdt)
		{
			if ($gdt instanceof GDT_VoteCount)
			{
				$vars[$gdt->name] = $this->queryVoteCount();
			}
			elseif ($gdt instanceof GDT_VoteRating)
			{
				$vars[$gdt->name] = $this->queryVoteRating();
			}
		}
		return $this->saveVars($vars);
	}

	public function getVoteCount()
	{
		if ($column = $this->getVoteCountColumn())
		{
			return $column->getValue();
		}
		return $this->queryVoteCount();
	}
	
	/**
	 * @return GDT_VoteCount
	 */
	public function getVoteCountColumn()
	{
		foreach ($this->gdoColumnsCache() as $gdt)
		{
			if ($gdt instanceof GDT_VoteCount)
			{
				return $gdt->gdo($this);
			}
		}
	}
	
	public function queryVoteCount()
	{
		$votes = $this->gdoVoteTable();
		$votes instanceof GDO_VoteTable;
		return $votes->countWhere('vote_object='.$this->getID());
	}
	
	public function displayVoteCount()
	{
	    $count = $this->getVoteCount();
	    return t('vote_count',  [$count]);
	}
	
	public function displayVoteRating()
	{
		return sprintf('%.01f', $this->getVoteRating());
	}
	
	public function getVoteRating()
	{
		if ($column = $this->getVoteRatingColumn())
		{
			return $column->getVar();
		}
		return $this->queryVoteRating();
	}
	
	/**
	 * @return GDT_VoteRating
	 */
	public function getVoteRatingColumn()
	{
		foreach ($this->gdoColumnsCache() as $gdt)
		{
			if ($gdt instanceof GDT_VoteRating)
			{
				return $gdt->gdo($this);
			}
		}
	}
	
	public function queryVoteRating()
	{
		$votes = $this->gdoVoteTable();
		$votes instanceof GDO_VoteTable;
		return $votes->select('AVG(vote_value)')->where('vote_object='.$this->getID())->exec()->fetchValue();
	}
	
	public function hasEnoughVotes()
	{
		$votesNeeded = $this->gdoVoteTable()->gdoVotesBeforeOutcome();
		$votesHave = $this->getVoteCount();
		return $votesHave >= $votesNeeded;
	}
	
	public function getVoteOutcomeId()
	{
		return 'votes-' . $this->gdoTableName() . '-' . $this->getID();
	}
	
}
