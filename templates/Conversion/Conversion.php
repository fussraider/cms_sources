<?php
if (!defined('_PLUGSECURE_'))
{
  die('Прямой вызов запрещен!');
}

$t_info = array(
		'name' 		=> 'Conversion',	//имя шаблона
		'autor'		=> 'fussraider',	//автор
		'version'	=> '0.1',			//версия
		'date'		=> '09.11.2016'		//дата создания
);

//список псевдопеременных, по которым работает шаблон
$t_files = array('HEADER','INTERESTING', 'QUOTES', 'CONTENT', 'MENU', 'FOOTER');
?>