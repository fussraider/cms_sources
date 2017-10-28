<?php
//	файл конфигурации
if (!defined('_PLUGSECURE_'))
{
  die('Прямой вызов модуля запрещен!');
}

class database 
{
	private static $className = 'Класс MySQL';
	private static $connection;

	//функция принимает на фход массив $data
	//содержащий данные для установки соединения (логин, пароль, порт и т.д.)
	
	public static function query($sql=null)
	{
		if($sql)
		{
			$try_query = self::$connection->query($sql);
			if($try_query)
			{
				return $try_query;
			}
			else
			{
				handler::engineError('db_query', self::$connection->error);
				return;
			}
		}
		else
		{
			handler::engineError('db_empty_query', 'Функция получила на вход пустой запрос.');
			return;
		}
	}

	public static function prepareQuery($prepare=null, $values=null)
	{
		if($prepare)
		{
			if($values)
			{
				foreach ($values as $key => $value) {
					$value_type = substr($prepare, strpos($prepare, ':'.$key)-1, 1);
					$value = self::$connection->real_escape_string($value);
					switch ($value_type) {
						case 'i': $typed_value = (int)$value; break;
						case 's': $typed_value = (string)$value; break;
						case 'd': $typed_value = (double)$value; break;
						case 'f': $typed_value = (float)$value; break;
						case 'r': $typed_value = (real)$value; break;
						case 'b': $typed_value = (bool)$value; break;
						default:
							if(!empty($value_type) || $value_type == '"' || $value_type == '\'')
								handler::engineError('db_empty_type', $prepare);
							else
								handler::engineError('db_unknown_type', '<i>'.$prepare.'</i> идентификатор, вызвавший ошибку: <b><i>'.$value_type.'</i></b>'); 
						break;
					}

					$prepare = str_replace(
									$value_type.':'.$key, 
									$typed_value, 
									$prepare
								);
				}
				return self::query($prepare);
			}
			else
			{
				handler::engineError('db_empty_values', $prepare);
				return;
			}
		}
		else
		{
			handler::engineError('db_empty_prepare', 'Функция получила на вход пустой шаблон запроса.');
			return;
		}
	}

	private function __clone(){}
	private function __wakeup(){}
	private function __sleep(){}

	public function __construct()
	{
		$data = config::$database;
		$connect = @new mysqli($data['host'], $data['user'], $data['pass'], $data['base'], $data['port']);
		//проверяем на ошибки
		if($connect->connect_errno || !$connect)
		{
			//если возникла ошибка - выбрасываем исключение
			handler::engineError('db_connect', $connect->connect_error);
		}
		else
		{
			//если все ок - добавляем подключение в глобальную переменну, предварительно установив кодировку работы с БД
			$connect->set_charset($data['charset']);
			self::$connection = $connect;
			//удаляем временную переменную
			unset($connect);
			//возвращаем наше открытое подключение
			return self::$connection;
		}
	}


	public static function getClassName()
	{		
		return self::$className;	
	}

}

?>