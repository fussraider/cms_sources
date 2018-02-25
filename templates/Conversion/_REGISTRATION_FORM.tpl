<form method="POST" name="registration_form" class="uk-form uk-form-horizontal user_registration_form" id="registration">
    <div class="uk-form-row">
        <label class="uk-form-label" for="login_edit">Логин:</label>
        <div class="uk-form-controls">
			<input type="text" name="user_login" class="user_login uk-width-1" value="{:user_login:}" id="login_edit" />
		</div>
	</div>
	<div class="uk-form-row">
        <label class="uk-form-label" for="email_edit">E-mail:</label>
        <div class="uk-form-controls">
			<input type="text" name="user_email" class="user_email uk-width-1" value="{:user_email:}" id="email_edit" />
		</div>
	</div>
	<div class="uk-form-row">
        <label class="uk-form-label" for="password_edit">Пароль:</label>
        <div class="uk-form-controls">
			<input type="password" name="user_password" class="user_password uk-width-1" value="" id="password_edit" />
		</div>
	</div>
	<div class="uk-form-row">
        <label class="uk-form-label" for="password_confirmation_edit">Подтверждение пароля:</label>
        <div class="uk-form-controls">
			<input type="password" name="user_password_confirmation" class="user_password_confirmation uk-width-1" value="" id="password_confirmation_edit" />
		</div>
	</div>
	<div class="uk-form-row">
        <button type="submit" name="user_registration" class="user_registration uk-button uk-button-primary uk-width-1-1 uk-width-small-1-1  uk-width-medium-1-3 uk-width-large-1-4 uk-width-xlarge-1-4" id="submit_btn" value="Регистрация">
			<span class="uk-icon-chevron-right"></span> Регистрация
		</button>
    </div>
</form>