<?php
namespace GDO\Votes;

use GDO\Core\GDT_Template;
use GDO\Core\GDT_UInt;

class GDT_VoteCount extends GDT_UInt
{
	public $writable = false;
	public $editable = false;
	
	public function defaultLabel() : self { return $this->label('votes'); }

	protected function __construct()
	{
		parent::__construct();
		$this->initial = "0";
	}
	
	public function getVoteObject()
	{
		return $this->gdo;
	}
	
	public function renderHTML() : string
	{
		return GDT_Template::php('Vote', 'cell/vote_count.php', ['field'=>$this]);
	}
	
}
