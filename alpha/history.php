<?php include_once '_top.php'; ?>

<?php require_once ($_SERVER['DOCUMENT_ROOT'] . '/db.php'); ?>

<?php

// avoid thrashing database
function memoized_get_user_by_id($id) {

  static $users = []; // id as key

  if (!isset($users[$id])) {
    $users[$id] = get_user_by_id($id);
  }

  return $users[$id];
}

?>

<table>

<thead>
  <tr>
  <td>Updated on</td>
  <td>By user</td>
  <td></td>
  </tr>
  </thead>

<?php foreach(get_entry_history($_GET['entry_id']) AS $entry): ?>

<tr>
<?php $url = "entry.php?entry_id=" . $_GET['entry_id'] . "&revision_id=" . $entry->_id; ?>
<td><a href="<?php echo $url ?>" rel="nofollow"><?php echo date('Y-m-d H:i:s', mongoIdToTimestamp($entry->_id)); ?></a></td>
<?php $user = memoized_get_user_by_id($entry->user->id); ?>
<td><?php if (isset($user->username)) echo $user->username;  ?></td>
</tr>

<?php endforeach; ?>

</table>


<?php include_once('_bottom.php'); ?>