<?php
namespace GDO\Votes;

use GDO\Core\GDT_Template;

/**
 * A like count field.
 * 
 * @author gizmore
 */
class GDT_LikeCount extends GDT_VoteCount
{
	public $writable = false;
	public $editable = false;
	
	public function defaultLabel() : self { return $this->label('likes'); }

	public function getLikeObject()
	{
		return $this->gdo;
	}
	
	/**
	 * @return GDO_LikeTable
	 */
	public function getLikeTable()
	{
		return $this->gdo->gdoLikeTable();
	}
	
	public function renderHTML() : string
	{
		return GDT_Template::php('Votes', 'cell/like_count.php', ['field'=>$this]);
	}
	
}
