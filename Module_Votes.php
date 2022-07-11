<?php
namespace GDO\Votes;

use GDO\Core\GDO_Module;
use GDO\Core\Application;
use GDO\Core\GDT_UInt;

final class Module_Votes extends GDO_Module
{
	public int $priority = 25;
	
	public function onLoadLanguage() : void { $this->loadLanguage('lang/votes'); }
	
	public function onIncludeScripts() : void
	{
	    if (Application::instance()->hasTheme('material'))
	    {
    		if (module_enabled('Angular'))
    		{
    			$this->addJS('js/gwf-vote-ctrl.js');
    		}
	    }
	    elseif (module_enabled('JQuery'))
		{
			$this->addJS('js/gdo-vote.js');
		}
		$this->addCSS('css/gwf-votes.css');
	}
	
	/**
	 * Store some stats in hidden settings.
	 */
	public function getUserConfig()
	{
		return array(
			GDT_UInt::make('likes')->initial('0'),
		);
	}

}
