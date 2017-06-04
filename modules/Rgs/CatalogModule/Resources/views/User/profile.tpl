<h2>My Profile: {$app.user.data.group.name}</h2>
<hr />
    
<p>{"user.login"|trans:[]:UserModule}: {$app.user.data.login}</p>

<p>{"user.email"|trans:[]:UserModule}: {$app.user.data.email}</p>

<p>{"user.address"|trans:[]:UserModule}: {$app.user.data.address}</p>

<p>{"user.code"|trans:[]:UserModule}: {$app.user.data.code_postal}</p>

<p>{"user.city"|trans:[]:UserModule}: {$app.user.data.ville}</p>

<p>{"user.tel"|trans:[]:UserModule}: {$app.user.data.tel}</p>

<p>{"user.mobile"|trans:[]:UserModule}: {$app.user.data.mobile}</p>

<p>{"user.last_login"|trans:[]:UserModule}: {$app.user.data.last_login|date_format:"%Y-%m-%d %H:%M:%S"}</p>

<p>{"user.created_at"|trans:[]:UserModule}: {$app.user.data.created_at|date_format:"%Y-%m-%d %H:%M:%S"}</p>

{bootstrap_modal id="editUser" title="{"profile.edit.submit"|trans:[]:UserModule}" class="fade" btn_class="btn-info" show_button=true}
	<form data-toggle="validator" lang={#lang#} method="post" >

		{form_build_widget form=$form}

		{* begin submit *}
		<div class="row form-group text-center col-xs-offset-0">
			<input type="submit" id="_submit" name="_submit" class="btn btn-success" value="{'profile.edit.submit'|trans:[]:UserModule}" />
		</div>
		{* end submit *}
	</form>
{/bootstrap_modal}