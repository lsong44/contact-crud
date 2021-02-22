<?php
  require_once "pdo.php";
  require_once "header.php";
  require_once "util.php";
  session_start();

  if(!isset($_SESSION['user_id']) || !isset($_SESSION['username']) ||
      !isset($_SESSION['pw']) || !isset($_SESSION['user_email'])){
    die("Please log in.");
  }

  if(isset($_POST['add'])){
    header("Location: add.php?user=".$_SESSION['username']);
    return;
  }

  if(isset($_POST['logout'])){
    header("Location: logout.php");
    return;
  }


  $stmt = $pdo->query("SELECT * FROM Profile AS p LEFT JOIN internal_user as i
                        ON p.user_id = i.user_id
                        LEFT JOIN source AS s ON p.source_id = s.source_id
                        LEFT JOIN state AS st ON p.state_id = st.state_id
                        ORDER BY p.added_time DESC");
  $rows = $stmt->fetchAll(PDO:: FETCH_ASSOC);
?>

<html>
<head>
  <title>Profile</title>
</head>
<body>
  <div class="container">
    <div class="row" style="margin-top: 50px">
      <div calss="page-header">
        <h1>Profile List</h1>
      </div>
    </div>
    <div class="row">

        <?php
          if(isset($_SESSION['success'])){
            echo("<p class='lead text-success'>".$_SESSION['success']."</p>");
            unset($_SESSION['success']);
          }

          if(isset($_SESSION['error'])){
            echo("<p class='lead text-danger'>".$_SESSION['error']."</p>");
            unset($_SESSION['error']);
          }
        ?>

    </div>

    <div class="row">
      <form method="post">
        <button type="submit" name = "add" class="btn btn-primary">Add New</button>
        <button type="submit" name = "logout" class="btn btn-secondary">Log Out</button>
      </form>
    </div>

    <div class="row" style="margin-top: 50px">
      <table class="table table-hover">
        <thead>
          <tr>
            <th scope="col">Name</th>
            <th scope="col">Title</th>
            <th scope="col">State</th>
            <th scope="col">Added Time</th>
            <th scope="col">Created By</th>
            <th scopt="col">Source</th>
            <th scope="col">In Backup List?</th>
            <th scope="col">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
            foreach($rows as $row){
              if($row['transferred_backup'] == 0){
                $backup = "No";
              }else{
                $backup = "Yes";
              }
              echo "<tr><td>";
              echo('<a href="detail.php?profile_id='.$row['profile_id'].'">');
              echo($row['first_name']." ".$row['last_name']);
              echo("</a>");
              echo "</td><td>";
              echo($row['title']);
              echo "</td><td>";
              echo($row['state_name']);
              echo "</td><td>";
              echo($row['added_time']);
              echo "</td><td>";
              echo($row['user_first_name']." ".$row['user_last_name']);
              echo "</td><td>";
              echo($row['source_name']);
              echo "</td><td>";
              echo($backup);
              echo "</td><td>";
              echo("<a href='edit.php?profile_id=".$row['profile_id']."'>Edit</a>"." / ");
              echo("<a href='delete.php?profile_id=".$row['profile_id']."'>Delete</a>\n");
              echo "</td></tr>\n";
            }
          ?>
        </tbody>
      </table>
    </div>


  </div>
