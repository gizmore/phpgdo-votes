<?php
namespace GDO\Votes\tpl\cell;

use GDO\Votes\GDT_LikeCount;

/** @var $field GDT_LikeCount * */
$gdo = $field->gdo;
$id = $field->getLikeTable()->gdoShortName() . '-' . $gdo->getID() . '-likes';
printf('<div class="%s ib"><b>%s</b>&nbsp;%s</div>', $id, $gdo->getLikeCount(), t('btn_likes'));
