<?php

session_start();

// redirect if not logged in
if (!isset($_SESSION['user'])) {
	header('Location: /login.php');
}


require_once ($_SERVER['DOCUMENT_ROOT'] . '/config.php');


// setup Cloudinary cloud image host
require_once ($_SERVER['DOCUMENT_ROOT'] . '/lib/3rdparty/cloudinary/Cloudinary.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/lib/3rdparty/cloudinary/Uploader.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/lib/3rdparty/cloudinary/Api.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/lib/3rdparty/cloudinary/Helpers.php');
\Cloudinary::config(MEMORY_ATLAS_CONFIG['cloudinary']);


?>

<style>

body {
    font-family: sans-serif;
}

code {
    font-family: monospace;
    white-space: pre-wrap;
    background-color: #eef;
    padding: 10px;
    line-height: 2;
    display: inline-block;
}

</style>

<script src="//cdn.quilljs.com/1.3.0/quill.min.js"></script>
<script src='//cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js'></script>


<form method="POST" enctype="multipart/form-data">
<?php echo cl_image_upload_tag("image_id", 
    array("html" => array("name"=> "upload[]", "multiple" => TRUE ))); ?>
<input type="submit" value="Upload images" />
</form>


<p>Copy the code below</p>
<code>
</code>


<script>


var Delta = Quill.import('delta');


let fileList = {};
let uploadResponses = [];


// is there a response for each file?
function areUploadsComplete() {
    return (fileList.length > 0 && (fileList.length == uploadResponses.length));
}


function generate_token() {
    // return sha1(mt_rand().mt_rand().mt_rand());  // PHP version

    // 10 random hex chars
    function f() {
        return Math.random().toString(16).substring(2, 12);
    }

    return f() + f() + f() + f();
}


// only call when areUploadsComplete() is true
function buildQuillDocument() {

    function r(acc, val) {
        let item0 = { insert: "Text about " };
        let item1 = {
            insert: "this image ",
            attributes: { image: val.image_url }
        };
        let item2 = { insert: "goes here " };
        let item3 = { insert: "\r\n\r\n" };
        acc.push(item0);
        acc.push(item1);
        acc.push(item2);
        acc.push(item3);
        return acc;
    }

    let contents = uploadResponses.reduce(r, []);

    let code = `var Delta = Quill.import('delta');
var delta = new Delta(${JSON.stringify(contents)});
var doc = quill.getContents();
quill.setContents(doc.concat(delta));`;

    $('code').text(code);
    // console.log(JSON.stringify(code,null,2));

    // contents = JSON.parse(JSON.stringify(contents));  // breaks if you don't do this
    // contents.entry_id = generate_token();
    // $.post('/api/v1/entry.php',
    //     {
    //         action: 'save',
    //         payload: contents
    //     },
    //     function (data) {
    //         let url = `entry.php?entry_id=${contents.entry_id}`;
    //         console.log(`Entry created at ${document.location.origin}/${url}`);
    //     }
    // ).fail(function (data) {
    //     console.error(`Could not create entry: ${data.responseText}`);
    // });

}


function uploadImage(file) {

    let formData = new FormData();
    formData.append('action', 'image');
    formData.append('upload', file);

    $.ajax({
        url: '/api/v1/image.php',
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        type: 'POST',
        success: function (response) {
            console.log(`Uploaded file ${file.name} OK`);
            uploadResponses.push(response);
            if (areUploadsComplete()) {
                console.log("All images uploaded successfully!");
                buildQuillDocument();
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.error(`Failed to upload ${file.name}: ${errorThrown}`);
    });

}


function onSubmit(e) {
    $("input").attr("disabled", true);
    for (var i = 0; i < fileList.length; i++) {
        uploadImage(fileList.item(i));
    }
    e.preventDefault();
}


function onFileSelectionChange(e) {
    fileList = $('.cloudinary-fileupload')[0].files;
}


$('.cloudinary-fileupload').change(onFileSelectionChange);
$('form').on('submit', onSubmit);


</script>

