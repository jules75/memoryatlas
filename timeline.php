<?php include_once '_top.php'; ?>

  <link rel="stylesheet" href="/css/timeline.css">

<?php

  // returns friendly string, mm and/or dd may be 00
  function friendlyDate($yyyymmdd) {

    $months = ["", "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

    $year = intval(substr($yyyymmdd, 0, 4));
    $month_num = intval(substr($yyyymmdd, 4, 2));
    $month = $months[$month_num];
    $day = intval(substr($yyyymmdd, 6, 2));

    if ($day) {
      return "$day $month $year";
    }

    if ($month) {
      return "$month $year";
    }

    return $year;
  }

  $date_arr = json_decode(file_get_contents("../cache/dates.json"));

?>

<?php foreach($date_arr AS $date=>$entries): ?>

  <h3><?php echo friendlyDate($date); ?> <i class="fa fa-calendar" aria-hidden="true"></i></h3>

  <?php foreach($entries AS $entry_data): ?>
  <p><a href="entry.php?entry_id=<?php echo $entry_data->entry_id ?>"><?php echo $entry_data->title ?></a></p>
  <?php endforeach; ?>

<?php endforeach; ?>

<?php include_once('_bottom.php'); ?>