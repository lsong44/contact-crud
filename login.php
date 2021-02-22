<?php
  //existing user: li.song@xenonhealth.com, lsong, 000
  require_once "pdo.php";
  require_once "header.php";
  require_once "util.php";
  session_start();

  if(isset($_POST['cancel'])){
    header("Location: index.php");
    return;
  }

  if(isset($_POST['email']) && isset($_POST['password'])){
    if(!$_POST['email'] || !$_POST['password']){
      $_SESSION['error'] = "All fields are required.";
      header("Location: login.php");
      return;
    }
    $email = htmlentities($_POST['email']);
    $pw = hash_pw($_POST['password']);

    if(strpos($email, '@') === false){
      $_SESSION['error'] = "Invalid email.";
      header("Location: log.php");
      return;
    }

    $sql = "SELECT * FROM internal_user WHERE user_email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':email' => $email));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$row){
      $_SESSION['error'] = "This user does not exist. Please <a href='register.php'>register</a>";
      header("Location: login.php");
      return;
    }

    if($row['password'] !== $pw){
      $_SESSION['error'] = "Password incorrect.";
      header("Location: login.php");
      return;
    }

    $_SESSION['user_id'] = $row['user_id'];
    $_SESSION['username'] = $row['username'];
    $_SESSION['user_fname'] = $row['user_first_name'];
    $_SESSION['user_email'] = $row['user_email'];
    $_SESSION['pw'] = $row['password'];
    $_SESSION['success'] = "Welcome ".$_SESSION['user_fname'];
    header("Location: view.php?user=".$_SESSION['username']);
    return;

  }


?>


<html>
<head>
  <title>Login</title>

</head>
<body>
  <div class="container">
    <div class="page-header">
      <div class="row" style="margin-top: 50px">
        <?php
        if(isset($_SESSION['success'])){
          echo("<p class='lead text-success'>".$_SESSION['success']."</p>");
          unset($_SESSION['success']);
        }
        ?>
      </div>
      <div class="row">
        <h2>Please Login</h2>
      </div>
    </div>
    <div class="row">
      <?php
        if(isset($_SESSION['error'])){
          echo("<p class='lead text-danger'>".$_SESSION['error']."</p>\n");
          unset($_SESSION['error']);
        }
      ?>
    </div>
    <div class="row">
      <form method="post">
        <fieldset>
          <div class="form-group">
            <label for="email">Email address</label>
            <input type="email" name = "email" class="form-control" id="email" placeholder="Enter email" required>
          </div>
          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name = "password" class="form-control" id="password" placeholder="Password" required>
          </div>
          <button type="submit" class="btn btn-primary">Log in</button>
          <button type="submit" class="btn btn-primary" name="cancel" formnovalidate>Cancel</button>
        </fieldset>
      </form>
    </div>
  </div>
</body>
