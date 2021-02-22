<?php
  require_once "pdo.php";
  require_once "header.php";
  require_once "util.php";
  session_start();

  if(isset($_POST['cancel'])){
    header("Location: index.php");
    return;
  }

  if(isset($_POST['fname']) && isset($_POST['lname']) &&
      isset($_POST['email']) && isset($_POST['username']) &&isset($_POST['password'])){
    if(!$_POST['fname'] || !$_POST['lname'] ||
      !$_POST['email'] ||!$_POST['username'] || !$_POST['password']){
      $_SESSION['error'] = "Please fill in all fields.";
      header("Location: register.php");
      return;
    }
    if(strpos($_POST['email'], "@") === false){
      $_SESSION['error'] = "Please fill in a valid email address.";
      header("Location: register.php");
    }
    $sql = "SELECT * FROM internal_user WHERE user_email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':email' => htmlentities($_POST['email'])));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!$row){
      $sql = "SELECT * FROM internal_user WHERE username = :username";
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array(':username' => htmlentities($_POST['username'])));
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if(!$row){
        $pw = hash_pw($_POST['password']); //function from util.php
        $sql = "INSERT INTO internal_user (user_first_name, user_last_name, username, user_email, password)
                VALUES (:fname, :lname, :username, :email, :pw)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
          ':fname' => htmlentities($_POST['fname']),
          ':lname' => htmlentities($_POST['lname']),
          ':username' => htmlentities($_POST['username']),
          ':email' => htmlentities($_POST['email']),
          ':pw' => $pw
        ));

        $_SESSION['success'] = "You've registered as a new user. Plesae log in.";
        header("Location: login.php");
        return;
      }
      else{
        $_SESSION['error'] = "The username already exists. Please try another username.";
        header("Location: register.php");
        return;
      }
    }else{
      $_SESSION['error'] = "This email already has an account. Please <a href='login.php'>log in</a> or use another email.";
      header("Location: register.php");
      return;
    }




  }
?>


<html>
<head>
  <title>Register</title>
</head>
<body>
  <div class="container">
    <div class="row" style="margin-top: 50px">
      <h1>Register</h1>
    </div>
    <div class="row">
      <?php
        if(isset($_SESSION['error'])){
          echo("<p class='lead text-danger'>".$_SESSION['error']."</p>");
          unset($_SESSION['error']);
        }
      ?>
    </div>


    <div class="row">
      <form method="post">
        <fieldset>
          <div class="form-group">
            <label for="fname">First Name</label>
            <input type="text" name = "fname" class="form-control" id="fname" required>
          </div>
          <div class="form-group">
            <label for="lname">Last Name</label>
            <input type="text" name = "lname" class="form-control" id="lname" required>
          </div>
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name = "email" class="form-control" id="email" aria-describedby="emailHelp" placeholder="Enter email" required>
          </div>
          <div class="form-group">
            <label for="username">Username</label>
            <input type="text" name = "username" class="form-control" id="username" placeholder="Username" required>
          </div>
          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name = "password" class="form-control" id="password" placeholder="Password" required>
          </div>
          <button type="submit" class="btn btn-primary">Register</button>
          <button type="submit" class="btn btn-primary" name="cancel" formnovalidate>Cancel</button>
        </fieldset>
      </form>
    </div>

  </div>
</body>
