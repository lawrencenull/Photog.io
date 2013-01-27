<?PHP echo form_open(); ?>

<label for="email">Email</label><input type="email" name="email" value="" id="email" autofocus />

<label for="username">Username</label>
<input type="text" name="username" value="" id="username" />

<label for="password1">Password</label>
<input type="password" name="password1" value="" id="password1" />

<label for="password2">Password (again)</label>
<input type="password" name="password2" value="" id="password2" />

<button class="btn btn-primary" type="submit" name="submit">Register</button>

<?PHP echo form_close(); ?>