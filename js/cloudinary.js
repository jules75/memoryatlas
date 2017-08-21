
// Ask Cloudinary to generate special images URLs

function cloudinaryThumbnailUrl(imageUrl) {
    return imageUrl.replace(new RegExp('upload/.*?/'), 'upload/w_200,h_300,c_thumb,g_auto/');
}

function cloudinaryGrayscaleUrl(imageUrl) {
    return imageUrl.replace(new RegExp('upload/.*?/'), 'upload/e_grayscale/');
}
