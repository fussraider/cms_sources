<?php
registry::disableDirectCall();

class auth{
	
	private static $className = "Авторизация";

/* 	public static function showLoginForm(){
		if(self::isAuth())
			header('location: '.config::getPath('root', true));
		else{
			$form = array(
				'content' => '<form method="POST" name="auth_form" class="user_auth_form uk-width-1-1 uk-container-center uk-form" id="login">
								<div class="box">
									<div class="login string uk-margin">
										<span id="label">Логин:</span>
										<span id="input">
											<input type="text" name="user_login" class="user_login uk-width-1" id="edit" />
										</span>
									</div> 
									<div class="password string uk-margin">
										<span id="label">Пароль:</span>
										<span id="input">
											<input type="password" name="user_password" class="user_password uk-width-1" id="edit" />
										</span>
									</div>
									<div class="buttons string uk-margin">
										<span class="login_button">
											<button type="submit" name="user_auth" class="user_login uk-button uk-button-primary uk-width-1-1 uk-width-small-1-1  uk-width-medium-1-3 uk-width-large-1-3 uk-width-xlarge-1-3" id="submit_btn" value="Вход">
												<span class="uk-icon-chevron-right"></span> Вход
											</button>
										</span>
									</div>
								</div>
							</form>',
				'title' => 'Авторизация'
			);

			functions::setTitle('Авторизация');
			functions::toContent(template::loadTemplate(config::$template, '_PAGE', $form, true), 'page uk-container-center uk-panel uk-panel-box crm-auth');
		}
	} */

	public function __construct(){
		if(isset($_POST['user_auth']) && !empty($_POST['user_auth'])){
			if(!empty($_POST['user_login']) && !empty($_POST['user_password'])){
				$user_id = self::checkUserLogin($_POST['user_login'], $_POST['user_password']);
				if($user_id){
					$_SESSION['user_hash'] = functions::generateUserHash($user_id);
					$_SESSION['user_id'] = $user_id;
				}
				else
					handler::userMessage('Неверный логин или пароль', true);
			}
			else{
				handler::userMessage('Заполнены не все поля', true);
			}
		}
		elseif(isset($_POST['user_registration']) && !empty($_POST['user_registration'])){
			if(!empty($_POST['user_login']) && !empty($_POST['user_email']) && !empty($_POST['user_password']) && !empty($_POST['user_password_confirmation'])){

				$user = array(
					'login' => $_POST['user_login'],
					'email' => $_POST['user_email'],
					'password' => $_POST['user_password'],
					'password_confirmation' => $_POST['user_password_confirmation']
				);

				$errors = array();
				if(users::checkUserByName($user['login']))
					$errors[] = 'Указанный логин уже занят';
				if(!preg_match('/^[a-zA-Z0-9]{2,20}$/', $user['login']))
					$errors[] = 'Логин должен состоять только из латинских букв и цифр. От 3 до 20 символов.';
				if(users::checkUserByEmail($user['email']))
					$errors[] = 'Указанный E-mail уже зарегистрирован на сайте';
				if(!preg_match('/^[-\w.]+@([A-z0-9][-A-z0-9]+\.)+[A-z]{2,4}$/', $user['email']))
					$errors[] = 'Невалидный адрес электронной почты';
				if(!preg_match('/(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/', $user['password']))
					$errors[] = 'Пароль должен сожержать только строчные и прописные латинские буквы, цифры, спецсимволы. Минимум 8 символов';
				if(strcmp($user['password'], $user['password_confirmation']) !== 0 )
					$errors[] = 'Поля паролей не совпадают.';

				if($errors){
					$full_error = '';
					foreach($errors as $error)
						$full_error .= '<li>' . $error . '</li>';

					handler::userMessage($full_error, true);
				}
				else{
					users::registerUser($user);
					$_SESSION['registration_complete'] = true;
				}
				
			}
			else
				handler::userMessage('Заполнены не все поля', true);
		}
	}

	public static function checkUserLogin($login, $pass){
		$res = database::prepareQuery("SELECT `id`, `passwd` FROM `userlist` WHERE `name`='s:login' LIMIT 1", 
										array('login' => $login)
									);
		if($res->num_rows){
			$res_data = $res->fetch_assoc();
			if(functions::cryptPassword($pass) == $res_data['passwd'])
				return $res_data['id'];
			else
				return false;
		}
		else
			return false;
	}
	
	public static function sessionDestroy(){
		session_destroy();
		header('location: '.config::getPath('root', true));
	}

	public static function isAuth(){
		//		if($_SESSION['user_id'] == -1)
		if(users::getId() == -1)
			return false;
		else{
			if(users::checkUser())
				return true;
			else
				return false;
		}
	}

	public static function getClassName()
	{		
		return self::$className;	
	}
}
?>