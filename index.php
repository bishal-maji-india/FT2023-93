<?php session_start();

require('User.php');

//first to check if the user is already loged in or not- if not, send to login.php page
if (!isset($_SESSION["login"])) {
  header("location: login.php");
} else {
  //if loged in, check if user inserted his data, if yes, go to welcome.php page.
  if ($_SESSION["data"] == "1") {
    header("location: welcome.php");
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<!-- link to global stylesheet -->
<link href="styles.css" rel="stylesheet" />

<head>
  <title>Document</title>
</head>

<?php
$first_name_err = $last_name_err = $img_upload_err = $mark_err = $phone_err = $mail_err = "";
$first_name = $last_name = "";

//form submission handling
if (array_key_exists('submit', $_POST)) {
  if (empty($_POST["first_name"])) {
    $first_name_err = "First Name is required";
  }
  if (is_numeric($_POST["first_name"])) {
    $first_name_err = "Must be an alphaber";
  }
  if (empty($_POST["last_name"])) {
    $last_name_err = "Last Name is required";
  }
  if (is_numeric($_POST["last_name"])) {
    $last_name_err = "Must be an alphaber";
  }
  if (empty($_POST["marks"])) {
    $mark_error = "marks field is required";
  }
  if (empty($_POST["phone"])) {
    $phone_err = "Phone Number is required";
  }
  if (!is_numeric($_POST["phone"])) {
    $phone_err = "Must be an number";
  }
  if (strlen($_POST["phone"]) != 10) {
    $phone_err = "Number Must be of 10 digits";
  }

  if ($first_name_err == "" && $last_name_err == "" && $mark_err == "" && $phone_err == "" && $mail_err == "") {
    $upload_directory = "images/";
    $destination_path = $upload_directory . basename($_FILES['user_image']['name']);
    $ready_to_upload = 1;
    $image_type = strtolower(pathinfo($destination_path, PATHINFO_EXTENSION));

    //check if file already exist
    if (file_exists($destination_path)) {
      // deleting the file
      unlink($destination_path);
    }

    //uploading only the specific formate
    if (
      $image_type != "jpg" && $image_type != "png" && $image_type != "jpeg"
      && $image_type != "gif"
    ) {
      $img_upload_err = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
      $ready_to_upload = 0;
    }

    //finally upload the image if everything is good
    if ($ready_to_upload == 1) {
      if (move_uploaded_file($_FILES['user_image']['tmp_name'], $destination_path)) {
        $_SESSION["user_image"] = basename($_FILES['user_image']['name']);
        uploadDataInSql();
        exit;
      }
    }
  }
}

//method to insert the data in server.
function uploadDataInSql()
{
  // Looing for .env at the root directory
  $_ENV = parse_ini_file('.env');

  // Retrive env variable
  $servername = $_ENV['SERVENAME'];
  $username = $_ENV['USERNAME'];
  $password_sql = $_ENV['PASSWORD_SQL'];
  $dbname = $_ENV['DB_NAME'];

  try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password_sql);
    // setting the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $user = new User();
    $user->set_first_name($_POST['first_name']);
    $user->set_last_name($_POST['last_name']);
    $user->set_email($_POST['first_name']);
    $user->set_phone($_POST['phone']);
    $user->set_marks($_POST['marks']);
    $user->set_user_image($_SESSION["user_image"]);
    
    /* Step 1: prepare */
    $param_f_name = $user->get_first_name();
    $param_l_name = $user->get_last_name();
    $param_phone = $user->get_phone();
    $param_marks = $user->get_marks();
    $param_user_img = $user->get_user_image();
    $param_uid = $_SESSION["uid"];
    $sql = "UPDATE user_data SET first_name=?, last_name=?, phone=?, marks=?, user_image=? WHERE uid=?";

    // Prepare statement and bind data
    $statement = $conn->prepare($sql);

    // If UPDATE succeeded or not.
    if ($statement->execute([$param_f_name, $param_l_name, $param_phone, $param_marks, $param_user_img, $param_uid]))
      echo "Connected successfully";
    $_SESSION["login"] = "1";
    $_SESSION["data"] = "1";
    $_SESSION["uid"] = $param_uid;
    header("location: welcome.php");
    exit;
  } catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
  }
}

//to logout the user this method is runned
if (array_key_exists('button_logout', $_POST)) {
  unset($_SESSION["login"]);
  header("location: login.php");
}
?>

<body>
  <div class="container">
    <div class="mid-box-center">
      <h1>Homepage</h1>
      <h4>Fill all the feilds</h4>
      <p id="error"></p>
      <form name="task_six_form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post" enctype="multipart/form-data">
        <h4>First Name : <input type="text" class="f_name" name="first_name"><span class="error">* <?php echo $first_name_err; ?></span></h4>
        <h4>Last Name : <input type="text" class="l_name" name="last_name"><span class="error">* <?php echo $last_name_err; ?></span></h4>
        <h4>Full Name : <input type="text" class="fu_name" disabled name="full_name"></h4>
        <input type="file" name="user_image" id="user_image" />*<span class="error"> <?php echo $img_upload_err; ?></span>
        <h4>Subject Marks : <textarea type="text" class="sub-marks" name="marks"></textarea><span class="error">* <?php echo $mark_err; ?></span></h4><br>
        <h4>Phone Number : <input type="number" name="phone"> <br><span class="error">* <?php echo $phone_err; ?></span></h4>
        <h4>User Email : <input type="text" disabled name="email" value="<?php echo $_SESSION['email']; ?>"><br><span class="error">* <?php echo $mail_err; ?></span></h4>
        <input type="submit" name="submit" class="button" value="Update user" />
        <input type='submit' name='button_logout' class='button' value='Logout' />
      </form>
    </div>
  </div>
</body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
<script>
  $(document).ready(function() {
    $("input").keyup(function() {
      $(".fu_name").val("");
      $(".fu_name").val($(".f_name").val() + " " + $(".l_name").val());
    });
  });
</script>

</html>