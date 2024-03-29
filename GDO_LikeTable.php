<?php
namespace GDO\Votes;

use GDO\Core\GDO;
use GDO\Core\GDT_AutoInc;
use GDO\Core\GDT_CreatedAt;
use GDO\Core\GDT_Int;
use GDO\Core\GDT_Object;
use GDO\Net\GDT_IP;
use GDO\User\GDO_User;
use GDO\User\GDT_User;

class GDO_LikeTable extends GDO
{

	public function gdoLikeForGuests() { return true; }

	public function gdoMaxLikeCount() { return 1; }

	public function gdoLikeCooldown() { return 60 * 60 * 24; }	/**
	 * @return GDO
	 */
	public function gdoLikeObjectTable() { return null; }

	public function gdoCached(): bool { return false; }

	public function getLike(GDO_User $user, GDO $likeObject)
	{
		return $this->select()->
		where(sprintf('like_user=%s AND like_object=%s', $user->getID(), $likeObject->getID()))->
		first()->exec()->fetchObject();
	}

    public function getScore(): int
    {
        return $this->gdoValue('like_score');
    }

    public function isPositive(): bool
    {
        return $this->getScore() > 0;
    }

    public function isNegative(): bool
    {
        return $this->getScore() < 0;
    }

    public function gdoAbstract(): bool { return $this->gdoLikeObjectTable() === null; }

	public function isTestable(): bool { return !$this->gdoAbstract(); }

	public function gdoColumns(): array
	{
		return [
//			GDT_AutoInc::make('like_id'),
			GDT_User::make('like_user')->notNull()->primary(),
			GDT_Object::make('like_object')->table($this->gdoLikeObjectTable())->notNull()->primary(),
            GDT_Int::make('like_score')->bytes(1)->notNull()->min(-1)->max(1),
			GDT_IP::make('like_ip')->useCurrent()->notNull(),
			GDT_CreatedAt::make('like_created'),
		];
	}


}
