var loadImages = {
    loadAll: function (){
        setTimeout(function(){
            let find_attr = 'data-squeezeimg-src';
            let lazt_loads = document.querySelectorAll('img['+find_attr+']');
            for(let lazy_item of lazt_loads){
                lazy_item.src = lazy_item.getAttribute(find_attr);
                lazy_item.removeAttribute(find_attr);
            }
            for(let lazy_item of lazt_loads){
                lazy_item.classList.remove('lazyload');
                lazy_item.classList.add('lazyloaded');
            }

        },2000);
    }
}
window.addEventListener('load', function () {
    loadImages.loadAll();
    document.body.addEventListener('DOMSubtreeModified', function () {loadImages.loadAll();});
})
