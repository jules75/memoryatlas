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

      let container = $(`[data-entry-id="${data.entry_id}"]`);

      // get title from first paragraph
      let regex = /(.*?)[\r\n]/;
      var title;
      if (regex.test(data.ops[0].insert)) {
        title = data.ops[0].insert.match(regex)[1];
      }
      else {
        title = data.ops[0].insert;
      }
      $(container).children("a").children("span").text(title);
            
      // load first image from entry as background
      let imageOp = data.ops.filter(isImage)[0];
      if (imageOp !== undefined) {
        let imageUrl = imageOp.attributes.image;        
        $(container).children("a").children("img").attr('src', cloudinaryThumbnailUrl(imageUrl));
      }

    }

    function onEntryListData(data) {
      data.result.forEach(function(entry) {
        let item = $(`
          <li data-entry-id="${entry.entry_id}">
            <a href="/alpha/entry.php?entry_id=${entry.entry_id}">
            <img></img>
            <span>Loading&hellip;</span>          
            </a>
            </li>
            `);

        $('#entry_previews').append(item);
        
        let url = `/api.php?action=entry&entry_id=${entry.entry_id}`;
        $.getJSON(url, onEntryData);
      });
    }

    $.getJSON("/api.php?action=list", onEntryListData);

  </script>

<?php include_once('_bottom.php'); ?>
