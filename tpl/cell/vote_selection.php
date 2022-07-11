<?php
namespace GDO\Votes\tpl\cell;
use GDO\Votes\GDT_VoteSelection;
use GDO\UI\GDT_Button;
/** @var $field GDT_VoteSelection **/
$vt = $field->voteTable();
$own = $field->ownVote();
$max = $vt->gdoVoteMax();
$can = $field->canVote();
?>
<?php for ($i = 1; $i <= $max; $i++) : ?>
<?php $color = $own < $i ? '#999' : '#ffd700'; ?>
<?= GDT_Button::make()->icon('star')->noFollow()->disabled(!$can)->iconColor($color)->href($field->hrefVoteScore($i))->render(); ?>
<?php endfor; ?>
