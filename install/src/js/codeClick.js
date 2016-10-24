document.addEventListener('DOMContentLoaded', codeClick);

function codeClick(e) {
    var codeBlocks = document.querySelectorAll('code');

    for (var codeBlock of codeBlocks) {
        codeBlock.addEventListener('click', function(e) {
            var range = document.createRange();
            range.selectNode(this);
            window.getSelection().addRange(range);
        });
    }
}
