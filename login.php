<!DOCTYPE html>
<html lang="en">

<head>
  <title>Login</title>
  <link rel="stylesheet" href="styles.css" />
  <script src="script.js"></script>
</head>

<body>
  <div class="container">
    <div class="mid-box-center">
      <h1>Login</h1>
      <h4>Fill all the feilds</h4>
      <p id="error">$crediential_err</p>
      <br>
      <form name="login_form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>" onsubmit="return validateSignUP()" method="post">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="Email"><span class="error">* <?php echo $email_err; ?></span>
        <br>
        <label for="password">Password</label>
        <input type="password" id="password" name="password" value="Password"><span class="error">* <?php echo $password_err; ?></span>
        <br>
        <input type="submit" name="submit_form" class="button" value="Login" />
        <input type='submit' name='forget_password' class='button' value='Forget Password' />
        <input type='submit' name='register_btn' class='button' value='Register' />
      </form>
    </div>
  </div>
</body>

<?php
session_start();
require 'utils.php';
$helper = new Helper();

//method for forget password
if (array_key_exists('forget_password', $_POST)) {
  $email_err = "";
  if (empty($_POST["email"])) {
    $email_err = "email Name is required";
  }
  if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $email_err = "Invalid email format";
  }
  if ($email_err == "") {
    $email = $_POST['email'];
    $helper->SendMail($email);
  } else {
    echo "empty";
  }
}

// method for exsiting user
if (array_key_exists('register_btn', $_POST)) {
  header('Location: register.php');
}

//function to avoid sql injection
function test_input($data)
{
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

$servername = "localhost";
$username = "root";
$password_sql = "Bishal.123";
$dbname = "user_db";
$crediential_err = "";
$email_err = $password_err = "";

// user form validation, in submission
if (array_key_exists('submit_form', $_POST)) {
  if (empty($_POST["password"])) {
    $password_err = "password Name is required";
  }
  if (empty($_POST["email"])) {
    $email_err = "Last Name is required";
  }
  if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $email_err = "Invalid email format";
  }

  if ($email_err == "" && $password_err == "") {

    try {
      $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password_sql);
      
      // setting the PDO error mode to exception
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
      echo "Connection failed: " . $e->getMessage();
    }

    $sql_query = "SELECT id,user_email, user_password FROM users";
    $result = $conn->query($sql_query);

    while ($row = $result->fetch()) {
      if ($row["user_email"] == $_POST['email'] &&  $row["user_password"] == $_POST['password']) {

        //check if the user has updated the data or not
        $uid = $row["id"] . $row["user_email"];
        echo $uid;
        $sql_q = "SELECT * FROM user_data where uid=:uid";
        $stmt = $conn->prepare($sql_q);
        $stmt->execute(['uid' => $uid]);

        while ($user = $stmt->fetch()) {
          $first_name = $user['first_name'];
          if ($first_name == "") {
            $_SESSION["login"] = "1";
            $_SESSION["data"] = "0";
            $_SESSION["uid"] = $uid;
            $_SESSION["email"] = $_POST['email'];
            header("location: ../index.php");
            break;
          } else {
            $_SESSION["login"] = "1";
            $_SESSION["data"] = "1";
            $_SESSION["uid"] = $uid;
            header("location: ../welcome.php");
            break;
          }
        }
      } else {
        $crediential_err = "Username or password is invalid";
      }
    }
  }
}
?>

</html>