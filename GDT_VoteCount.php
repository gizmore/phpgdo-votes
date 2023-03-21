<?php
namespace GDO\Votes;

use GDO\Core\GDO;
use GDO\Core\GDT_Template;
use GDO\Core\GDT_UInt;
use GDO\Core\WithGDO;

/**
 * Display a votecount.
 *
 * @version 7.0.1
 * @since 6.5.0
 * @author gizmore
 */
class GDT_VoteCount extends GDT_UInt
{

	use WithGDO;

	protected function __construct()
	{
		parent::__construct();
		$this->initial = '0';
		$this->writeable = false;
	}

	public function isTestable(): bool
	{
		return false;
	}

	public function defaultLabel(): self
	{
		return $this->label('votes');
	}

	public function renderHTML(): string
	{
		return GDT_Template::php('Votes', 'votecount_html.php', [
			'field' => $this]);
	}

	public function getVoteObject(): GDO
	{
		return $this->gdo;
	}

}
