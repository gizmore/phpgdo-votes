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
    public bool $dislikes = false;
    public function dislikes(bool $dislikes=true): static
    {
        $this->dislikes = $dislikes;
        return $this;
    }

	public bool $writeable = false;

	public function gdtDefaultLabel(): ?string
    {
        return 'likes';
    }

	public function isTestable(): bool
	{
		return false;
	}

	public function renderHTML(): string
	{
		return GDT_Template::php('Votes', 'cell/like_count.php', ['field' => $this]);
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
