<?php include_once '_top.php'; ?>

<form>
<input id="entry_id" placeholder="entry id" />
<div id="editor-container"></div>
<button>Submit</button>
</form>

<script src="//cdn.quilljs.com/1.3.0/quill.min.js"></script>

<script>

let quill = new Quill('#editor-container');

function onSaveSuccess(e) {
  console.log('success', e);
}

function sendForm(e) {
  
  let entry_id = $('#entry_id').val();
  let doc = JSON.stringify(quill.getContents());

  $.post('/api/v1/comment.php',
      {
        id: entry_id,
        content: doc
      },
      onSaveSuccess
    ).fail(function (data) {
      alert(data.responseText);
    });
  
  e.preventDefault();
}

$('button').click(sendForm);

</script>

<?php include_once('_bottom.php'); ?>