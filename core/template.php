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

	public static function loadTemplate($data, $page, $vars, $fast = false)
	{
		if(!empty($data))
		{
			if($fast)
			{
				if(!file_exists('./' . $data['dir'] . '/' . $data['name'] . '/' . $page . '.tpl'))
				{
					handler::engineError('template_not_found', $data['dir'] . '/' . $data['name'] . '/' . $page . '.tpl');
				}
				else
				{
					self::$template_dir = './' . $data['dir'] . '/' . $data['name'] . '/';
					self::$template_page = $page;
					self::$template_vars = $vars;
					self::getTemplate($fast);
					self::parseTemplate();
					return self::$template;
				}
			}
			else
			{
				if(!file_exists('./' . $data['dir'] . '/' . $data['name'] . '/' . $data['name'] . '.php'))
				{
					handler::engineError('template_not_found', $data['dir'] . '/' . $data['name'] . '/' . $data['name'] . '.tpl');
				}
				else
				{
					self::$template_dir = './' . $data['dir'] . '/' . $data['name'] . '/';
					require_once self::$template_dir . $data['name'] . '.php';
					self::$template_page = $page;
					self::$template_parts = $t_files;
					self::$template_vars = $vars;
					self::getTemplate($fast);
					self::parseTemplate();
					return self::$template;
				}
			}
		}
		else
		{
			handler::engineError('template_not_configure');
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

	
	//функция вывода имени класса (модуля)
	public static function getClassName()
	{		
		return self::$className;	
	}

}
?>