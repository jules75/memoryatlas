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

    // from https://css-tricks.com/snippets/javascript/get-url-variables/
    function getQueryVariable(variable){
          var query = window.location.search.substring(1);
          var vars = query.split("&");
          for (var i=0;i<vars.length;i++) {
                  var pair = vars[i].split("=");
                  if(pair[0] == variable){return pair[1];}
          }
          return(false);
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

    // only show entries with matching hashtag
    let hashtag = getQueryVariable('hashtag');
    if (hashtag) {
      $('#content').prepend($(`<p>#${hashtag}</p>`));
      $.getJSON("/cache/hashtags.json", function(result) {
        onEntryListData(result['#' + hashtag]);
      });
    } 

    // show all entries
    else {
      $.getJSON("/api.php?action=list", onEntryListData);
    }

  </script>

<?php include_once('_bottom.php'); ?>
