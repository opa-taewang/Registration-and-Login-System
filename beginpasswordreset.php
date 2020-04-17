<?php
    require_once 'core/init.php';

    if(Input::exists())
    {
        if(Token::check(Input::get('token')))
        {
            $validate = new Validate();
            $validation = $validate->Check($_POST,  array(
             //Validate email
            'email' => array(
                'required' => true,
                'min' => 2,
                'max' => 50,
                'type' => 'email'
            )));
            if ($validation->passed()) {
                $user = new User();
                $send = $user->beginPasswordReset(Input::get('email'));
                if(!$send)
                {
                    echo "Password Generation failed";
                }
            }
            else {
                foreach ($validate->errors() as $error) {
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
                <h3 class="text-primary font-weight-bold text-center mt-5">RESET PASSWORD</h3>
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
                <form method = "POST" action = "beginpasswordreset.php">
                    <div class="input-group-lg mb-2">
                        <input class="border-info form-control" type="text" name="email" placeholder="Enter your email" autofocus autocomplete/>
                    </div>

                    <!-- Hidden token class -->
                    <div class="form-group">
                        <input class="form-control border-dark" type="hidden" name="token" placeholder="Full name" value="<?php echo Token::generate() ?>" autocomplet="off" />
                    </div>

                    <div class="form-group">
                        <button class="btn btn-block btn-primary" type="submit" name="reset">Reset Password</button>
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>
<?php require_once 'javascript.php'; ?>