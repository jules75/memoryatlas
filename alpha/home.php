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

    function randomItem(arr) {
      return arr[Math.floor(Math.random()*arr.length)];
    }

    // ask Cloudinary to generate smart thumbnail for us
    function cloudinaryThumbnailUrl(imageUrl) {
      return imageUrl.replace(new RegExp('upload/.*?/'), 'upload/w_200,h_300,c_thumb,g_auto/');
    }

    function onEntryData(data) {
      let imageOp = randomItem(data.ops.filter(isImage));
      let imageUrl = imageOp.attributes.image;
      let title = data.ops[0].insert;
      let item = $(`
        <li>
          <img></img>
          <a href="/alpha/entry.php?page_id=${data.page_id}">${title}</a>
          </li>
          `);
      $(item).children("img").attr('src', cloudinaryThumbnailUrl(imageUrl));
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
