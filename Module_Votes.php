<?php
namespace GDO\Votes;

use GDO\Core\GDO_Module;
use GDO\Core\GDT_UInt;

/**
 * Voting and like module.
 * 
 * @link https://www.evanmiller.org/how-not-to-sort-by-average-rating.html
 * 
 * @author gizmore
 * @version 7.0.1
 */
final class Module_Votes extends GDO_Module
{
	public int $priority = 25;
	
	public function onLoadLanguage() : void { $this->loadLanguage('lang/votes'); }
	
	public function onIncludeScripts() : void
	{
		$this->addCSS('css/gwf-votes.css');
	    if (module_enabled('JQuery'))
		{
			$this->addJS('js/gdo-vote.js');
		}
	}
	
	/**
	 * Store some stats in hidden settings.
	 */
	public function getUserConfig()
	{
		return [
			GDT_UInt::make('likes')->initial('0'),
		];
	}

}
