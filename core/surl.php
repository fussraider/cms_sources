<?php
//	семантические ссылки, или ЧПУ
if (!defined('_PLUGSECURE_'))
{
  die('Прямой вызов модуля запрещен!');
}

class surl{
	
	private static $className = "ЧПУ";

	public static function parseUrl($type, $requset_uri)
	{

		if ($requset_uri != '/')
		{
			$data = array();
			$query_alias = database::prepareQuery("SELECT * FROM `aliases` WHERE `alias` = 's:uri';", array('uri' => $_SERVER['REQUEST_URI']));
			if($query_alias->num_rows)
			{
				$alias = $query_alias->fetch_assoc();
				$requset_uri = $alias['address'];
			}
			if($type == 1)
			{
				$url_path = parse_url($requset_uri, PHP_URL_PATH);
				$uri_parts = explode('/', trim($url_path, ' /'));
				if (count($uri_parts) % 2) 
				{
					if(isset($_GET['module']))
					{
						$data['module'] = $_GET['module'];
						unset($_GET['module']);

						if(isset($_GET['action'])){
							$data['action'] = $_GET['action'];
							unset($_GET['action']);
						}

						foreach ($_GET as $key => $value)
						{
							$data['params'][$key] = $value;
						}
					}
					else
					{
						$uri_parts = explode('&', trim($url_path, ' /'));
						if(modules::getModule($uri_parts[0]))
							$data['module'] = array_shift($uri_parts);
						else
							handler::httpError(404);
					}
				}
				else
				{
					$data['module'] = array_shift($uri_parts);
					$data['action'] = array_shift($uri_parts);

					for ($i=0; $i < count($uri_parts); $i++) 
					{
						$data['params'][$uri_parts[$i]] = $uri_parts[++$i];
					}
				}
				return $data;
			}
			else
			{
				if(isset($_GET['module']))
				{
					$data['module'] = $_GET['module'];
					unset($_GET['module']);

					if(isset($_GET['action'])){
						$data['action'] = $_GET['action'];
						unset($_GET['action']);
					}

					foreach ($_GET as $key => $value)
					{
						$data['params'][$key] = $value;
					}
				}
				else
				{
					handler::httpError(404);
				}
				return $data;
			}
		}
		return;
	}

	public static function genUri($module, $action = null, $params = null){
		if(config::$s_url){
			$result = '/'.$module.'/';
			if($action){
				$result .= $action.'/';

				if(is_array($params)){
					foreach ($params as $key => $value) {
						$result .= $key.'/'.$value.'/';
					}
				}
			}
		}else{
			$result = '/?module='.$module;
			if($action){
				$result .= '&action='.$action;

				if(is_array($params)){
					foreach ($params as $key => $value) {
						$result .= '&'.$key.'='.$value;
					}
				}
			}
		}

		return $result;
	}



	public static function getClassName()
	{		
		return self::$className;	
	}
}
?>