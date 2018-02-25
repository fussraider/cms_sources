<?php
registry::disableDirectCall();

if($url_data['action'] == 'login'){
	if(auth::isAuth())
			header('location: '.config::getPath('root', true));
	else{
		$form_data['user_login'] = isset($_POST['user_login']) ? trim($_POST['user_login']) : null;

		$form = array(
			'content' => template::loadTemplate(config::$template, '_AUTH_FORM', $form_data, true),
			'title' => 'Авторизация'
		);

		functions::setTitle('Авторизация');
		functions::toContent(template::loadTemplate(config::$template, '_PAGE', $form, true), 'page uk-container-center uk-panel uk-panel-box crm-auth');
	}
}
else{
	if(empty($url_data['action']))
		header('Location: '.surl::genUri('user', 'login'));
	elseif($url_data['action'] == 'logout'){
		auth::sessionDestroy();
	}
	else
		handler::httpError(404);
}

?>