function getWidth(){
    if ($(window).width() < 768 ){
        $('img').attr('class', 'img-responsive').width('100%');

    }else {
        $('img').removeAttr('class', 'img-responsive').width('');
        $('p').each(function () {
            var img = this.querySelectorAll('img');
            if (img.length === 1){
                img[0].setAttribute('class', 'img-responsive');
            }
        })
    }
}
getWidth();
$(window).resize(function () {
    getWidth();
});