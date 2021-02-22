<?php
  require_once "pdo.php";
  require_once "header.php";

  session_start();

  if(!isset($_SESSION['user_id'])){
    die("Please log in.");
  }

  if(!isset($_GET['profile_id'])){
    die("Invalid profile id.");
  }
  $profile_id = $_GET['profile_id'];
  $sql = "SELECT * FROM Profile AS p LEFT JOIN state AS s ON p.state_id = s.state_id
          LEFT JOIN source AS src ON p.source_id = src.source_id
          LEFT JOIN internal_user i ON i.user_id = p.user_id WHERE profile_id = :id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(':id' => $_GET['profile_id']));
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if(!$row){
    $_SESSION['error'] = "The record no longer exists.";
    header("Location: view.php");
    return;
  }

?>
<html>
<head>
  <title><?php echo($row['first_name']." ".$row['last_name'])?></title>
</head>
<body>
  <div class="container">
    <div class="page-header">
      <div class="row" style="margin-top: 50px">
    <h1><?php echo($row['first_name']." ".$row['last_name'])?>'s Profile</h1>
  </div>
    <p class="small">Created by <strong><?php echo($row['user_first_name']." ".$row['user_last_name'])?></strong>
       at <strong><?= $row['added_time'] ?></strong></p>
    <p class="small">Last updated at <strong><?=$row['edited_time']?></strong>.</p>
    <p>Name: <?php echo($row['first_name']." ".$row['last_name'])?></p>
    <p>Title: <?php echo($row['title'])?></p>
    <p> Transferred to Backup List:
      <?php
        if($row['transferred_backup']){
          echo ("Yes");
        }else{
          echo("No");
        }
      ?>
    </p>
    <p> Last update: <?php echo($row['edited_time']) ?></p>
    <p>State: <?php echo($row['state_name'])?></p>
    <?php
      $sql = "SELECT * FROM Address a LEFT JOIN state s ON a.state_id = s.state_id
              WHERE a.profile_id = :id";
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array(':id' => $_GET['profile_id']));
      $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
      if($rows){
        echo "<p>Address:</p>\n";
        echo "<ul>\n";
        foreach($rows as $row){
          echo("<li>".$row['street']."<br>".$row['city'].", ".$row['state_name']." ".$row['zip_code']);
          echo "</li>\n";
        }
        echo "</ul>\n";
      }

      $sql = "SELECT * FROM Email WHERE profile_id = :id";
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array(':id' => $_GET['profile_id']));
      $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
      if($rows){
        echo "<p>Email:</p>\n";
        echo "<ul>\n";
        foreach($rows as $row){
          echo("<li>".$row['email']."</li>\n");
        }
        echo "</ul>\n";
      }

      $sql = "SELECT * FROM Phone WHERE profile_id = :id";
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array(':id' => $_GET['profile_id']));
      $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
      if($rows){
        echo "<p>Phone number:</p>\n";
        echo "<ul>\n";
        foreach($rows as $row){
          echo("<li>".$row['phone']."</li>\n");
        }
        echo "</ul>\n";
      }
    ?>
    </p>
    <p><a href="view.php?user=<?= $_SESSION['username']?>"><button class="btn btn-primary">Done</button></a>
  </div>
</div>
