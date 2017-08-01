
let Inline = Quill.import('blots/inline');

class YoutubeBlot extends Inline {

  static create(videoid) {
    let node = super.create();
    node.setAttribute('data-tagtype', 'youtube');
    node.setAttribute('data-videoid', videoid);
    return node;
  }

  static formats(node) {
    return node.getAttribute('data-videoid');
  }

  static popupEditor(onOk) {
    let vid = prompt("Enter Youtube video id, e.g. RLoHcB8A63M");
    onOk(vid);
  }

  static popupShow(vid) {
    let url = `https://www.youtube.com/watch?v=${vid}`;
    window.open(url, '_blank');
  }

}

YoutubeBlot.blotName = 'youtube';
YoutubeBlot.tagName = 'span';
