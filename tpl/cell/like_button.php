<?php
namespace GDO\Votes\tpl\cell;

use GDO\UI\GDT_Button;
use GDO\User\GDO_User;
use GDO\Votes\GDT_LikeButton;
use GDO\Votes\GDT_LikeCount;

/** @var $field GDT_LikeButton * */
$user = GDO_User::current();
$gdo = $field->getLikeObject();
$liked = $gdo->hasLiked($user);
$likes = GDT_LikeCount::make()->gdo($gdo)->render();
echo GDT_Button::make()->addClass($liked ? 'liked' : '')->addClass('gdt-like-button')->icon('like')->href($field->href)->writeable($field->writeable)->labelRaw($likes)->render();
