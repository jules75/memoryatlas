
let Inline = Quill.import('blots/inline');

class LinkBlot extends Inline {

  static create(url) {
    let node = super.create();
    node.setAttribute('data-tagtype', 'link');
    node.setAttribute('data-url', url);
    node.setAttribute('title', url);
    return node;
  }

  static formats(node) {
    return node.getAttribute('data-url');
  }

  static popupEditor(onOk) {
    let url = prompt("Enter full URL (web address) you want to link to");
    onOk(url);
  }

  static popupShow(url) {
    window.open(url, '_blank');
  }

}

LinkBlot.blotName = 'link';
LinkBlot.tagName = 'span';
