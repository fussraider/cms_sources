<?php
//Регистр
if (!defined('_PLUGSECURE_'))
{
  die('Прямой вызов модуля запрещен!');
}

//стандартизируем стандартные интерфейсы объектов
//если метод в классе отстутствует - объект не будет загружен, выведется сообщение об ошибке
interface StorableObject
{
    	public static function getClassName();
}


class Registry implements StorableObject
{
	//имя модуля, читаемое
	private static $className = 'Реестр';
	
	//экземпляр реестра
	private static $instance;
	
	//массив объектов
	private static $objects = array();
	
	//конструктор реестра, при создании подключаем модули ядра
	public function loadCore()
	{
		$this->config = './core/config.php';
		$this->functions = './core/functions.php';
		$this->handler = './core/handler.php';
		$this->database = './core/database.php';
		$this->users = './core/users.php';
		$this->auth = './core/auth.php';
		$this->modules = './core/modules.php';
		$this->surl = './core/surl.php';
		$this->template = './core/template.php';
		$this->menus = './core/menus.php';
		//$this->plugins = './core/plugins.php';
	}

	//метод синглтон для доступа к объекту
	public static function singleton()
    {
        if( !isset( self::$instance ) )
        {
            $obj = __CLASS__;
            self::$instance = new $obj;
        }
          
        return self::$instance;
    }
	
	
	//избавляемся от ненужной магии
	private function __construct()
	{
		$this->loadCore();
	}
	private function __clone(){}
	private function __wakeup(){}
	private function __sleep() {}
	
	
	//добавляем объект в регистр
	//$object - путь к подключаемому объекту
	//$key - ключ доступа к объекту в регистре
	public function addObject($key, $object)
	{
		if(!isset(self::$objects[$key])){
			require_once $object;
			self::$objects[$key] = new $key();	
		}
		else{
			if(isset(self::$objects['handler']))
				handler::engineError('exception', 'Заблокирована попытка переопределения объекта ' . $key . ': ' . $object);
			else
				die('Заблокирована попытка переопределения объекта ' . $key . ': ' . $object);
		}
	}
	//альтернативный метод через магию
	public function __set($key, $object)
	{
		$this->addObject($key, $object);	
	}

	//получаем объект из регистра
	//$key - ключ в массиве
	public function getObject($key)
	{
		//проверяем есть ли запрошенный объект
		if ( is_object(self::$objects[$key]))
		{
			//если да, то возвращаем этот объект
			return self::$objects[$key];	
		}
	}
	//аналогичный метод через магию
	public function __get($key)
	{
		if ( is_object(self::$objects[$key]))
		{
			return self::$objects[$key];	
		}
	}

	//функция возвращает список подключенных модулей в т.ч. регистра
	public function getObjectsList()
	{
		//массив который будем возвращать
		$names = array();
		//получаем имя каждого объекта из массива объектов
		foreach(self::$objects as $obj) 
		{
        	$names[] = $obj->getClassName();
    	}
		//дописываем в массив имя модуля регистра
		array_push($names, self::getClassName());
		//и возвращаем
		return $names;
	}

	public static function disableDirectCall(){
		if (!defined('_PLUGSECURE_'))
			die('Прямой вызов модуля запрещен!');
	}

	
	//функция вывода имени класса (модуля)
	public static function getClassName()
	{		
		return self::$className;	
	}
}

?>