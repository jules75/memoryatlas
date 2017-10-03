<?php include_once '_top.php'; ?>

<link rel="stylesheet" href="/css/entries.css">

<?php

  $user_arr = json_decode(file_get_contents("../cache/users.json"));
  $user_id = $_SESSION['user']['id'];
  $created_entry_ids = $user_arr->$user_id->created;
  $contributed_entry_ids = $user_arr->$user_id->contributed;

?>

<h3>Created by me</h3>

<ul>
<?php foreach($created_entry_ids AS $id): ?>
<li><a href="/alpha/entry.php?entry_id=<?php echo $id ?>"><?php echo $id ?></a></li>
<?php endforeach; ?>
</ul>


<h3>Contributed to by me</h3>

<ul>
<?php foreach($contributed_entry_ids AS $id): ?>
<li><a href="/alpha/entry.php?entry_id=<?php echo $id ?>"><?php echo $id ?></a></li>
<?php endforeach; ?>
</ul>

