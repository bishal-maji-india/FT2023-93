<?php

use Fpdf\Fpdf;

session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <!-- link to stylesheet -->
  <link href="styles.css" rel="stylesheet" />
  <script src="script.js"></script>

  <!-- this script helps in generation and creating pdf format file -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.0.272/jspdf.debug.js"></script>
  <title>Document</title>
</head>

<body>

  <?php

  // accessing the global session varialble
  $servername = "localhost";
  $username = "root";
  $password_sql = "Bishal.123";
  $dbname = "user_db";

  try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password_sql);
    // setting the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  } catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
  }

  $uid = $_SESSION["uid"];
  $sql_query = "SELECT * FROM user_data WHERE uid=?";
  global $conn;
  $statement = $conn->prepare($sql_query);
  $statement->execute([$uid]);

  $row = $statement->fetch();

  $first_name_v = $row['first_name'];
  $last_name_v = $row["last_name"];
  $full_name_v = $first_name_v . ' ' . $last_name_v;
  $user_img_v = $row["user_image"];
  $marks_v = $row["marks"];
  $phone_no_v = "+91" . $row["phone"];
  $mail_v = $row["email"];



  $destination_path = "http://localhost/images/";
  $file_path = $destination_path . $user_img_v;


  // structuring the marks of user for printing in pdf
  $mark_temp_array = $sub_temp_array = array();
  $mark_sub_array = preg_split('/\r\n|\r|\n/', $marks_v);
  $j = sizeof($mark_sub_array);
  $i = 0;
  if (array_key_exists('button_download', $_POST)) {
    while ($i < $j) {
      $sub_and_mark = preg_split("/\|/", $mark_sub_array[$i]);
      $sub_temp_array[$i] = $sub_and_mark[0];
      $mark_temp_array[$i] = $sub_and_mark[1];
      $i++;
    }
    DownloadPdf($full_name_v, $phone_no_v, $mail_v, $mark_temp_array, $sub_temp_array, $file_path);
  }


  // function to download the data in pdf format
  function DownloadPdf($full_name, $phone, $mail, $mark_temp_array, $sub_temp_array, $image_path)
  {
    ob_start();
    require('vendor/autoload.php');
    $pdf = new FPDF();
    $pdf->AddPage('P', 'A4');
    $pdf->Image($image_path, 150, 10, 40);
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(100, 10, "Full name=$full_name");
    $pdf->Ln();
    $pdf->Cell(100, 10, "Phone number=$phone");
    $pdf->Ln();
    $pdf->Cell(100, 10, "Email=$mail");
    $pdf->Ln();
    $pdf->Cell(100, 20, "Marks Table");
    $pdf->Ln();
    $pdf->Cell(50, 10, "Subjects");
    $pdf->Cell(80, 10, "Marks");
    $pdf->Ln();
    for ($j = 0; $j < sizeof($mark_temp_array); $j++) {
      $pdf->Cell(50, 10, $sub_temp_array[$j]);
      $pdf->Cell(70, 10, $mark_temp_array[$j]);
      $pdf->Ln();
    }
    $pdf->Output('D', 'somethig.pdf');
    ob_end_flush();
  }
  ?>

  <div class="container" id="container">
    <div class="mid-box-center">
      <img src='<?php global $file_path;
                echo $file_path; ?>' width='150px' height='auto' />
      <h2><?php global $full_name_v;
          echo $full_name_v ?></h2>
      <h4>Marks Obtained</h4>

      <table id='table_my'>
        <tr>
          <th>Subjects</th>
          <th>Marks</th>
        </tr>
        <br>
        <?php
        $mark_temp_array = $sub_temp_array = array();
        $mark_sub_array = preg_split('/\r\n|\r|\n/', $marks_v);
        $j = sizeof($mark_sub_array);
        $i = 0;
        while ($i < $j) {
          $sub_and_mark = preg_split("/\|/", $mark_sub_array[$i]);
          $sub_temp_array[$i] = $sub_and_mark[0];
          $mark_temp_array[$i] = $sub_and_mark[1];
          $i++;
        }

        for ($a = 0; $a < sizeof($sub_temp_array); $a = $a + 1) {
        ?>
          <tr>
            <td> <?php echo $sub_temp_array[$a] ?></td>
            <td> <?php echo $mark_temp_array[$a] ?></td>
          </tr>
        <?php

        } ?>

      </table>
      <form method='post' class='button-wrapper'>
        <input type='submit' name='button_download' class='button' value='Download Data' />
      </form>

    </div>
  </div>


</body>

</html>