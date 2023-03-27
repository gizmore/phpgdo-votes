<?php
namespace GDO\Votes;

use GDO\Core\GDO;
use GDO\Core\GDT;
use GDO\Core\GDT_Template;
use GDO\UI\GDT_Button;
use GDO\User\GDO_User;

class GDT_LikeButton extends GDT_Button
{

	public $dislike = false;

	protected function __construct()
	{
		parent::__construct();
		$this->icon('like');
	}

	public function defaultLabel(): self { return $this->label('likes'); }

	public function gdo(?GDO $gdo): GDT
	{
		parent::gdo($gdo);
		$likeObject = $this->getLikeObject();
		$likeTable = $this->getLikeTable();
		$hasLiked = $likeObject->hasLiked(GDO_User::current());
		$method = $hasLiked ? 'UnLike' : 'Like';
		$this->href = href('Votes', $method, "&gdo={$likeTable->gdoClassName()}&id={$likeObject->getID()}");
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

	public function dislike($dislike = true)
	{
		$this->dislike = $dislike;
		return $this;
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

	public function renderJSON(): array|string|null
	{
		return [
			'html' => $this->renderHTML(),
			'count' => $this->getLikeObject()->getLikeCount(),
		];
	}

}
