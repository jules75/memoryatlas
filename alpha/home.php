<?php include_once '_top.php'; ?>


  <ul id="page_previews">

  </ul>

  <script>

    function onPageData(data) {
      let title = data.ops[0].insert;
      let item = $(`<li><a href="/alpha/entry.php?page_id=${data.page_id}">${title}</a></li>`);
      $('#page_previews').append(item);
    }

    function onPageListData(data) {
      
      data.result.forEach(function(entry) {
        let url = `/api.php?action=page&page_id=${entry.page_id}`;
        $.getJSON(url, onPageData);
      });

    }

    $.getJSON("/api.php?action=list", onPageListData);

  </script>

<?php include_once('_bottom.php'); ?>
