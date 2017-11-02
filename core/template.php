<?php
//	семантические ссылки, или ЧПУ
if (!defined('_PLUGSECURE_'))
{
  die('Прямой вызов модуля запрещен!');
}

class template 
{
	//имя класса (читаемое)
	private static $className = 'Шаблоны';

	private static $template; //финальный код шаблона
	private static $template_dir;	//путь к шаблону
	private static $template_page;	//страница (index или error)
	private static $template_parts = array();
	private static $template_vars = array();

public static function loadTemplate($template_name, $page, $vars, $fast = false)
{
	if(!empty($template_name))
	{
		if($fast)
		{
			if(!file_exists(config::getPath('templates') . '/' . $template_name . '/' . $page . '.tpl'))
			{
				handler::engineError('template_not_found', config::getPath('templates') . '/' . $template_name . '/' . $page . '.tpl', __FILE__, __LINE__);
			}
			else
			{
				self::$template_dir = config::getPath('templates') . '/' . $template_name . '/';
				self::$template_page = $page;
				self::$template_vars = $vars;
				self::getTemplate($fast);
				self::parseTemplate();
				return self::$template;
			}
		}
		else
		{
			if(!file_exists(config::getPath('templates') . '/' . $template_name . '/' . $template_name . '.php'))
			{
				handler::engineError('template_not_found', $_SERVER['DOCUMENT_ROOT'].'/'.$template_name['dir'] . '/' . $template_name . '/' . $template_name . '.tpl', __FILE__, __LINE__);
				return false;
			}
			else
			{
				self::$template_dir = config::getPath('templates') . '/' . $template_name . '/';
				require_once self::$template_dir . $template_name . '.php';
				self::$template_page = $page;
				self::$template_parts = $t_files;
				self::$template_vars = $vars;
				self::getTemplate($fast);
				self::loadStyles($t_styles);   //загрузили стили
				self::loadScripts($t_scripts); //загрузили скрипты
				self::parseTemplate();
				return self::$template;
			}
		}
	}
	else
	{
		handler::engineError('template_not_configure', null, __FILE__, __LINE__);
		return false;
	}
}


   private static function getTemplate($fast = false)
   {
   		if($fast)
   			$template_file = self::$template_dir . self::$template_page . '.tpl';
   		else
   			$template_file = self::$template_dir . self::$template_page . '_template.tpl';
   		if(!file_exists($template_file))
   		{
   			handler::engineError('template_not_found', $template_file);
			return;
   		}
   		else
   		{
			self::$template = file_get_contents($template_file);
   		}
   }
   

   private static function parseTemplate()
   {
   		foreach(self::$template_parts as $replace) 
		{
			//если в шаблоне встречается определенный тег части шаблона {*TAG*}
			if(substr_count(self::$template, '{*' . $replace . '*}')>0)
			{
				//обрабатываем тег {*TAG:*}
                self::$template = str_replace('{*' . $replace . '*}', self::getTemplatePart($replace), self::$template);
			}
        }
        
        //теперь парсим на переменные самой системы {:TAG:}
		foreach(self::$template_vars as $find => $replace) 
		{
				//теперь обрабатываем теги {:TAG:}
                self::$template = str_replace('{:' . $find . ':}', $replace, self::$template);
        }

        // и выводим на экран результат
        return self::$template;
   }

   private static function getTemplatePart($part)
	{
		$template_part = '';
		$part_file = self::$template_dir . '__' . $part . '.tpl';
		if(!file_exists($part_file)) 
		{
			handler::engineError('template_part_not_found', $part_file);
			return;
		} 
		else 
		{
			$template_part = file_get_contents($part_file);
		}
		return $template_part;
	}

private static function loadStyles($styles){
	//формируем массив с базовыми стилями системы (подключаем uikit и компонент всплывающих подсказок)
	$system_styles = array(
		'/admin/css/uikit.almost-flat.min.css'						=> 'all',
		'/admin/css/components/tooltip.almost-flat.min.css'			=> 'all',
	);

	if(count($styles) > 0){
		foreach($styles as $style => $media)
			$system_styles['/'.config::$template.$style] = $media;
	}

	self::$template_vars['STYLES'] = null;
	foreach($system_styles as $style => $media)
		self::$template_vars['STYLES'] .= '<link type="text/css" rel="stylesheet" '.(!empty($media) ? 'media="'.trim($media).'"' : null).' href="'.config::getPath('templates', true).$style.'">';
}

private static function loadScripts($scripts){
	//формируем массив с базовыми скриптами системы (подключаем jQuery, Uikit и компонент всплывающих подсказок)
	$system_scripts = array(
		'/admin/js/jquery.min.js'				=> false,
		'/admin/js/uikit.min.js'				=> false,
		'/admin/js/components/tooltip.min.js'	=> false
	); 

	if(count($scripts) > 0){
		foreach($scripts as $script => $in_footer)
			$system_scripts['/'.config::$template.$script] = $in_footer;
	}

	self::$template_vars['HEADER_SCRIPTS'] = null;
	self::$template_vars['FOOTER_SCRIPTS'] = null;

	foreach($system_scripts as $script => $in_footer){
		$include_string = '<script type="text/javascript" src="'.config::getPath('templates', true).$script.'"></script>';
		if($in_footer)
			self::$template_vars['FOOTER_SCRIPTS'] .= $include_string;
		else
			self::$template_vars['HEADER_SCRIPTS'] .= $include_string;
	}
}

	
	//функция вывода имени класса (модуля)
	public static function getClassName()
	{		
		return self::$className;	
	}

}
?>