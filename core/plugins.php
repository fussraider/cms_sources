<?php
//	семантические ссылки, или ЧПУ
if (!defined('_PLUGSECURE_'))
{
  die('Прямой вызов модуля запрещен!');
}

class plugins{
	
	private static $className = "Плагины";
	private static $plugins_list = array();

	public function __construct()
	{
		$plugins = array();
		$query = database::query('SELECT * FROM `plugins` ORDER BY `id` ASC;');
		if($query->num_rows)
		{
			while ($row = $query->fetch_assoc()) {
				$plugins[] = array(
								'id'	=>	$row['id'],
								'name'	=>	$row['name'],
								'type'	=>	$row['type'],
								'is_on'	=>	$row['is_on']
							); 
			}
		}

		self::$plugins_list = $plugins;
		unset($plugins);
		self::initPlugins();
	}

	public static function initPlugins()
	{
		$plugins_count = count(self::$plugins_list);
		if($plugins_count>0)
		{
			foreach (self::$plugins_list as $key => $plugin) {
				$plugin_path = './includes/plugins/'.$plugin['name'].'/index.php';
				if(!file_exists($plugin_path))
				{
					self::$plugins_list[$key]['error'] = 1;
					handler::engineError('plugin_not_found', $plugin_path);
				}
			}
		}
	}

	/*
	 * функция аодключает плагины
	 * $type - before или after - ключ для плагинов которые срабатывают перед 
	 * отрисовкой страницы и после соответвственно 
	 */
	public static function loadPlugins($type)
	{
		if($type != 'before' && $type != 'after')
			handler::engineError('plugin_error', $type.' - не верный ключ. Возможно использование только <b>before</b> и <b>after</b>.');
		else
		{
			foreach (self::$plugins_list as $plugin) 
			{
				$plugin_path = './includes/plugins/'.$plugin['name'].'/index.php';
				if($plugin['is_on'] <> 0 && $plugin['type'] == $type)
				{
					if(!isset($plugin['error']))
						require_once($plugin_path);
					else
						handler::engineError('plugin_bad_try', $plugin_path);
				}
			}
		}
	}

	public static function addScript($plugin_name, $script_name, $place = 'head')
	{
		$script_path = '/includes/plugins/'.$plugin_name.'/'.$script_name;
		if(file_exists('.'.$script_path))
		{
			$dom_place = '</'.$place.'>';
			$added_string = '<script type="text/javascript" src="'.$script_path.'"></script>';
			//str_replace($dom_place, $replace, self::$template);
			config::$global_cms_vars['PAGE'] = str_replace($dom_place, $added_string.$dom_place, config::$global_cms_vars['PAGE']);
		}
		else
			handler::engineError('plugin_part_not_found', '<b>'.$plugin_name.':</b> '.$script_path);
	}

	public static function addStyle($plugin_name, $style_name, $media=null)
	{
		$script_path = '/includes/plugins/'.$plugin_name.'/'.$style_name;
		if(file_exists('.'.$script_path))
		{
			$dom_place = '</head>';
			$added_string = '<link rel="stylesheet" '.$media.' type="text/css" href="'.$script_path.'">';
			config::$global_cms_vars['PAGE'] = str_replace($dom_place, $added_string.$dom_place, config::$global_cms_vars['PAGE']);
		}
		else
			handler::engineError('plugin_part_not_found', '<b>'.$plugin_name.':</b> '.$script_path);
	}

	public static function getClassName()
	{		
		return self::$className;	
	}
}
?>