<?php
namespace GDO\Votes\Method;

use GDO\Core\Application;
use GDO\Core\GDO;
use GDO\Core\GDT;
use GDO\Core\GDT_Checkbox;
use GDO\Core\GDT_CreatedBy;
use GDO\Core\GDT_Enum;
use GDO\Core\GDT_Object;
use GDO\Core\GDT_Response;
use GDO\Core\GDT_String;
use GDO\Core\Method;
use GDO\Date\Time;
use GDO\Net\GDT_IP;
use GDO\UI\GDT_Redirect;
use GDO\User\GDO_User;
use GDO\Util\Common;
use GDO\Votes\GDO_LikeTable;
use GDO\Votes\GDT_DislikeButton;
use GDO\Votes\GDT_LikeButton;
use GDO\Votes\Module_Votes;

/**
 * The method to like an item.
 *
 * @version 7.0.1
 * @since 5.0.0
 * @author gizmore
 */
class Like extends Method
{

    protected function getLikeScore(): int
    {
        return 1;
    }

    public function isTrivial(): bool { return false; }

	public function isUserRequired(): bool { return true; }

    public function isGuestAllowed(): bool
    {
        return $this->getLikeTable()->gdoLikeForGuests();
    }

    public function isShownInSitemap(): bool { return false; }

	public function getMethodTitle(): string
	{
		return t('votes');
	}

	public function gdoParameters(): array
	{
		return [
			GDT_String::make('gdo')->notNull(),
			GDT_Object::make('id')->notNull(),
            GDT_Enum::make('direction')->enumValues('up', 'down')->notNull(),
		];
	}

    protected function afterAddCompose(): void
    {
        $this->gdoParameter('id', false)->table($this->getLikeTable());
    }

    /**
	 * @return GDO_LikeTable
	 */
	public function getLikeTable()
	{
		return call_user_func([$this->getLikeTableClass(), 'table']);
	}

	public function getLikeTableClass(): string
	{
		return $this->gdoParameterVar('gdo');
	}

	public function execute(): GDT
	{
		$user = GDO_User::current();

		# Get LikeTable, e.g. GDO_CommentLike
		$class = $this->getLikeTableClass();
		if (!class_exists($class))
		{
			return $this->error('err_vote_gdo');
		}
		if (!is_subclass_of($class, GDO_LikeTable::class))
		{
			return $this->error('err_vote_table');
		}
        /** @var GDO_LikeTable $table */
		$table = GDO::tableFor($class);

		if ((!$user->isMember()) && (!$table->gdoLikeForGuests()))
		{
			return $this->error('err_members_only');
		}

		# Get GDO table, e.g. Link
		$objects = $table->gdoLikeObjectTable();

		# Get GDO row, e.g. Link
		$object = $objects->find($this->getInputFor('id'));

		if (!$object->gdoCanLike($user))
		{
			return $this->error('err_cannot_like');
		}

		# Check IP count
//		$count = $table->countWhere(sprintf("like_object=%s AND like_ip='%s'", $object->getID(), GDT_IP::current()));
//		if ($count >= $table->gdoMaxLikeCount())
//		{
//			return $this->error('err_vote_ip');
//		}

		# Check user count
//		$count = $table->countWhere(sprintf("like_object=%s AND like_user='%s'", $object->getID(), $user->getID()));
//		if ($count >= $table->gdoMaxLikeCount())
//		{
//			return $this->error('err_vote_count', [$table->gdoMaxLikeCount()]);
//		}

		# Check last vote date against cooldown
//		$lastVoteDate = $table->select('like_created')->
//		where(sprintf('like_object=%s', $object->getID()))->
//		where(sprintf("like_user=%s or like_ip='%s'", $user->getID(), GDT_IP::current()))->
//		order('like_created DESC')->
//		first()->exec()->fetchVar();
//		if ($lastVoteDate && (Time::getAgo($lastVoteDate) < $table->gdoLikeCooldown()))
//		{
//			return $this->error('err_vote_frequency', [Time::humanDuration($table->gdoLikeCooldown())]);
//		}

        /** @var GDO_LikeTable $like */
		# Vote
		$like = $class::blank([
			'like_user' => $user->getID(),
			'like_object' => $object->getID(),
            'like_score' => $this->getLikeScore(),
			'like_ip' => GDT_IP::current(),
		]);
		$like->softReplace();

		# Update cache
		$object->updateLikes();

		# Update user likes
//		if ($otherUser = $object->gdoColumnOf(GDT_CreatedBy::class))
//		{
//			$otherUser = $otherUser->getValue();
//			Module_Votes::instance()->saveUserSetting($otherUser, 'likes');
//		}

		if (Application::instance()->isCLI())
		{
			return $this->message('msg_liked');
		}

        if ($this->getLikeScore() < 0)
        {
            return GDT_Response::makeWith(
                GDT_DislikeButton::make('likes')->gdo($object),
                GDT_Redirect::to(GDT_Redirect::hrefBack()),
            );
        }
        else
        {
            return GDT_Response::makeWith(
                GDT_LikeButton::make('likes')->gdo($object),
                GDT_Redirect::to(GDT_Redirect::hrefBack()),
            );
        }

	}

}
