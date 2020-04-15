<?php
	require_once 'core/init.php';
	
	if (Session::exists('success'))
	{
		echo Session::flash('success');
	}

	$user = new User();
	if($user->isLoggedIn())
	{
		if (Session::exists('login'))
		{
			echo Session::flash('login');
		}elseif (Session::exists('welcome'))
		{
			echo Session::flash('login');
		}
			?>
			<ul>
				<li><a href="profile.php">Profile</a></li>
				<li><a href="update.php">Change Password</a></li>
				<li><a href="logout.php">logout.php</a></li>
			</ul>
			<?php
	}else
	{
		echo "You have to <a href = 'login.php'>Login</a> or <a href = 'register.php'>Register</a>";
	}
	 	