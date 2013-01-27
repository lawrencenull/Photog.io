<?PHP echo form_open('login'); ?>

<label for="identity">Email</label><input type="text" name="identity" value="" id="identity" autofocus />
<label for="password">Password</label><input type="password" name="password" value="" id="password" />
<button class="btn btn-primary btn-large" type="submit" name="submit">Login</button>

<?PHP echo form_close(); ?>