/**
 * Functions to transform a Quill document (aka Delta) in various ways
 * Depends on underscore.js
 */


function dedupe(result, s) {
    return _.isEqual(s, _.last(result)) ? result : result.concat(s);
};

function dropLast(arr) {
    return arr.slice(0, -1)
};

let QuillDoc = {

    // reducing fn
    concatPreserveImageAttributes: function (result, op) {

        if (_.isEmpty(op.insert) || _.isEmpty(result)) {
            return result.concat(op);
        }

        let lastOp = _.last(result);
        var newOp = { insert: lastOp.insert + op.insert };

        if (lastOp.hasOwnProperty('attributes')) {
            if (lastOp.attributes.hasOwnProperty('image')) {
                newOp.attributes = { image: lastOp.attributes.image };
            }
        }
        else if (op.hasOwnProperty('attributes')) {
            if (op.attributes.hasOwnProperty('image')) {
                newOp.attributes = { image: op.attributes.image };
            }
        }

        return dropLast(result).concat(newOp);
    },

    // reducing fn
    splitByParagraph: function (result, quillOp) {

        let f = function (op) {
            if (quillOp.attributes) {
                return { insert: op, attributes: quillOp.attributes }
            }
            return { insert: op };
        }

        let newOps = quillOp.insert.split("\n");
        return result.concat(_.map(newOps, f));
    },

    hasYouTube: function (quillOp) {
        return quillOp.hasOwnProperty('attributes') && quillOp.attributes.hasOwnProperty('youtube');
    },

    // Returns document with ops grouped by paragraph (broken on newline).
    // If any of the original ops contained an image attribute, that attribute
    // is attached to the entire output op/paragraph.
    coalesceParagraphs: function (ops) {
        let a = _.reduce(ops, QuillDoc.splitByParagraph, []);
        let b = _.reduce(a, dedupe, []);
        return _.reduce(b, QuillDoc.concatPreserveImageAttributes, []);
    },

    // Returns ID of first YouTube found, otherwise null.
    firstYouTubeId: function (ops) {
        let op = _.find(ops, QuillDoc.hasYouTube);
        return op ? op.attributes.youtube : null;
    },


    //   Fix formatting of current document (by removing attributes)
    //   Updating the document triggers a save
    fixFormatting: function () {

        function stripAttributes(item) {

            if (item.hasOwnProperty('attributes') && item.attributes.hasOwnProperty('bold')) {
                let newItem = item;
                delete newItem.attributes.bold;
                return newItem;
            }

            return item;
        }

        let contents = quill.getContents();
        let newContents = { ops: contents.ops.map(stripAttributes) };
        quill.setContents(newContents);
    }

};
