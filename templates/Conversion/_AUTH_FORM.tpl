<form method="POST" name="auth_form" class="user_auth_form uk-width-1-1 uk-container-center uk-form" id="login">
	<div class="box">
		<div class="login string uk-margin">
			<span id="label">Логин:</span>
			<span id="input">
				<input type="text" name="user_login" class="user_login uk-width-1" value="{:user_login:}" id="edit" />
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
</form>