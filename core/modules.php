<?php
if (!defined('_PLUGSECURE_'))
{
  die('Прямой вызов модуля запрещен!');
}

class modules{
	
	private static $className = "Модули";
	private static $core_modules = array('posts', 'error');

	public static function getModule($module)
	{
			if(file_exists('./includes/modules/custom/'.$module.'/'.$module.'.inc'))
				return './includes/modules/custom/'.$module.'/'.$module.'.inc';
			elseif(file_exists('./includes/modules/stock/'.$module.'/'.$module.'.inc'))
				return './includes/modules/stock/'.$module.'/'.$module.'.inc';
			else
			{
				return false;
			}
	}

	public static function isCore($module)
	{
		return in_array(strtolower($module), self::$core_modules);
	}

	public static function getClassName()
	{		
		return self::$className;	
	}
}
?>