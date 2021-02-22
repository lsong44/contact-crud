<?php
require_once "pdo.php";
require_once "header.php";
require_once "util.php";
session_start();

if(!isset($_SESSION['user_email'])){
  die("Please log in.");
}

if(isset($_POST['cancel'])){
  header("Location: view.php?user=".$_SESSION['username']);
  return;
}

if(isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['state']) &&
isset($_POST['title']) && isset($_POST['transferred']) && isset($_POST['source']))
{
  if (!$_POST['first_name'] || !$_POST['last_name'] || !$_POST['title']
  || !$_POST['source'] || !$_POST['state'] || $_POST['transferred'] ==""){
    $_SESSION['error'] = "Please fill in all required fields.";
    header("Location: add.php?user=".$_SESSION['username']);
    return;
  }elseif(validateAddress() !== true){
    $_SESSION['error'] = validateAddress();
    header("Location: add.php");
    return;
  }elseif(validateEmail() !== true){
    $_SESSION['error'] = validateEmail();
    header("Location: add.php");
    return;
  }elseif(validatePhone() !== true){
    $_SESSION['error'] = validatePhone();
    header("Location: add.php");
    return;
  }
  else{
    //get state_id
    $sql = "SELECT * FROM state WHERE state_name = :state";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':state' => $_POST['state']));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!$row){
      $_SESSION['error'] = "Invalid state.";
      header("Location: add.php");
      return;
    }
    $state_id = $row['state_id'];

    //insert into source
    $sql = "SELECT * FROM source WHERE source_name = :src";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
      ':src' => $_POST['source']
    ));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!$row){
      $sql = "INSERT INTO source (source_name) VALUES (:srcname)";
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array(':srcname' => $_POST['source']));
      $src_id = $pdo->lastInsertId();
    }else{
      $src_id = $row['source_id'];
    }

    //insert into profile
    $sql= "INSERT INTO Profile (first_name, last_name, title, added_time, edited_time, user_id, transferred_backup, source_id, state_id)
    VALUES (:first_name, :last_name, :title, :added_time, :added_time, :creator, :transferred, :src_id, :st_id);";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
      ':first_name' => $_POST['first_name'],
      ':last_name' => $_POST['last_name'],
      ':title' => $_POST['title'],
      ':added_time' => date("m-d-Y H:i:s"),
      ':creator' => $_SESSION['user_id'],
      ':transferred' => $_POST['transferred'],
      ':src_id' => $src_id,
      ':st_id' => $state_id
    ));
    $pf_id = $pdo->lastInsertId();

    //insert into email
    for($i=1; $i<=10; $i++){
      if(!isset($_POST['email'.$i])) continue;
      $sql = "INSERT INTO Email (email ,profile_id)
              VALUES (:email, :pf_id)";
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array(
        ':email' => $_POST['email'.$i],
        ':pf_id' => $pf_id
      ));
    }

    //insert into phone
    for($i=1; $i<=10; $i++){
      if(!isset($_POST['phone'.$i])) continue;
      $sql = "INSERT INTO Phone (phone ,profile_id)
              VALUES (:phone, :pf_id)";
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array(
        ':phone' => $_POST['phone'.$i],
        ':pf_id' => $pf_id
      ));
    }

    //insert into address
    for($i=1; $i<=10; $i++){
      if(!isset($_POST['city'.$i])) continue;

      $sql = "INSERT INTO Address (street, city, state_id, zip_code, profile_id)
              VALUES (:street, :city, :state_id, :zip, :pf_id)";
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array(
        ':street' => $_POST['street'.$i],
        ':city' => $_POST['city'.$i],
        ':zip' => $_POST['zip_code'.$i],
        ':pf_id' => $pf_id,
        ':state_id' => $state_id
      ));
    }


    $_SESSION['success'] = "New record added.";
    header("Location: view.php?user=".$_SESSION['username']);
    return;
  }
}

?>
<html>
<head>
  <title>Add a new profile</title>
</head>
<body style="font-family: sans-serif;">
  <div class="container">
    <div class="row" style="margin-top: 50px">
      <div calss="page-header">
        <h1>Add a new Record</h1>
      </div>
    </div>
    <div class="row" style="margin-top: 50px">
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
            <label for="first_name">First Name</label>
            <input type="text" name = "first_name" class="form-control" id="first_name" required>
          </div>
          <div class="form-group">
            <label for="last_name">Last Name</label>
            <input type="text" name = "last_name" class="form-control" id="last_name" required>
          </div>
          <div class="form-group">
            <label for="title">Title</label>
            <input type="text" name = "title" class="form-control" id="title" required>
          </div>
          <div class="form-group">
            <label for="state">State</label>
            <input type="text" name = "state" class="form-control state ui-autocomplete-input"
              autocomplete="off" id="state" placeholder="Please type and select from the dropdown list" required>
          </div>
          <div class="form-group row">
            <label for="add-address" class="col-sm-9 col-form-label">Add Address</label>
            <div class="col-sm-3">
              <input type="submit" class="btn btn-primary btn-sm" value="+" id="add-address" formnovalidate/>
            </div>
          </div>
          <div id="address-fields">
          </div>
          <div class="form-group row">
            <label for="add-email" class="col-sm-9 col-form-label">Email</label>
            <div class="col-sm-3">
              <input type="submit" class="btn btn-primary btn-sm" value="+" id="add-email" formnovalidate/>
            </div>
          </div>
          <div id="email-fields">
          </div>
          <div class="form-group row">
            <label for="add-phone" class="col-sm-9 col-form-label">Phone</label>
            <div class="col-sm-3">
              <input type="submit" class="btn btn-primary btn-sm" value="+" id="add-phone" formnovalidate/>
            </div>
          </div>
          <div id="phone-fields">
          </div>

          <div class="form-group">
            <label for="transferred">Transferred to the backup list?</label>
            <select class="form-control" id="transferred" name="transferred" required>
              <option value="">Please select</option>
              <option value=1>Yes</option>
              <option value=0>No</option>
            </select>
          </div>
          <div class="form-group">
            <label for="source">Source</label>
            <input type="text" name = "source" class="form-control source ui-autocomplete-input"
              autocomplete="off" id="source">
          </div>
          <div class="form-group">
            <button type="submit" class="btn btn-primary" name="save">Save</button>
            <input type="submit" class="btn btn-secondary" name="cancel" value="Cancel" formnovalidate>
          </div>
        </fieldset>
      </form>
    </div>
  </div>

<script type="text/javascript">
  $(document).ready(function(){
    countAdd = 0;
    $('#add-address').click(function(event){
      event.preventDefault();
      if(countAdd > 9){
        alert("You've reached the maximum of address.");
        return;
      }
      countAdd++;
      $('#address-fields').append(
        '<div id="address' + countAdd +'" name="address '+ countAdd + '">\
        <div class="form-group">\
          <div class="form-group row">\
            <label class="col-sm-9 col-form-label" for="street'+ countAdd + '">\
            Street Address ' + countAdd + '</label>\
            <div class="col-sm-3">\
              <input type="submit" class="btn btn-primary btn-sm" value="-" \
              onclick = "$(\'#address' + countAdd + '\').remove(); return false;" formnovalidate/>\
            </div>\
          </div>\
          <input type="text" name = "street' + countAdd + '" class="form-control" \
          id="street' + countAdd +'">\
        </div>\
        <div class="form-group">\
          <label for="city' + countAdd + '">City</label>\
          <input type="text" name = "city' + countAdd + '" class="form-control" \
          id="city' + countAdd + '">\
        </div>\
        <div class="form-group">\
          <label for="zip_code' + countAdd + '">Zip Code</label>\
          <input type="text" name = "zip_code' + countAdd + '" class="form-control" \
          id="zip_code' + countAdd + '">\
        </div>\
        </div>'
      );
    })

    countEmail = 0;
    $('#add-email').click(function(event){
      event.preventDefault();
      if(countEmail > 9){
        alert("You've reached the maximum of email.");
        return;
      }
      countEmail++;
      $('#email-fields').append(
        '<div id="email' + countEmail +'">\
        <div class="form-group">\
          <div class="form-group row">\
            <label class="col-sm-9 col-form-label" for="email'+ countEmail + '">\
            Email ' + countEmail + '</label>\
            <div class="col-sm-3">\
              <input type="submit" class="btn btn-primary btn-sm" value="-" \
              onclick = "$(\'#email' + countEmail + '\').remove(); return false;" formnovalidate/>\
            </div>\
          </div>\
          <input type="text" name = "email' + countEmail + '" class="form-control" \
          id="email' + countEmail +'" placeholder="xxx@xxx.xxx">\
        </div>\
        </div>'
      );
    })

    countPhone = 0;
    $('#add-phone').click(function(event){
      event.preventDefault();
      if(countPhone > 9){
        alert("You've reached the maximum of phone number.");
        return;
      }
      countPhone++;
      $('#phone-fields').append(
        '<div id="phone' + countPhone +'">\
        <div class="form-group">\
          <div class="form-group row">\
            <label class="col-sm-9 col-form-label" for="phone'+ countPhone + '">\
            Phone Number ' + countPhone + '</label>\
            <div class="col-sm-3">\
              <input type="submit" class="btn btn-primary btn-sm" value="-" \
              onclick = "$(\'#phone' + countPhone + '\').remove(); return false;" formnovalidate/>\
            </div>\
          </div>\
          <input type="text" name = "phone' + countPhone + '" class="form-control" \
          id="phone' + countPhone +'" placeholder="(000) 000-0000">\
        </div>\
        </div>'
      );
    })

    $('.state').autocomplete({source:"state_auto.php"});
    $('.source').autocomplete({source:"source_auto.php"});
  })
</script>

</body>
