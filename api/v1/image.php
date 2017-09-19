<?php

require_once '../../config.php';
require_once '../../db.php';
require_once '../../api/fns.php';


// setup Cloudinary cloud image host
require_once '../../lib/3rdparty/cloudinary/Cloudinary.php';
require_once '../../lib/3rdparty/cloudinary/Uploader.php';
require_once '../../lib/3rdparty/cloudinary/Api.php';
\Cloudinary::config(MEMORY_ATLAS_CONFIG['cloudinary']);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = \Cloudinary\Uploader::upload($_FILES['upload']['tmp_name']);
    succeed(['image_url'=>$result['secure_url']]);
}
