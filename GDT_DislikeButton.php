<?php
namespace GDO\Votes;

use GDO\Core\GDO;
use GDO\Core\GDT;
use GDO\Core\GDT_Template;
use GDO\UI\GDT_Button;
use GDO\User\GDO_User;

class GDT_DislikeButton extends GDT_Button
{

	protected function __construct()
	{
		parent::__construct();
		$this->icon('dislike');
	}

	public function gdtDefaultLabel(): ?string
    {
        return 'dislikes';
    }

	public function gdo(?GDO $gdo): static
	{
		parent::gdo($gdo);
		$likeObject = $this->getLikeObject();
		$likeTable = $this->getLikeTable();
		$hasLiked = $likeObject->hasDisliked(GDO_User::current());
		$method = $hasLiked ? 'UnLike' : 'Dislike';
		$this->href = hrefNoSeo('Votes', $method, "&gdo={$likeTable->gdoClassName()}&id={$likeObject->getID()}");
// 		$this->editable(!$gdo->hasLiked(GDO_User::current()));
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
		return GDT_Template::php('Votes', 'cell/dislike_button.php', ['field' => $this]);
	}


	public function configJSON(): array
	{
		return array_merge(parent::configJSON(), [
			'count' => $this->getLikeObject()->getDislikeCount(),
		]);
	}

	public function renderJSON(): array|string|null|int|bool|float
	{
		return [
            'html_like' => GDT_LikeButton::make()->gdo($this->gdo)->renderHTML(),
            'html_dislike' => $this->renderHTML(),
            'count_like' => $this->getLikeObject()->getLikeCount(),
            'count_dislike' => $this->getLikeObject()->getDislikeCount(),
		];
	}

}
