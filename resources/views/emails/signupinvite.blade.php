<div>Hi,</div>
<div style="margin-top:10px;">
<% $owner %> has added you to his company (<% $company %>) account at <a href="<% url('/') %>"><% url('/') %></a>.
</div>
<div style="margin-top:10px;">
Click the following link in order to signup and become part of <% $company %>'s Rainbow CRM account:
</div>
<div style="margin-top:10px;">
<a href="<% url('/invitesignup?invitation_code='.$invitation_code.'&email='.$email.'&company='.$company) %>">
	<% url('/invitesignup?invitation_code='.$invitation_code.'&email='.$email.'&company='.$company) %>
</a>
</div>
<div style="margin-top:10px;">
	Kind Regards, <br />
	The Rainbow CRM Team<br />
	<a href="<% url() %>"><% url()%></a>
</div>
