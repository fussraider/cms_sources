<?php
//	файл конфигурации
if (!defined('_PLUGSECURE_'))
{
  die('Прямой вызов модуля запрещен!');
}

class Config 
{
	//имя модуля, читаемое
	private static $className = 'Конфиг';

	public static $site_name = 'New CMS';
	
	//настройки для БД
	public static $database = array(
				'host'		=>	'localhost',	//хост бд
				'port'		=>	'3306',			//порт бд
				'user'		=>	'root',			//логин к бд
				'pass'		=>	'',				//пароль к бд
				'base'		=>	'fssrdr',		//имя бд
				'charset'	=>	'utf8'			//кодировка
	);

	public static $salt = 'VerySecretString';	//соль, используется в шифровании паролей (помимо прочего)

	public static $strip_posts = 200; 

	//настройки семантических УРЛ
	public static $s_url = 1;

	//настройки шаблонов
	public static $template = 'Conversion';

	public static $dirs = array();

	public static function setPath($name, $value){
		self::$dirs[$name] = $value;
	}

	public static function getPath($name, $is_url = false){
		if(isset(self::$dirs[$name])){
			return ($is_url ? self::$dirs[$name] : $_SERVER['DOCUMENT_ROOT'].self::$dirs[$name]);
		}
		else
			return false;
	}

	public static $global_cms_vars	= array();
	

	public static function getClassName()
	{		
		return self::$className;	
	}

	public function __construct()
	{
		self::$dirs = array(
			'root' 		=> '/',
			'core' 		=> '/core',
			'libraries'	=> '/core/libraries',
			'includes'	=> '/includes',
			'modules'	=> '/includes/modules',
			'plugins'	=> '/includes/plugins',
			'media'		=> '/media',
			'images'	=> '/media/images',
			'templates' => '/templates'
		);
		self::$global_cms_vars['SITE_NAME'] = self::$site_name;
		self::$global_cms_vars['PAGE_TITLE'] = '';
		self::$global_cms_vars['USER_MESSAGE'] = '';
		self::$global_cms_vars['CONTENT'] = '';
		self::$global_cms_vars['YEAR'] = date('Y');
		self::$global_cms_vars['TIME'] = date('H:i:s');
	}

}

?>