<?php
namespace GDO\Votes;

use GDO\Core\GDO;
use GDO\Core\GDT;
use GDO\Core\GDT_Template;
use GDO\UI\GDT_Button;
use GDO\User\GDO_User;

class GDT_LikeButton extends GDT_Button
{

	protected function __construct()
	{
		parent::__construct();
		$this->icon('like');
        $this->noFollow();
	}

	public function gdtDefaultLabel(): ?string
    {
        return 'likes';
    }

	public function gdo(?GDO $gdo): static
	{
		parent::gdo($gdo);
		$likeObject = $this->getLikeObject();
		$likeTable = $this->getLikeTable();
		$hasLiked = $likeObject->hasLiked(GDO_User::current());
		$method = $hasLiked ? 'UnLike' : 'Like';
		$this->href = hrefNoSeo('Votes', $method, "&gdo={$likeTable->gdoClassName()}&id={$likeObject->getID()}");
        $this->writeable($likeObject->gdoCanLike(GDO_User::current()));
		return $this;
	}

	/**
	 * @return WithLikes
	 */
	public function getLikeObject()
	{
		return $this->gdo;
	}

	/**
	 * @return GDO_LikeTable
	 */
	public function getLikeTable()
	{
		return $this->getLikeObject()->gdoLikeTable();
	}

	public function renderHTML(): string
	{
		return GDT_Template::php('Votes', 'cell/like_button.php', ['field' => $this]);
	}


	public function configJSON(): array
	{
		return array_merge(parent::configJSON(), [
			'count' => $this->getLikeObject()->getLikeCount(),
		]);
	}

	public function renderJSON(): array|string|null|int|bool|float
	{
        $dislike = GDT_DislikeButton::make()->gdo($this->gdo);
		return [
			'html_like' => $this->renderHTML(),
            'html_dislike' => $dislike->renderHTML(),
            'count_like' => $this->getLikeObject()->getLikeCount(),
            'count_dislike' => $this->getLikeObject()->getDislikeCount(),
		];
	}

}
