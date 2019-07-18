<?php
	if (!empty($this->session->flashdata('message'))) {
		echo "<p>".$this->session->flashdata('message')."</p>";
	}
?>
<form action="<?php echo base_url('auth/action'); ?>" method="POST">
	<input type="email" name="email" id="email" placeholder="Email">
	<input type="password" name="password" id="password" placeholder="Password">
	<input type="submit" value="Login">
</form>
<a href="<?php echo $url_login; ?>">Login or Signup with Facebook</a>