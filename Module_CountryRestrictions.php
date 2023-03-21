<?php
namespace GDO\CountryRestrictions;

use GDO\Core\GDO_Module;
use GDO\Core\GDO_RedirectError;
use GDO\Country\GDT_Country;
use GDO\User\GDO_User;

/**
 * Restrict access for certain contries via black and/or whitelist.
 *
 * @version 7.0.1
 * @author gizmore
 */
final class Module_CountryRestrictions extends GDO_Module
{

	public int $priority = 20;

	public function getConfig(): array
	{
		return [
			GDT_Country::make('country_blacklist')->multiple(),
			GDT_Country::make('country_whitelist')->multiple(),
		];
	}

	public function onModuleInit(): void
	{
		$this->enforceRestrictions(GDO_User::current());
	}

	private function enforceRestrictions(GDO_User $user): void
	{
		if ((!$user->isAdmin()) && (!$this->isAlwaysAllowed()))
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

	private function isAlwaysAllowed(): bool
	{
		global $me;
		return $me->isAlwaysAllowed();
	}

	public function cfgWhitelist(): array { return $this->getConfigValue('country_whitelist'); }

	private function restricted(): void
	{
		$href = $this->href('Restricted');
		throw new GDO_RedirectError('err_country_restriction', null, $href);
	}

	public function cfgBlacklist(): array { return $this->getConfigValue('country_blacklist'); }

	public function onLoadLanguage(): void
	{
		$this->loadLanguage('lang/country_restrictions');
	}

}
