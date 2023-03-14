<!DOCTYPE html>
<html lang="en">

<head>
  <title>Register</title>
  <link rel="stylesheet" href="styles.css" />
  <script src="script.js"></script>
</head>

<body>
  <div class="container">
    <div class="mid-box-center">
      <h1>Register</h1>
      <h4>Fill all the feilds</h4>
      <p id="error"></p>
      <br>
      <form name="register_form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>" onsubmit="return validateSignUP()" method="post">
        <label for="name">Name</label>
        <input type="name" id="name" name="name" value="Name"><span class="error">* <?php echo $name_err; ?></span>
        <br>
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="Email"><span class="error">* <?php echo $email_err; ?></span>
        <br>
        <label for="password">Password</label>
        <input type="password" id="password" name="password" value="Password"><span class="error">* <?php echo $password_err; ?></span>
        <br>
        <input type="submit" name="submit_form" class="button" value="Register">

      </form>
    </div>
  </div>
</body>
<!-- register the user and save in sql -->
<?php
$servername = "localhost";
$username = "root";
$password_sql = "Bishal.123";
$dbname = "user_db";
$name_err = $email_err = $password_err = "";

// user form validation
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (empty($_POST["name"])) {
    $name_err = "First Name is required";
  }
  if (empty($_POST["password"])) {
    $password_err = "Last Name is required";
  }
  if (empty($_POST["email"])) {
    $email_err = "Last Name is required";
  }
  if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $email_err = "Invalid email format";
  }

    //api call for email validation
    $mail_id=$_POST["email"];
    $curlObj = curl_init();
    curl_setopt_array($curlObj, array(
      CURLOPT_URL => "https://api.apilayer.com/email_verification/check?email=" . $mail_id,
      CURLOPT_HTTPHEADER => array(
        "Content-Type: text/plain",
        "apikey: kG3IBcX6qqFzwvOXaJnVRPq3luu9TL4O"
      ),
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_FOLLOWLOCATION => true,
    ));

    $response = curl_exec($curlObj);
    curl_close($curlObj);
    $decoded_result = json_decode($response, true);

    // check the email format is valid or not
    if (!$decoded_result["format_valid"]) {
      $email_err = "email format is not valid";
    }

  if ($name_err == "" && $email_err == "" && $password_err == "") {

    try {
      $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password_sql);
      // setting the PDO error mode to exception
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      echo "Connected successfully";
    } catch (PDOException $e) {
      echo "Connection failed: " . $e->getMessage();
    }

    $count_query = "SELECT count(*) from users";
    $count_res = $conn->query($count_query);
    $count_result = 1;

    while ($count_res->fetch()) {
      $count_result = $count_result + 1;
    }

    /* Step 1: prepare */
    $sql = "INSERT INTO users(user_name, user_email, user_password) VALUES (?, ?, ?)";
    $query = $conn->prepare($sql);

    
    $param_name = $_POST['name'];
    $param_email = $_POST['email'];
    $param_password = $_POST['password'];
    $param_uid = $count_result . $param_email;

    /* Step 2: bind and execute */
    $query->bindParam(1, $param_name, PDO::PARAM_STR);
    $query->bindParam(2, $param_email, PDO::PARAM_STR);
    $query->bindParam(3, $param_password, PDO::PARAM_STR);

    if ($query->execute()) {
      //loged in now enter the data with empty feild to update latte
      InsertDataInSql($param_uid, $param_email);
    } else {
      echo "Error: " . $sql . "<br>" . $conn->errorInfo();
    }
  }
}

//insert data with empty field first
function InsertDataInSql($param_uid, $param_email)
{
  $servername = "localhost";
  $username = "root";
  $password_sql = "Bishal.123";
  $dbname = "user_db";

  try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password_sql);
    // setting the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully";
  } catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
  }

  /* Step 1: prepare */
  $sql = "INSERT INTO user_data(first_name, last_name, email, phone,marks,user_image,uid) VALUES (?, ?, ?, ? , ? , ?,?)";
  $query = $conn->prepare($sql);

  $param_f_name = "";
  $param_l_name = "";
  $param_phone = "";
  $param_marks = "";
  $param_user_img = "";

  /* Step 2: bind and execute */
  $query->bindParam(1, $param_f_name, PDO::PARAM_STR);
  $query->bindParam(2, $param_l_name, PDO::PARAM_STR);
  $query->bindParam(3, $param_email, PDO::PARAM_STR);
  $query->bindParam(4, $param_phone, PDO::PARAM_STR);
  $query->bindParam(5, $param_marks, PDO::PARAM_STR);
  $query->bindParam(6, $param_user_img, PDO::PARAM_STR);
  $query->bindParam(7, $param_uid, PDO::PARAM_STR);


  if ($query->execute()) {
    session_start();
    $_SESSION["login"] = "1";
    $_SESSION["data"]="0";
    $_SESSION["uid"] = $param_uid;
    $_SESSION["email"] = $param_email;
    header("location: index.php");
  } else {
    echo "Error: " . $sql . "<br>" . $conn->errorInfo();
  }
}

?>

</html>