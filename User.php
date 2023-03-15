<?php 
class User{
  public $first_name;
  public $last_name;
  public $marks;
  public $phone;
  public $user_image;
  public $email;

  // Methods
  function set_first_name($first_name) {
    $this->first_name = $first_name;
  }
  function get_first_name() {
    return $this->first_name;
  }
  function set_last_name($last_name) {
    $this->last_name = $last_name;
  }
  function get_last_name() {
    return $this->last_name;
  }
  function set_marks($marks) {
    $this->marks = $marks;
  }
  function get_marks() {
    return $this->marks;
  }
  function set_phone($phone) {
    $this->phone = $phone;
  }
  function get_phone() {
    return $this->phone;
  }
  function set_email($email) {
    $this->email = $email;
  }
  function get_email() {
    return $this->email;
  }
  function set_user_image($user_image) {
    $this->user_image = $user_image;
  }
  function get_user_image() {
    return $this->user_image;
  }
}
?>