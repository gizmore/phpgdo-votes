<?php
namespace GDO\Votes\tpl\cell;

use GDO\UI\GDT_Button;
use GDO\User\GDO_User;
use GDO\Votes\GDT_DislikeCount;
use GDO\Votes\GDT_LikeButton;
use GDO\Votes\GDT_LikeCount;

/** @var $field GDT_LikeButton * */
$user = GDO_User::current();
$gdo = $field->getLikeObject();
$liked = $gdo->hasDisliked($user);
$likes = GDT_DislikeCount::make()->gdo($gdo)->render();
echo GDT_Button::make()->addClass($liked ? 'liked' : '')->addClass('gdt-like-button')->icon('thumbs_down')->href($field->href)->writeable($field->writeable)->labelRaw($likes)->render();
