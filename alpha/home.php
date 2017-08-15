<?php include_once '_top.php'; ?>

  <ul id="entry_previews"></ul>

  <script>

    function isImage(quillOp) {
      if (quillOp.hasOwnProperty('attributes')) {
        if (quillOp.attributes.hasOwnProperty('image')) {
          return true;
        }
      }
      return false;
    }

    function onEntryData(data) {
      let imageUrl = data.ops.filter(isImage)[0].attributes.image;
      let title = data.ops[0].insert;
      let item = $(`
        <li>
          <img></img>
          <a href="/alpha/entry.php?page_id=${data.page_id}">${title}</a>
          </li>
          `);
      $(item).children("img").attr('src', imageUrl);
      $('#entry_previews').append(item);
    }

    function onEntryListData(data) {
      data.result.forEach(function(entry) {
        let url = `/api.php?action=page&page_id=${entry.page_id}`;
        $.getJSON(url, onEntryData);
      });
    }

    $.getJSON("/api.php?action=list", onEntryListData);

  </script>

<?php include_once('_bottom.php'); ?>
