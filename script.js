function validateSignUP() {
  var error = "";
  var name = document.forms["register_form"]["name"].value;
  var email = document.forms["register_form"]["email"].value;
  var password = document.forms["register_form"]["password"].value;
  var error_tag = document.getElementById("error");

  if (name == null || name == "") {
    // alert("Name must be filled out");
    error = "Name must be filled out";
  }
  document.getElementById("error").innerHTML = "";
  if (email == null || email == "") {
    // alert("Name must be filled out");
    error = "Email must be filled out";
  }
  if (password == null || password == "") {
    // alert("Name must be filled out");
    error = "Password must be filled out";
  }

  if (error == "") {
    error_tag.innerHTML = "";
    return true;
  }
  error_tag.innerHTML = error;
  return false;
}
