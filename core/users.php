<?php

registry::disableDirectCall();

class users{
	
	private static $className = "Пользователи";

	private static $user_id;

	public function __construct(){
		$check_user = self::checkUser();
		if($check_user)
			self::$user_id = $check_user;
		else
			self::$user_id = -1;
	}

    public static function checkUser(){
        if($_SESSION['user_id'] && $_SESSION['user_hash']){
            $check_data = database::prepareQuery("SELECT `id` FROM `userlist` WHERE `id`='i:user_id' AND `secret`='s:user_hash'",
                                                    array(
                                                        'user_id'=>$_SESSION['user_id'],
                                                        'user_hash'=>$_SESSION['user_hash']
                                                    )
                                                );
            if($check_data->num_rows){
                $result = $check_data->fetch_assoc();
                return $result['id'];
            }
            else 
                return false;
        }
        else
            return false;
    }

    public static function checkUserByName($name){
        $result = database::prepareQuery(
                                        "SELECT COUNT(`name`) as `count` FROM `userlist` WHERE `name`='s:name';",
                                        array('name'=>$name)
                                    );
        $result = $result->fetch_assoc();
        if($result['count'] > 0)
            return true;
        else
            return false;
	}
	
	public static function checkUserByEmail($email){
		$result = database::prepareQuery(
			"SELECT COUNT(`email`) as `count` FROM `userlist` WHERE `email`='s:email';",
			array('email'=>$email)
		);
		$result = $result->fetch_assoc();
		if($result['count'] > 0)
			return true;
		else
			return false;
	}

    private static function getUserIdByName($name){
        $result = database::prepareQuery(
                                            "SELECT `id` FROM `userlist` WHERE `name`='s:name';",
                                            array('name'=>$name)
                                        );
        if($result->num_rows){
            $result = $result->fetch_assoc();
            return $result['id'];
        }
        else
            return false;
    }

	public static function getUserNameById($id){
		$result = database::prepareQuery(
											"SELECT `name` FROM `userlist` WHERE `id`='i:id';",
											array('id'=>$id)
										);
		if($result->num_rows){
			$result = $result->fetch_assoc();
			return $result['name'];
		}
		else
			return false;
	}

	public static function registerUser($data){
		if(!empty($data['login']) && !empty($data['password'] && !empty($data['email'] && strcmp($user['password'], $user['password_confirmation']) === 0))){
			unset($data['password_confirmation']);
			$sql = "INSERT INTO `userlist` SET `name`='s:login', `passwd`='s:password', `email`='s:email', `reg_date`=NOW()";

			$data['password'] = functions::cryptPassword($data['password']);
			$result = database::prepareQuery($sql, $data);

			return $result;
		}
		else
			handler::userMessage('Получен невалидный набор данных');
	}

/* 	private static function getUserRights(){
		$access_level = self::getUserAccessLevel();
		if($access_level){
			$query = database::query("SELECT `action_name` FROM `rights_actions` WHERE `access_level`<=".$access_level);
			if($query->num_rows)
				{
					$rights = array();
					foreach ($query as $value) {
						$rights[] = $value['action_name'];
					}
					return $rights;
				}
			else
				return false;
		}
		else
			return false;
	} */

/* 	private static function getUserGroup(){
		$user_group = self::getUserData('group_id');
		if($user_group)
			return $user_group;
		else
			return false;
	} */

/* 	private static function getUserAccessLevel(){
		$user_group = self::getUserGroup();

		if($user_group){
			$query= database::query("SELECT `access_level` FROM `rights_groups` WHERE `id`='".$user_group."' LIMIT 1");
			if($query->num_rows){
				$user_access_level = $query->fetch_assoc();
				return $user_access_level['access_level'];
			}
			else
				return false;
		}
		else
			return false;
	} */

    private static function isThisUser($name){
        if(self::$user_id == self::getUserIdByName($name))
            return true;
        else
            return false;
    }

    public static function getUserData($cols = null){
            
            if($cols){
                if(is_array($cols))
                    $cols_str = implode(',', $cols);
                else
                    $cols_str = $cols;
            }
            else
                $cols_str = '*';
                
            $user_data = database::query("SELECT ".$cols_str." FROM `userlist` WHERE `id`='".(int)self::$user_id."' LIMIT 1");
            if($user_data->num_rows){
                if(strpos($cols_str, ',') === false){
                    $result = $user_data->fetch_assoc();
                    return $result[$cols_str];
                }
                else
                    return $user_data->fetch_assoc();
            }
            else
                return false;
    }

/* 	public static function checkUserLogin($login, $pass){
		$res = database::prepareQuery("SELECT `id`, `passwd` FROM `userlist` WHERE `name`='s:login' LIMIT 1", 
										array('login' => $login)
									);
		if($res->num_rows){
			$res_data = $res->fetch_assoc();
			if(functions::cryptPassword($pass) == $res_data['passwd'])
			{
				self::$user_id = $res_data['id'];
				return $res_data['id'];
			}
			else
				return false;
		}
		else
			return false;
	} */

/* 	public static function getFullUserData($user_id = null){
		if(!self::checkUserAccess('read_user')){
			return handler::userMessage('Недостаточно прав для выполнения этого действия', true);
		}

		$sql = "SELECT ul.name, ul.email, ul.group_id, ul.avatar, ul.reg_date, ul.last_login,
						rg.name as role, rg.access_level,
						GROUP_CONCAT(ra.action_name SEPARATOR ',') as actions
				FROM `userlist` ul 
				JOIN `rights_groups` rg ON rg.id=ul.group_id 
				JOIN `rights_actions` ra ON ra.access_level<=rg.access_level
				WHERE ul.id=i:id;";
		$result = database::prepareQuery($sql, array('id'=>($user_id ? $user_id: self::$user_id)));
		if($result->num_rows){
			return $result->fetch_assoc();
		}
		else{
			return false;
		}
	}


	public static function checkUserAccess($rights){
		$user_rights = self::getUserRights();
		if(is_array($user_rights)){
			if(!is_array($rights))
				$rights = explode(',', $rights);

			foreach ($rights as $value) {
				if(!in_array(trim($value), $user_rights))
					return false;
			}
		}else	
			return false;

		return true;
	}

	public static function showUserInfo($user_name){
		if(!self::checkUserAccess('read_user')){
			return handler::userMessage('Недостаточно прав для выполнения этого действия', true);
		}

		$is_this_user = self::isThisUser($user_name);
		$full_user_data = self::getFullUserData($is_this_user ? self::$user_id : self::getUserIdByName($user_name));

		$change_pass = '<span id="change_pass">
							<a href="'.surl::genUri('user',$full_user_data['name'], array('edit'=>'password')).'">
								Сменить пароль
							</a>
						</span>';
		$change_email = '<span id="change_pass">
							<a href="'.surl::genUri('user',$full_user_data['name'], array('edit'=>'email')).'">
								Сменить
							</a>
						</span>';
		$user_avatar = '<img src="/media/images/avatars/'.($full_user_data['avatar'] ? $full_user_data['avatar'] : 'no-avatar.png').'" width="128px" />';

		$form = array(
				'content' => '<div class="user_profile_box">
									<div class="user_avatar">
										'.$user_avatar.'
									</div>
									<div class="user_login">
										<span id="label">Логин:</span>
										<span id="value">
											'.$full_user_data['name'].'
										</span>
										'.($is_this_user ? $change_pass : null).'
									</div> 
									<div class="user_email">
										<span id="label">E-mail:</span>
										<span id="value">
											'.$full_user_data['email'].'
										</span>
										'.($is_this_user ? $change_email : null).'
									</div>
									<div class="user_reg_date">
										<span id="label">Дата регистрации:</span>
										<span id="value">
											'.$full_user_data['reg_date'].'
										</span>
									</div>
									<div class="user_last_login_date">
										<span id="label">Дата последнего входа:</span>
										<span id="value">
											'.$full_user_data['last_login'].'
										</span>
									</div>

									<div class="user_group">
										<span id="label">Группа пользователей:</span>
										<span id="value">
											'.$full_user_data['role'].'
										</span>
									</div>


									<div class="user_access">
										<span id="label">Права:</span>
										<span id="value">
											'.str_replace(',',', ', $full_user_data['actions']).'
										</span>
									</div>
									'.(
										$is_this_user ? 
														'<div class="user_logout">
															<span id="button">
																<a href="'.surl::genUri('user', 'logout').'" class="user_logout_btn">Выход</a>
															</span>
														</div>'
										: null 
									).'
								</div>',
				'title' => 'Пользователь '.$full_user_data['name']
			);

			functions::setTitle('Профиль пользователя '.$full_user_data['name']);
			functions::toContent(template::loadTemplate(config::$template, '_PAGE', $form, true), 'page');
	}

	public static function getUserMenu(){
		if(auth::isAuth()){
			if(crypt::checkPublicKey())
				$acces_button = 'uk-button-success uk-icon-unlock-alt';	
			else
				$acces_button = 'uk-button-danger uk-icon-lock';

			return '<ul id="usermenu" class="uk-navbar-nav">
						<li class="uk-parent" data-uk-dropdown="" aria-haspopup="true" aria-expanded="false">
							<a href="#"><span class="uk-icon-user uk-icon-medium"></span></a>

							<div class="uk-dropdown uk-dropdown-navbar uk-dropdown-bottom" aria-hidden="true" tabindex="" style="top: 40px; left: 0px;">
								<ul class="uk-nav uk-nav-navbar">
									<li class="uk-nav-header uk-vertical-align-middle">
										'.config::$global_cms_vars['USER'].' 
									</li>
									<li><a href="/user/{:USER:}/"><i class="uk-icon uk-icon-cog"></i> Профиль</a></li>
									<!--li><a href="/personalisation/">Персонализация</a></li-->
									<li class="uk-nav-divider"></li>
									<li><a href="/user/logout/"><i class="uk-icon uk-icon-sign-out"></i> Выход</a></li>
								</ul>
							</div>

						</li>
					</ul>';
		}
		else
			return null;
	} */


	public static function getId(){
		return self::$user_id;
	}

/* 	public static function getGroup(){
		return self::getUserGroup();
	}

	public static function getRights(){
		return self::getUserRights();
	}

	public static function getAccessLevel(){
		return self::getUserAccessLevel();
	} */
	
	public static function getClassName()
	{		
		return self::$className;	
	}
}
?>