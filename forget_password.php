<!DOCTYPE html>
<html lang="en">

<head>
  <link href="styles.css" rel="stylesheet" />
  <title>Forget Password</title>
</head>

<body>
  <div class="container">
    <div class="mid-box-center">
      <h1>change password</h1>
      <form name="forget_form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post">
        <label for="new_password">New Password</label>
        <input type="password" id="new_password" name="new_password" value="New Password"><span class="error">* <?php echo $new_pass_err; ?></span>
        <br>
        <label for="confirm_password">Confirm password</label>
        <input type="password" id="confirm_password" name="confirm_password" value="Confirm Password"><span class="error">* <?php echo $confirm_pass_err; ?></span>
        <br>
        <input type="submit" name="forget_submit_form" class="button" value="Change Password">
        <input type="submit" name="login_btn" class="button" value="Login">
      </form>
    </div>
  </div>
</body>

<?php
$confirm_pass_err = $new_pass_err = "";

//metod for handling forget password submission
if (array_key_exists('forget_submit_form', $_POST)) {
  if (empty($_POST["new_password"])) {
    $new_pass_err = "new password is required";
  }
  if (empty($_POST["confirm_password"])) {
    $confirm_pass_err = "confirm password is required";
  }
  if ($_POST["new_password"] != $_POST["confirm_password"]) {
    $confirm_pass_err = 'password not matched';
  } else {
  }
}

//go to login page
if (array_key_exists('login_btn', $_POST)) {
  header('Location: login.php');
  exit;
}
?>

</html>