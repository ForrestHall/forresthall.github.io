<?php

session_start();

$_SESSION['message'] = '';
$_SESSION['username'] = '';
$_SESSION['password'] = '';
$_SESSION['email'] = '';

$mysqli =new mysqli('localhost', 'root', '', 'accounts');

if ($_SERVER['REQUEST_METHOD'] == 'POST') { 
    
    $username = htmlentities($_POST['username']);
    $email = htmlentities($_POST['email']);
    $_SESSION['username'] = $username;
    $_SESSION['email'] = $email;
    
    if ($_POST['password'] == $_POST['confirmpassword']) {
        
        $username = $mysqli->real_escape_string($_POST['username']);
        $email = $mysqli->real_escape_string($_POST['email']);
        
        //md5 hash password for security
        $password = md5($_POST['password']);
        
        //path of avatar image will be stored
        $avatar_path = $mysqli->real_escape_string('images/'.$_FILES['avatar']['name']);
        
        if (preg_match("!image!",$_FILES['avatar']['type'])) {
            if (copy($_FILES['avatar']['tmp_name'], $avatar_path)){
                
                $_SESSION['avatar'] == $avatar_path;
                
                $sql = "INSERT INTO users (username, email, password, avatar) VALUES (
                    '".$username."', 
                    '".$email."', 
                    '".$password."', 
                    '".$avatar_path."'
                )";
                //sucessful? redirect to welcome.php
                if ($mysqli->query($sql) === true ) {
                    $_SESSION['message'] = "Registration successful! Added ".$username." to the database!";
                    header("location: welcome.php");
                } else {
                   $_SESSION['message'] = "User could not be added to the database!";
                }
            } else {
                $_SESSION['message'] = "File upload failed!";
            }
        } else {
            $_SESSION['message'] = "Please only upload GIF, JPG, or PNG images!";
        }
        
    } else {
        $_SESSION['message'] = "Two passwords do not match!";
    }
    
} // end REQUEST_METHOD check
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF -8">
        <title>BMW OWNERS GROUP</title>
        <link href="//db.onlinewebfonts.com/c/a4e256ed67403c6ad5d43937ed48a77b?family=Core+Sans+N+W01+35+Light" rel="stylesheet" type="text/css"/>
        <link rel="stylesheet" href="http://forresthall.github.io/form.css" type="text/css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    </head>
    <body>
        <div class="body-content">
            <div class="module">
                <h1>Create an account</h1>
                <form class="form" action="form.php" method="post" enctype="multipart/form-data" autocomplete="off">
                    <?php if(!empty($_SESSION['message'])):?>
                    <div class="alert alert-error">
                        <?= $_SESSION['message']?>
                    </div>
                    <?php endif;?>
                    <input type="text" placeholder="User Name" name="username" value="<?php echo $_SESSION['username'];?>" aria-label="User Name" required />
                    <input type="email" placeholder="Email" name="email" value="<?php echo ((!empty($_SESSION['email']))?$_SESSION['email']:"");?>" aria-label="Email Address" required />
                    <input id="password" type="password" placeholder="Password" name="password" autocomplete="new-password" value="<?php echo $_SESSION['password'];?>" aria-label="Password" required />
                    <input id="confirmpassword" type="password" placeholder="Confirm Password" name="confirmpassword" autocomplete="new-password" aria-label="Confirm Password" required />
                    <div class="avatar">
                        <label for="avatar">Select your Profile Pic: </label>
                        <input id="avatar" type="file" name="avatar" accept="image/*" required />
                    </div>
                    <input type="submit" value="Register" name="register" aria-label="Submit Signup Form" class="btn btn-block btn-primary" />
                </form>
            </div>
        </div>
        <script type="text/javascript">
            $(function(){
                
                <?php if(!empty($_SESSION['message'])):?> 
                var message = '<?php echo $_SESSION['message'];?>';
                if(message == "Two passwords do not match!") {
                    $("#password").focus();
                }
                <?php endif;?>
                
            });
        </script>
    </body>
</html>
