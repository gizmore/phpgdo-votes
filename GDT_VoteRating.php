<?php
namespace GDO\Votes;

use GDO\Core\GDO;
use GDO\Core\GDT_Template;
use GDO\Core\GDT_Decimal;

final class GDT_VoteRating extends GDT_Decimal
{
    public $orderDefaultAsc = false;
    
    public function defaultLabel() : self { return $this->label('rating'); }
	
	/**
	 * @return GDO|WithVotes
	 */
	public function getVoteObject() { return $this->gdo; }
	
	protected function __construct()
	{
	    $this->writable = false;
	    $this->editable = false;
		$this->digits(2, 2);
		$this->notNull();
		$this->initial('0');
	}

	public function renderHTML() : string
	{
		return GDT_Template::php('Votes', 'cell/vote_rating.php', ['field'=>$this]);
	}
	
}
