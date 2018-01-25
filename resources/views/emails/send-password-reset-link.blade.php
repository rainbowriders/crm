<div>Hi,</div>
<div style="margin-top:10px;">
In order to reset your password, please click the following link:
</div>
<div style="margin-top:10px;">
<a href="<% url('/reset-password?token='.$user->password_reset_token) %>">
	<% url('/reset-password?token='.$user->password_reset_token) %>
</a>
</div>
<div style="margin-top:10px;">
	Kind Regards, <br />
	The Rainbow CRM Team<br />
	<a href="<% url() %>"><% url()%></a>
</div>