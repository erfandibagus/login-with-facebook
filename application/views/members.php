<table cellpadding="5" border="1">
	<thead>
		<tr>
			<th>FB ID</th>
			<th>NAME</th>
			<th>EMAIL</th>
			<th>TOKEN</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><?php echo $this->session->userdata('fbid'); ?></td>
			<td><?php echo $this->session->userdata('name'); ?></td>
			<td><?php echo $this->session->userdata('email'); ?></td>
			<td>*** <?php echo substr($this->session->userdata('token'), 25, 50); ?> ***</td>
		</tr>
	</tbody>
</table>
<p><a href="<?php echo base_url('auth/logout'); ?>">Logout</a></p>