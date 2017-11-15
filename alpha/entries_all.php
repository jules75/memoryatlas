<?php include_once '_top.php'; ?>

<?php require_once ($_SERVER['DOCUMENT_ROOT'] . '/lib/entry.php'); ?>
<?php require_once ($_SERVER['DOCUMENT_ROOT'] . '/db.php'); ?>

<link rel="stylesheet" href="/css/entries.css">
<link rel="stylesheet" href="/css/preview.css">

<?php

  $entries = get_entries();

?>

<ul id="entry_previews">
<?php 
  foreach($entries->result AS $entry) {
    echo entry_preview_html($entry->entry_id);
  }  
?>
</ul>
