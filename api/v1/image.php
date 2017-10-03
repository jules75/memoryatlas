<?php

require_once ($_SERVER['DOCUMENT_ROOT'] . '/config.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/db.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/api/fns.php');


// setup Cloudinary cloud image host
require_once ($_SERVER['DOCUMENT_ROOT'] . '/lib/3rdparty/cloudinary/Cloudinary.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/lib/3rdparty/cloudinary/Uploader.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/lib/3rdparty/cloudinary/Api.php');
\Cloudinary::config(MEMORY_ATLAS_CONFIG['cloudinary']);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = \Cloudinary\Uploader::upload($_FILES['upload']['tmp_name']);
    succeed(['image_url'=>$result['secure_url']]);
}
