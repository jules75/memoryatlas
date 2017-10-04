<?php include_once '_top.php'; ?>

<?php require_once ($_SERVER['DOCUMENT_ROOT'] . '/lib/entry.php'); ?>

<link rel="stylesheet" href="/css/entries.css">
<link rel="stylesheet" href="/css/preview.css">

<?php

  $user_arr = json_decode(file_get_contents("../cache/users.json"));
  $user_id = $_SESSION['user']['id'];
  $created_entry_ids = $user_arr->$user_id->created;
  $contributed_entry_ids = $user_arr->$user_id->contributed;

?>

<h3>Created by <span class="username"><?php echo $_SESSION['user']['username'] ?></span></h3>

<ul id="entry_previews">
<?php 
  foreach($created_entry_ids AS $id) {
    echo entry_preview_html($id);
  }  
?>
</ul>


<h3>Contributions by <span class="username"><?php echo $_SESSION['user']['username'] ?></span></h3>

<ul id="entry_previews">
<?php 
  foreach($contributed_entry_ids AS $id) {
    echo entry_preview_html($id);
  }  
?>
</ul>

