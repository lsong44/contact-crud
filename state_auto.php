<?php
  require_once "pdo.php";
  session_start();

  if(!isset($_SESSION['user_id'])){
    die("Not logged in.");
  }

  $sql = "SELECT state_name FROM state WHERE state_name LIKE :prefix";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(':prefix' => $_REQUEST['term']."%"));
  $state_selection = Array();
  while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
    $state_selection[] = $row['state_name'];
  }

  echo(json_encode($state_selection, JSON_PRETTY_PRINT));
