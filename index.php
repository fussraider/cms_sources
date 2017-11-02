<?php
session_start();
define( "_PLUGSECURE_", true); //определили константу для защиты от прямого доступа к объектам
require_once './core/registry.php';	//подключили регистр
$registry = Registry::singleton();	//создали экземпляр-синглтон регистра
//обрабатываем запрошенный адрес

$url_data = surl::parseUrl(config::$s_url,  $_SERVER['REQUEST_URI']);

if(empty($url_data))
{
	$request_module = 'posts';
	$request_template = 'index';
}
else
{
	$request_module = $url_data['module'];
	$request_template = 'page';
}

//пробуем загрузить модуль
$try_module = modules::getModule($request_module);
//если модль нашелся - подключаем его
if($try_module)
	require_once $try_module;
//если нет, проверяем не является ли запрошенный модуль обязательным
else
{
	handler::engineError('module_not_found', './includes/modules/stock/'.$request_module.'/'.$request_module.'.inc');
}

handler::engineError('exception', 'Тестовое исключение для проверки вывода ошибки', __FILE__, __LINE__);
handler::userMessage('Обычное информационное сообщение');
handler::userMessage('Сообщение об ошибке или предупреждение', true);
config::$global_cms_vars['PAGE'] = template::loadTemplate(config::$template, $request_template, config::$global_cms_vars);

//показываем страницу
echo config::$global_cms_vars['PAGE'];

?>