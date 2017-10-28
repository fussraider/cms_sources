<?php
//	скрипт отвечает за вывод постов
if (!defined('_PLUGSECURE_'))
  die('Прямой вызов модуля запрещен!');
//если данных нет выводим все посты

if(empty($url_data['action']))
{
	functions::setTitle("Новости");
	$posts = database::query("SELECT * FROM `posts` ORDER BY `id`;");
	$posts_grid = '';
	while($res = $posts->fetch_assoc())
	{
		if(config::$s_url){
			$try_alias = database::prepareQuery("SELECT `alias` FROM `aliases` WHERE `address`='s:addr' ORDER BY `id` LIMIT 1;", 
												array(
													'addr' => surl::genUri(
																		'posts', 
																		'show', 
																		array('id' => $res['id'])
																	)
													)
												);
			if($try_alias->num_rows)
			{
				$try_alias = $try_alias->fetch_assoc();
				$res['id'] = $try_alias['alias'];
			}
			else
				$res['id'] = surl::genUri('posts', 'show', array('id' => $res['id']));
		}
		else
			$res['id'] = surl::genUri('posts', 'show', array('id' => $res['id']));

		$res['text'] = functions::stripPost($res['text']);
		$posts_grid .= template::loadTemplate(config::$template, '_POSTS', $res, true);
	}
	functions::toContent($posts_grid, 'posts');
	unset($posts_grid, $posts, $res);
}
//если данные есть то выводим один пост по шаблону единичного поста
elseif($url_data['action'] == 'show' && !empty($url_data['params']['id']))
{
	$post = database::prepareQuery("SELECT * FROM `posts` WHERE `id`='s:id' LIMIT 1;", array('id' => $url_data['params']['id']));
	$res = $post->fetch_assoc();
	functions::setTitle($res['title']);
	functions::toContent(template::loadTemplate(config::$template, '_SINGLE_POST_'.$res['class'], $res, true), 'single_post');
}
//во всех остальных случаях генерим 404
else
	handler::httpError(404);

unset($post, $res);
?>