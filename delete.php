<?php
  require_once "pdo.php";
  session_start();

  if(!isset($_SESSION['username'])){
    die("Please log in.");
  }

  if(isset($_POST['cancel'])){
    header("Location: view.php?user=".$_SESSION['email']);
    return;
  }

  if(isset($_POST['id']) && isset($_POST['delete'])){
    $sql = "DELETE p, a, e, ph FROM Profile p LEFT JOIN Address a ON p.profile_id = a.profile_id
            LEFT JOIN Email e ON e.profile_id = p.profile_id
            LEFT JOIN Phone ph ON p.profile_id = ph.profile_id
            WHERE p.profile_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':id'=>$_POST['id']));

    $_SESSION['success'] = "Record Deleted";
    header("Location: view.php?user=".$_SESSION['username']);
    return;
  }

  $sql = "SELECT * FROM Profile WHERE profile_id = :id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(':id' => htmlentities($_GET['profile_id'])));
  $row = $stmt->fetch(PDO::FETCH_ASSOC);

  if(!$row){
    $_SESSION['error'] = "The record cannot be found.";
    header("Location: view.php?user=".$_SESSION['username']);
    return;
  }

?>

<html>
<head>
  <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
</head>
<body>
  <div class="container">
    <div class="row" style="margin-top: 50px">
      <p class="lead">Delete <?php echo($row['first_name']." ".$row['last_name']) ?> permenantly from the system?</p>
    </div>
    <div class="row">
      <form method="post">
        <fieldset>
          <input type="hidden" name="id" value=<?php echo($row['profile_id'])?>>
          <input type="submit" name="delete" class="btn btn-danger" value="Delete">
          <button type="submit" name="cancel" class="btn btn-secondary">Cancel</button>
        </fieldset>
      </form>

    </div>
  </div>
