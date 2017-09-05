
let Inline = Quill.import('blots/inline');

class YoutubeBlot extends Inline {

  static create(videoid) {
    let node = super.create();
    node.setAttribute('data-tagtype', 'youtube');
    node.setAttribute('data-videoid', videoid);
    node.setAttribute('title', `https://www.youtube.com/watch?v=${videoid}`);
    return node;
  }

  static formats(node) {
    return node.getAttribute('data-videoid');
  }

  static popupEditor(onOk) {
    var picker = new google.picker.PickerBuilder().
        addView(new google.picker.VideoSearchView().
        setSite(google.picker.VideoSearchView.YOUTUBE)).
        setCallback(onOk).
        build();
    picker.setVisible(true);
  }

  static popupShow(vid) {
    let url = `https://www.youtube.com/watch?v=${vid}`;
    window.open(url, '_blank');
  }

}

YoutubeBlot.blotName = 'youtube';
YoutubeBlot.tagName = 'span';
