<?php
namespace GDO\Votes\tpl\cell;
use GDO\UI\GDT_Badge;
use GDO\Votes\GDT_VoteCount;
/** @var $field GDT_VoteCount **/
$gdo = $field->getVoteObject(); ?>
<span class="<?=$field->name;?>-vote-count-<?= $gdo->getID(); ?>">
<?php
$value = t('vote_count', [$gdo->getVoteCount()]);
echo GDT_Badge::make()->addClass('vote-count')->var($value)->renderCell();
?>
</span>
