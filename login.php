<?php

    require 'core/init.php';

    if(Input::exists())
        //CSRF Protection
        if(Token::check(Input::get('token'))){
        {
            $validate = new Validate();
            $validation = $validate->Check($_POST,  $validate->_loginItems);

            //Validate Input
            if($validate->passed()){
                //Instantiate user
                $user = new User();
                // Remember me
                $remember = (Input::get('remember') === 'on') ? true :false;
                //Login User
                $login = $user->login(Input::get('username'), Input::get('password'), $remember);
                if ($login)
                {
                    $message = "<p>You are welcome <b>" . escape($user->data()->username) . "<b><p>";
                    Session::flash('login', $message);
                    Redirect::to('index.php');
                } else
                {
                    echo "login failed";
                }
            } else
            //Display Errors
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
        <title>Login</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <!-- Bootstrap CSS -->
        <?php require_once 'bootstrap.php'; ?>
        <link rel="stylesheet" type="text/css" href="style.css">
    </head>
    <body>
        <div class="login-page">
            
            <div class="container col-md-4 py-5 my-5" id="reg">
                <h2 class="text-dark font-weight-bold text-center mt-5">LOGIN</h2>
                <!-- Display Error -->
                <!-- <?php if(count($errors) > 0){ ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error){ ?>
                    <li>
                        <?php echo "$error"; ?>
                    </li>
                    <?php } ?>
                </div>
                <?php } ?> -->
                <!-- Form start -->
                <form method = "POST" action = "login.php">

                    <!-- Username or Email -->
                    <div class="input-group-lg mb-2">
                        <input class="border-dark form-control" type="text" name="username" value="<?php echo escape(Input::get('username')); ?>"placeholder="Username or Email" autofocus autocomplete/>
                    </div>

                    <!-- Password -->
                    <div class="input-group-lg mb-2">
                        <input class="border-dark form-control" type="password" name="password" placeholder="Password">
                    </div>

                    <!-- Hidden token class -->
                    <div class="form-group">
                        <input class="form-control border-dark" type="hidden" name="token" placeholder="Full name" value="<?php echo Token::generate() ?>" autocomplet="off" />
                    </div>

                    <div class="form-check mb-2">
                        <input class="border-dark form-check-input" type="checkbox" name="remember" id="rememberMe" placeholder="Password">
                        <label class="form-check-label" for="rememberMe">Remember me</label>
                    </div>

                    <!-- Login Button -->
                    <div class="form-group">
                        <button class="btn btn-block btn-dark" type="submit" name="login">Login</button>
                    </div>
                    <div class="form-group text-center">
                        <div class="d-inline">Don't have an account? </div>
                        <div class="d-inline text-right"><a href="register.php">Register</a></div>
                        <p><a href="beginpasswordreset.php">Forgot Password?</a></p>
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>
<?php require_once 'javascript.php'; ?>