<?php
namespace GDO\Votes;

use GDO\Core\GDT_Template;
use GDO\Core\GDO;
use GDO\User\GDO_User;
use GDO\UI\GDT_Button;

class GDT_LikeButton extends GDT_Button
{
	public function defaultLabel() : self { return $this->label('likes'); }
	
	protected function __construct()
	{
	    parent::__construct();
		$this->icon('like');
	}
	
	public function gdo(GDO $gdo=null)
	{
		parent::gdo($gdo);
		$likeObject = $this->getLikeObject();
		$likeTable = $this->getLikeTable();
		$hasLiked = $likeObject->hasLiked(GDO_User::current());
		$method = $hasLiked ? 'UnLike' : 'Like';
		$this->href = href('Vote', $method, "&gdo={$likeTable->gdoClassName()}&id={$likeObject->getID()}");
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
	
	public function renderCell() : string
	{
		return GDT_Template::php('Vote', 'cell/like_button.php', ['field'=>$this]);
	}
	
	public $dislike = false;
	public function dislike($dislike=true)
	{
	    $this->dislike = $dislike;
	    return $this;
	}
	
	public function configJSON()
	{
		return array_merge(parent::configJSON(), [
			'count' => $this->getLikeObject()->getLikeCount(),
		]);
	}
	
	public function renderJSON()
	{
	    return [
	        'html' => $this->renderCell(),
	        'count' => $this->getLikeObject()->getLikeCount(),
	    ];
	}
	
}
