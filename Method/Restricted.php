<?php
namespace GDO\CountryRestrictions\Method;

use GDO\UI\MethodPage;

final class Restricted extends MethodPage
{
	public function isAlwaysAllowed() : bool { return true; }
	
	protected function getTemplateName() : string
	{
		return 'country_restriction_page.php';
	}
	
	public function getMethodTitle() : string
	{
		return t('restricted');
	}
	
	public function getMethodDescription() : string
	{
		return t('err_country_restriction');
	}
	
}
