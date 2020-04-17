<?php
    require_once 'core/init.php';
    if(!Cookie::exists('hash'))
    {
        Redirect::to('beginpasswordreset.php');
    }else
    {
        $hash = new User;
        if (!$hash->findHash(Cookie::get('hash')))
        {
            Redirect::to('beginpasswordreset.php');
        }
    }

    if(Input::exists())
    {
        if(Token::check(Input::get('token')))
        {
            $hash = 
            $validate = new Validate();
            $validation = $validate->check($_POST, array(
                //Validate password
                'password' => array(
                'required' => true,
                'min' => 6,
                ),
                //Validate confirm password
                'cpassword' => array(
                'required' => true,
                'matches' => 'password'
                )
                ));

            // Check validation if its true
            if($validate->passed())
            {   
                $reset = new User();
                $salt = Hash::salt(16);
                $password = Hash::make(Input::get('password'), $salt);
                try
                {
                    $reset->passwordReset(Cookie::get('hash'), array(
                        'password' => $password,
                        'salt' => $salt
                    ));
                } catch (Exception $e)
                {
                    die($e->getMessage());  
                }
                //Session::flash('success', 'You have registered successfully');
                Redirect::to('login.php');
            }else
            {
                foreach ($validate->errors() as $error)
                {
                    echo $error ."<br>";
                }
            }
        }
    }
    
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Password Reset</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <!-- Bootstrap CSS -->
        <?php require_once 'bootstrap.php'; ?>
        <link rel="stylesheet" type="text/css" href="style.css">
    </head>
    <body>
        <div class=" ">
            
            <div class="container col-md-4 py-5 my-5" id="reg">
                <h4 class="text-success font-weight-bold text-center mt-5">New Password</h4>
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
                <form method = "POST" action = "passwordreset.php">
                    <!-- Password -->
                    <div class="input-group-lg mb-2">
                        <input class="border-info form-control" type="password" name="password" placeholder="Enter new password" autofocus autocomplete/>
                    </div>

                    <!-- Confirm Password -->
                    <div class="input-group-lg mb-2">
                        <input class="border-info form-control" type="password" name="cpassword" placeholder="Confirm new password" autofocus autocomplete/>
                    </div>

                    <!-- Hidden token class -->
                    <div class="form-group">
                        <input class="form-control border-dark" type="hidden" name="token" placeholder="Full name" value="<?php echo Token::generate() ?>" autocomplet="off" />
                    </div>

                    <div class="form-group">
                        <button class="btn btn-block btn-primary" type="submit" name="passwordreset">Reset Password</button>
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>
<?php require_once 'javascript.php'; ?>