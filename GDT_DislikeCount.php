<?php
namespace GDO\Votes;

use GDO\Core\GDT_Template;

/**
 * A like count field.
 *
 * @author gizmore
 */
class GDT_DislikeCount extends GDT_VoteCount
{
	public bool $writeable = false;

	public function gdtDefaultLabel(): ?string
    {
        return 'dislikes';
    }

	public function isTestable(): bool
	{
		return false;
	}

	public function renderHTML(): string
	{
		return GDT_Template::php('Votes', 'cell/dislike_count.php', ['field' => $this]);
	}

	public function getLikeObject()
	{
		return $this->gdo;
	}

	public function getLikeTable(): GDO_LikeTable
	{
		return $this->gdo->gdoLikeTable();
	}

}
