<?php
if (!defined('_PLUGSECURE_'))
{
  die('Прямой вызов модуля запрещен!');
}

class functions
{
	//имя класса (читаемое)
	private static $className = 'Функции';

	//функция вывода контента
	public static function toContent($contents, $class = null)
	{
		if($class)
			Config::$global_cms_vars['CONTENT'] .= '<div class="' . $class . '">' . $contents . '</div>';
		else
			Config::$global_cms_vars['CONTENT'] .= $contents;
	}

	public static function setTitle($title)
	{
		Config::$global_cms_vars['PAGE_TITLE'] = $title;
	}
	
	public static function stripPost($text)
	{
		$check_pre_tags = strpos($text, '<pre');
		if($check_pre_tags < config::$strip_posts && (!empty($check_pre_tags) || $check_pre_tags !=0))
			return substr(strip_tags($text), 0, strpos($text, ' ', $check_pre_tags)).'...';
		else	
			return substr(strip_tags($text), 0, strpos($text, ' ', config::$strip_posts)).'...';
	}

	public static function cryptPassword($password){
		return md5(sha1($password).config::$salt);
	}

	public static function generateUserHash($user_id){
		$hash = md5(time().rand().config::$salt.$user_id);
		$sql = database::prepareQuery("UPDATE `userlist` SET `secret`='s:hash' WHERE `id`='i:user_id'",
										array(
											'hash'	=>	$hash,
											'user_id'	=>	$user_id
										)
									);
		if($sql)
			return $hash;
		else{
			handler::engineError('user_hash_error', 'Ошибка записи хеша');
			return false;
		}
	}

	
	//функция вывода имени класса (модуля)
	public static function getClassName()
	{		
		return self::$className;	
	}

}
?>