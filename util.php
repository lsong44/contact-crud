<?php
  //hash pw
  function hash_pw($pw){
    $salt = 'b$Yq5(7S';
    return hash('md5', $salt.$pw);
  }

  //data validation
  function validateEmail(){
    for($i=1; $i<=10; $i++){
      if(!isset($_POST['email'.$i])) continue;

      $email = $_POST['email'.$i];

      if(strlen($email) == 0){
        return "All fields are required";
      }
      if(strpos($email, '@') === false){
        return "Please enter a valid email";
      }
    }
    return true;
  }

  function validatePhone(){
    for($i=1; $i<=10; $i++){
      if(!isset($_POST['phone'.$i])) continue;

      $phone = $_POST['phone'.$i];

      if(strlen($phone) != 10){
        return "Please enter a valid phone number";
      }
    }
    return true;
  }

  function validateAddress(){
    for($i=1; $i<=10; $i++){
      if(!isset($_POST['street'.$i])) continue;
      if(!isset($_POST['city'.$i])) continue;
      if(!isset($_POST['zip_code'.$i])) continue;

      $street = $_POST['street'.$i];
      $city = $_POST['city'.$i];
      $zip = $_POST['zip_code'.$i];

      if(strlen($city) == 0){
        return "City is required in address";
      }
    }
    return true;
  }
