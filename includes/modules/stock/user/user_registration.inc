<?php
registry::disableDirectCall();

if(auth::isAuth() || isset($_SESSION['registration_complete']))
    header('Location: '.surl::genUri('user', 'login'));
else{
    $form_data['user_login'] = isset($_POST['user_login']) ? trim($_POST['user_login']) : null;
    $form_data['user_email'] = isset($_POST['user_email']) ? trim($_POST['user_email']) : null;

    $form = array(
        'content' => template::loadTemplate(config::$template, '_REGISTRATION_FORM', $form_data, true),
        'title' => 'Регистрация'
    );

    functions::setTitle('Регистрация');
    functions::toContent(template::loadTemplate(config::$template, '_PAGE', $form, true), 'page uk-container-center uk-panel uk-panel-box crm-registration');
	
}