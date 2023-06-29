<?php
namespace GDO\Votes;

use GDO\Core\GDT_Template;
use GDO\UI\GDT_Button;

/**
 * Show a trophy with level badge.
 * A tooltip explains if your access is granted or restricted.
 *
 * @author gizmore
 */
final class GDT_VotePopup extends GDT_Button
{

	public $level = 0;

//	public function gdtDefaultName(): ?string
//	{
//		return 'vote';
//	}

	public function level($level)
	{
		$this->level = $level;
		return $this;
	}

	public function renderCell(): string
	{
		return GDT_Template::php('Votes', 'cell/vote_popup.php', ['field' => $this]);
	}

}
