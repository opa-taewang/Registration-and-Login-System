<?php
	require_once 'core/init.php';	
	if(Input::exists())
	{
		if(Token::check(Input::get('token')))
		{
			$validate = new Validate();
			$validation = $validate->check($_POST, $validate->_items);

			if($validate->passed())
			{
				$register = new User();
				$salt = Hash::salt(16);
				$password = Hash::make(Input::get('password'), $salt);
				try
				{
					$register->create(array(
						'name' => Input::get('name'),
						'username' => Input::get('username'),
						'email' => Input::get('email'),
						'password' => $password,
						'salt' => $salt
					));
				} catch (Exception $e)
				{
					die($e->getMessage());	
				}
				Session::flash('success', 'You have registered successfully');
				Redirect::to('login.php');
			}else
			{
				foreach ($validate->errors() as $key)
				{
					echo $key . "<br>";
				}
			}
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Register</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<!-- Bootstrap CSS -->
		<?php require_once 'bootstrap.php'; ?>
		<link rel="stylesheet" type="text/css" href="style.css">
	</head>
	<body>
		<div class="container col-md-6 py-5" id="reg">
			<h2 class="text-dark font-weight-bold text-center pt-5 mt-5">REGISTER</h2>
			<!-- Display Error -->
			
			<!-- Form start -->
			<form method = "POST" action = "register.php">
				<!-- For full name -->
				<div class="form-group">
					<label class="form-control-label">Full Name</label>
					<input class="form-control border-dark" type="text" name="name" placeholder="Full name" value="<?php echo escape(Input::get('name')) ?>" autocomplet="off" />
				</div>

				<!-- For password and confirm password -->
				<div class="form-row">
					<div class="col-lg-6 form-group">
						<label class="form-control-label">Username</label>
						<input class="form-control border-dark" type="text" name="username" placeholder="Username" value="<?php echo escape(Input::get('username')) ?>" autocomplet="off" />
					</div>
					<div class="col-lg-6 form-group">
						<label class="form-control-label">E-mail</label>
						<input class="form-control border-dark" type="email" name="email" placeholder="Email" value="<?php echo escape(Input::get('email')) ?>" autocomplet="off" />
					</div>
				</div>

				<!-- For password and confirm password -->
				<div class="form-row">
					<div class="col-lg-6 form-group">
						<label class="form-control-label">Password</label>
						<input class="form-control border-dark" type="password" name="password" placeholder="Password" autocomplet="off" />
					</div>
					<div class="col-lg-6 form-group">
						<label class="form-control-label">Confirm Password</label>
						<input class="form-control border-dark" type="password" name="cpassword" placeholder="Confirm password" autocomplet="off" />
					</div>
				</div>

				<!-- Hidden token class -->
				<div class="form-group">
					<input class="form-control border-dark" type="hidden" name="token" placeholder="Full name" value="<?php echo Token::generate() ?>" autocomplet="off" />
				</div>

				<!-- Submit button -->
				<div class="form-group d-block text-center">
					<button class="btn btn-block btn-dark justify-content-center" type="submit" name="register">Register</button>
				</div>

				<div class="form-group text-center">
                        <div class="d-inline">Have an account?</div>
                        <div class="d-inline text-right"><a href="login.php">Login</a></div>
                </div>
				
			</form>
		</div>
	</body>
</html>
<?php require_once 'javascript.php'; ?>