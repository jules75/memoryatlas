<?php include_once '_top.php'; ?>

<?php require_once('../db.php'); ?>

  <noscript>
  <ul>
    <?php foreach (get_entries()->result as $entry) : ?>
  <li><a href="/alpha/entry.php?entry_id=<?php echo $entry->entry_id; ?>">Link to entry</a></li>
    <?php endforeach; ?>
  </ul>
  </noscript>

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

    function hasNoContent(entryData) {
      return (entryData.ops[0].insert.length == 1);
    }

    function onEntryData(data) {

      let container = $(`[data-entry-id="${data.entry_id}"]`);

      if (hasNoContent(data)) {
        $(container).hide();
      }

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
