<?php
namespace GDO\CountryRestrictions;

use GDO\Core\GDO_Module;
use GDO\User\GDO_User;
use GDO\Country\GDT_Country;
use GDO\Core\GDO_RedirectError;

/**
 * Restrict access for certain contries via black and/or whitelist.
 * 
 * @author gizmore
 * @version 7.0.1
 */
final class Module_CountryRestrictions extends GDO_Module
{
	public int $priority = 20;
	
	public function getConfig() : array
	{
		return [
			GDT_Country::make('country_blacklist')->multiple(),
			GDT_Country::make('country_whitelist')->multiple(),
		];
	}
	
	public function cfgBlacklist() : array { return $this->getConfigValue('country_blacklist'); }
	public function cfgWhitelist() : array { return $this->getConfigValue('country_whitelist'); }
	
	public function onInit() : void
	{
		$this->enforceRestrictions(GDO_User::current());
	}
	
	private function enforceRestrictions(GDO_User $user) : void
	{
		if (!$this->isAlwaysAllowed())
		{
			if ($iso = $user->getCountryISO())
			{
				if ($whitelist = $this->cfgWhitelist())
				{
					if (!isset($whitelist[$iso]))
					{
						$this->restricted();
					}
				}
				if ($blacklist = $this->cfgBlacklist())
				{
					if (isset($blacklist[$iso]))
					{
						$this->restricted();
					}
				}
			}
		}
	}

	private function isAlwaysAllowed() : bool
	{
		global $me;
		return $me->isAlwaysAllowed();
	}
	
	private function restricted() : void
	{
		$href = $this->href('Restricted');
		throw new GDO_RedirectError('err_country_restriction', null, $href);
	}
	
}
