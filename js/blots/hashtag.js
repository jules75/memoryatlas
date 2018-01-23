
let Inline = Quill.import('blots/inline');

class HashtagBlot extends Inline {

  static create(text) {
    let node = super.create();
    node.setAttribute('data-tagtype', 'hashtag');
    node.setAttribute('data-hashtag', text);
    return node;
  }

  static formats(node) {
    return node.getAttribute('data-hashtag');
  }

  // static popupEditor(onOk) {
  //   onOk("popupEditor not implemented");
  // }

  static popupShow(hashtag) {
    document.location.href = `/home.php?hashtag=${hashtag}`;
  }

}

HashtagBlot.blotName = 'hashtag';
HashtagBlot.tagName = 'span';
