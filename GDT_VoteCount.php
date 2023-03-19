<?php
namespace GDO\Votes;

use GDO\Core\GDO;
use GDO\Core\GDT_Template;
use GDO\Core\GDT_UInt;
use GDO\Core\WithGDO;

/**
 * Display a votecount.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.5.0
 */
class GDT_VoteCount extends GDT_UInt
{
	use WithGDO;
	
	public function isTestable(): bool
	{
		return false;
	}
	
	public function defaultLabel(): static
	{
		return $this->label('votes');
	}

	protected function __construct()
	{
		parent::__construct();
		$this->initial = "0";
		$this->writeable = false;
	}
	
	public function getVoteObject() : GDO
	{
		return $this->gdo;
	}
	
	public function renderHTML() : string
	{
		return GDT_Template::php('Votes', 'votecount_html.php', [
			'field' => $this]);
	}
	
}
