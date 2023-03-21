<?php
namespace GDO\Votes;

use GDO\Core\GDO;
use GDO\Core\GDT;
use GDO\Core\GDT_Decimal;
use GDO\Core\GDT_Template;
use GDO\Core\WithGDO;

final class GDT_VoteRating extends GDT_Decimal
{

	use WithGDO;

	protected function __construct()
	{
		parent::__construct();
		$this->writeable = false;
		$this->digits(2, 2);
		$this->notNull();
		$this->initial('0');
	}

	public function defaultLabel(): self { return $this->label('rating'); }

	public function isDefaultAsc(): bool { return false; }

	public function renderHTML(): string
	{
		if (!isset($this->gdo))
		{
			return GDT::EMPTY_STRING;
		}
		return GDT_Template::php('Votes', 'cell/vote_rating.php', ['field' => $this]);
	}

	/**
	 * @return GDO|WithVotes
	 */
	public function getVoteObject(): GDO { return $this->gdo; }

}
