$('.frontShow').each(function (index) {
        var img = this.querySelector('img');
        if (img) {
            img = img.getAttribute('src');
            $('#image' + (index + 1)).attr('src', img);
        }
    }
);
$('.content img').remove();
$('.frontShow p').each(function () {
    if (this.firstChild && this.firstChild.length > 1) {
        if (this.firstChild.length > 300) {
            var newtext = document.createTextNode(this.innerText.slice(0, 230) + '...');
            var newp = document.createElement('p').appendChild(newtext);
            this.firstChild.remove();
            this.appendChild(newp);
        }
    } else {
        this.remove();
    }
});
$('.frontShow').each(function () {
    var paragraphs = this.querySelectorAll('p');
    for (var i = 0; i < paragraphs.length; i++) {
        if (i > 0) {
            paragraphs[i].remove();
        }
    }
});
