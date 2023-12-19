<?php
namespace GDO\Votes;

use GDO\User\GDO_User;

/**
 * Trait to add likes to your GDO.
 *
 * You have to implement:
 *
 *  - gdoLikeTable() which returns your inherited GDO_LikeTable.
 *  - a GDO inheriting from GDO_LikeTable. You can configure stuff there.
 *
 * @version 7.0.0
 * @since 6.2.0
 * @author gizmore
 */
trait WithLikes
{

	public function gdoCanLike(GDO_User $user)
	{
		return true;
	}

    public function hasLiked(GDO_User $user)
    {
        $like = $this->getLike($user);
        return $like === null ? false : $like->isPositive();
    }

    public function hasDisliked(GDO_User $user)
    {
        $like = $this->getLike($user);
        return $like === null ? false : $like->isNegative();
    }

    public function getLike(GDO_User $user)
	{
		$likes = $this->gdoLikeTable();
		$likes instanceof GDO_LikeTable;
		return $likes->getLike($user, $this);
	}

	public function updateLikes()
	{
		$vars = [];
		foreach ($this->gdoColumnsCache() as $gdt)
		{
			if ($gdt instanceof GDT_LikeCount)
			{
				$vars[$gdt->name] = $this->queryLikeCount();
			}
            elseif ($gdt instanceof GDT_DislikeCount)
            {
                $vars[$gdt->name] = $this->queryDislikeCount();
            }
		}
		return $this->saveVars($vars, false);
	}

    public function queryLikeCount()
    {
        $likes = $this->gdoLikeTable();
        $likes instanceof GDO_LikeTable;
        return (string) $likes->countWhere("like_object={$this->getID()} and like_score=1");
    }

    public function queryDislikeCount()
    {
        $likes = $this->gdoLikeTable();
        $likes instanceof GDO_LikeTable;
        return (string) $likes->countWhere("like_object={$this->getID()} and like_score=-1");
    }

    public function getLikeCount()
    {
        foreach ($this->gdoColumnsCache() as $gdt)
        {
            if ($gdt instanceof GDT_LikeCount)
            {
                return $this->gdoVar($gdt->name);
            }
        }
        return $this->queryLikeCount();
    }

    public function getDislikeCount()
    {
        foreach ($this->gdoColumnsCache() as $gdt)
        {
            if ($gdt instanceof GDT_DislikeCount)
            {
                return $this->gdoVar($gdt->name);
            }
        }
        return $this->queryDislikeCount();
    }

}
