
let Inline = Quill.import('blots/inline');

class ImageBlot extends Inline {

  static create(url) {
    let node = super.create();
    node.setAttribute('data-tagtype', 'image');
    node.setAttribute('data-url', url);
    return node;
  }

  static formats(node) {
    return node.getAttribute('data-url');
  }

  static popupEditor(onOk) {

    createOverlay();    

    function setUploadStatus(msg) {
      $("#imageUploaderStatus").text(msg);
    }

    function onImageUploadSuccess(cloudinaryResponse) {
        destroyOverlay();
        $('#imageUploader').remove();
        onOk(cloudinaryResponse.image_url);
    };

    function uploadImage(e) {
      
        var data = new FormData();
        data.append('action', 'image');
        data.append('upload', $('#imageUploaderInput')[0].files[0]);

        setUploadStatus("Uploading...");
        
        $.ajax({
            url: '/api.php',
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            type: 'POST',
            success: onImageUploadSuccess
        });
    }

    var container = $(`
      <div id="imageUploader" class="modal">
        <p id="imageUploaderStatus">Loading...</p>
        <input type="file" name="upload" id="imageUploaderInput"></input>
        <button>Cancel</button>
        </div>
      `);

    // create upload box
    $('body').append(container);
    setUploadStatus("Drag and drop your image file below");

    // listen for file selection
    $('#imageUploaderInput').change(uploadImage);

    // close on any button
    $('#imageUploader button').click(function (e) {
      $('#imageUploader').remove();
      destroyOverlay();
    });
  }

  static popupShow(url) {
    window.open(url, '_blank');
  }

  static onHover(e) {

    // highlight any loaded image with matching url
    let url = e.target.dataset.url;
    let selector = `img[src="${url}"]`;
    $(selector).toggleClass('highlight');
  }

}

ImageBlot.blotName = 'image';
ImageBlot.tagName = 'span';
