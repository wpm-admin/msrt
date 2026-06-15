var gsap = window.gsap;
var ScrollTrigger = window.ScrollTrigger;
var ScrollSmoother = window.ScrollSmoother;

function decodeEntities(str) {
  var txt = document.createElement('textarea');
  txt.innerHTML = str;
  return txt.value;
}

$(function () {
  // Init sliders via App bundle
  App.initSwiper('.hero-slider', {
    speed: 1000,
    effect: 'fade',
    pagination: { el: '.hero-pagination', clickable: true }
  });
  var categoriesSlider = document.querySelector('.categories__slider');
  var originalSlideIds = [];
  if (categoriesSlider && categoriesSlider.dataset.markIds) {
    try {
      originalSlideIds = JSON.parse(categoriesSlider.dataset.markIds);
    } catch (e) {
      categoriesSlider.querySelectorAll('.swiper-slide').forEach(function (slide) {
        originalSlideIds.push(slide.getAttribute('data-mark-id'));
      });
    }
  }
  App.initSwiper('.categories__slider', {
    loop: true,
    speed: 1000,
    slidesPerView: 3,
    centeredSlides: true,
    navigation: {
      nextEl: '.categories__slider-next',
      prevEl: '.categories__slider-prev'
    }
  });
  App.initResponsiveSwiper('.categories__bottom', 980, {
    loop: true,
    slidesPerView: 2,
    spaceBetween: 20,
    pagination: { el: '.categories__list-pagination', clickable: true },
    breakpoints: { 580: { slidesPerView: 3, spaceBetween: 30 } }
  });
  App.initResponsiveSwiper('.news__slider', 980, {
    loop: true,
    slidesPerView: 1.2,
    spaceBetween: 20,
    breakpoints: {
      480: { slidesPerView: 2 },
      680: { slidesPerView: 3, spaceBetween: 30 }
    }
  });
  App.initResponsiveSwiper('.club__slider', 800, {
    loop: true,
    slidesPerView: 1.2,
    spaceBetween: 20,
  });
  App.initResponsiveSwiper('.auctions__slider', 800, {
    loop: true,
    slidesPerView: 1.2,
    spaceBetween: 20,
  });

  // Promo code copy
  document.querySelectorAll('.promo-code-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var code = this.dataset.promo;
      if (!code) return;
      var tooltip = this.querySelector('.tooltip');
      navigator.clipboard.writeText(code).then(function () {
        if (tooltip) tooltip.textContent = 'Copied!';
        btn.classList.add('copied');
        setTimeout(function () {
          if (tooltip) tooltip.textContent = 'Click to copy';
          btn.classList.remove('copied');
        }, 2000);
      }).catch(function () {
        if (tooltip) tooltip.textContent = 'Failed to copy';
      });
    });
  });

  var lastRequestedMarkId = null;

  function rebuildSelect(container, items, labelText) {
    var native = container.querySelector('.c-select__native');
    var dropdown = container.querySelector('.c-select__dropdown');
    var label = container.querySelector('.c-select__label');
    native.innerHTML = '';
    dropdown.innerHTML = '';
    var def = document.createElement('option');
    def.value = '';
    def.text = labelText;
    native.appendChild(def);
    items.forEach(function (item) {
      var nopt = document.createElement('option');
      nopt.value = item.value || item.model_id || item.category_id || '';
      nopt.textContent = decodeEntities(item.name);
      if (item.href) nopt.setAttribute('data-href', item.href);
      native.appendChild(nopt);
      var opt = document.createElement('div');
      opt.className = 'c-select__option';
      opt.dataset.value = item.value || item.model_id || item.category_id || '';
      if (item.href) opt.dataset.href = item.href;
      if (item.image) {
        var img = document.createElement('img');
        img.src = item.image;
        img.alt = '';
        opt.appendChild(img);
      }
      var span = document.createElement('span');
      span.textContent = decodeEntities(item.name);
      opt.appendChild(span);
      dropdown.appendChild(opt);
    });
    if (items.length > 0) {
      label.innerHTML = dropdown.querySelector('.c-select__option').innerHTML;
      native.value = items[0].value || items[0].model_id || items[0].category_id || '';
      dropdown.querySelector('.c-select__option').classList.add('is-active');
    } else {
      label.textContent = labelText;
    }
  }

  function setupDelegatedClicks() {
    document.querySelectorAll('.c-select__dropdown').forEach(function (dd) {
      dd.onclick = function (e) {
        var opt = e.target.closest('.c-select__option');
        if (!opt) return;
        var select = this.closest('.c-select');
        var label = select.querySelector('.c-select__label');
        var native = select.querySelector('.c-select__native');
        var dropdown = select.querySelector('.c-select__dropdown');
        var value = opt.dataset.value;
        var href = opt.dataset.href;

        // Only redirect for category select
        if (href && select.id === 'lm-category-select') {
          window.location.href = href;
          return;
        }

        // For model select (and others): just update selection, trigger change event
        label.innerHTML = opt.innerHTML;
        native.value = value;
        select.querySelectorAll('.c-select__option').forEach(function (o) {
          o.classList.remove('is-active');
        });
        opt.classList.add('is-active');
        select.classList.remove('is-open');
        gsap.to(dropdown, {
          duration: 0.3,
          opacity: 0,
          y: -10,
          autoAlpha: 0,
          ease: 'power2.in'
        });
        var evt = new Event('change', { bubbles: true });
        native.dispatchEvent(evt);
      };
    });
  }

  function loadModels(markId) {
    lastRequestedMarkId = markId;
    $.getJSON('index.php?route=extension/module/lm_category_section/models&mark_id=' + markId, function (data) {
      if (lastRequestedMarkId !== markId) return;
      var container = document.querySelector('#lm-model-select');
      rebuildSelect(container, data, $('.select__models .color-grey').text());
      setupDelegatedClicks();
      if (data.length > 0) loadCategories(data[0].model_id);
      ScrollTrigger.refresh();
    });
  }

  function loadCategories(modelId) {
    $.getJSON('index.php?route=extension/module/lm_category_section/categories&model_id=' + modelId, function (data) {
      var container = document.querySelector('#lm-category-select');
      rebuildSelect(container, data, $('#lm-category-select').closest('.select__category').find('.color-grey').text());
      setupDelegatedClicks();
    });
    loadCategoryBottom(modelId);
  }

  function loadCategoryBottom(modelId) {
    $.getJSON('index.php?route=extension/module/lm_category_section/categoryBottom&model_id=' + modelId, function (data) {
      var wrapper = document.querySelector('.categories__bottom .swiper-wrapper');
      if (!wrapper) return;
      wrapper.innerHTML = '';
      data.forEach(function (item) {
        var slide = document.createElement('div');
        slide.className = 'swiper-slide categories__list-item';
        var a = document.createElement('a');
        a.href = item.href;
        a.className = 'category_link';
        var div = document.createElement('div');
        div.className = 'category-image';
        var pic = document.createElement('picture');
        var img = document.createElement('img');
        img.loading = 'lazy';
        img.src = item.thumb;
        img.className = 'image';
        img.alt = decodeEntities(item.name);
        pic.appendChild(img);
        div.appendChild(pic);
        a.appendChild(div);
        var title = document.createElement('h3');
        title.className = 'item-title';
        title.textContent = decodeEntities(item.name);
        a.appendChild(title);
        slide.appendChild(a);
        wrapper.appendChild(slide);
      });
      var swiperEl = document.querySelector('.categories__bottom');
      if (swiperEl && swiperEl.swiper) swiperEl.swiper.update();
      ScrollTrigger.refresh();
    });
  }

  var swiperEl = categoriesSlider;
  if (swiperEl && originalSlideIds.length) {
    var check = setInterval(function () {
      if (swiperEl.swiper) {
        clearInterval(check);
        swiperEl.swiper.on('slideChange', function () {
          var markId = originalSlideIds[this.realIndex];
          if (markId) loadModels(markId);
        });
        var firstMarkId = originalSlideIds[swiperEl.swiper.realIndex || 0];
        if (firstMarkId) loadModels(firstMarkId);
      }
    }, 200);
  }

  $(document).on('change', '#lm-model-select .c-select__native', function () {
    var modelId = this.value;
    if (modelId) loadCategories(modelId);
  });

  $(document).on('change', '#lm-category-select .c-select__native', function () {
    var opt = this.options[this.selectedIndex];
    if (opt && opt.dataset.href) {
      window.location.href = opt.dataset.href;
    }
  });
});

// Allow native scroll in dropdowns / live-search (fix ScrollSmoother conflict)
document.addEventListener('wheel', function (e) {
  var el = e.target.closest('.c-select__dropdown, .live-search, .m-select__body');
  if (!el) return;
  var goingUp = e.deltaY < 0;
  var goingDown = e.deltaY > 0;
  var atTop = el.scrollTop <= 0;
  var atBottom = el.scrollHeight - el.scrollTop - el.clientHeight <= 1;
  if ((goingUp && atTop) || (goingDown && atBottom)) return;
  e.stopPropagation();
}, { passive: true, capture: true });

// Init smooth scroll + fade-in after all images loaded
$(window).on('load', function () {
  ScrollSmoother.create({
    wrapper: '#smooth-wrapper',
    content: '#smooth-content',
    smooth: 0.8,
    effects: true,
    smoothTouch: 0.1,
  });

  document.querySelectorAll('.js-fade-in').forEach(function (el) {
    gsap.to(el, {
      scrollTrigger: {
        trigger: el,
        start: 'top 90%'
      },

      duration: 1.8,
      opacity: 1,
      y: 0,
      ease: 'power2.out'
    });
  });
});

// from main
//search

function initSearch() {
  const searchBlock = document.querySelector('#search');
  if (!searchBlock) return;

  const liveSearch = searchBlock.querySelector('.live-search');
  const triggers = searchBlock.querySelectorAll('.search__btn');
  const closeBtn = searchBlock.querySelector('#search .search__list-close');
  const input = searchBlock.querySelector('input[name="search"]');

  const toggleSearch = (isOpen) => {
    liveSearch.classList.toggle('is-open', isOpen);
    gsap.to(liveSearch, {
      duration: 0.3,
      opacity: isOpen ? 1 : 0,
      y: isOpen ? 0 : -10,
      autoAlpha: isOpen ? 1 : 0,
      ease: isOpen ? 'power2.out' : 'power2.in'
    });
  };

  triggers.forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      toggleSearch(true);
    });
  });

  if (closeBtn) {
    closeBtn.addEventListener('click', (e) => {
      e.preventDefault();
      toggleSearch(false);
    });
  }

  input.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
      e.preventDefault();
      toggleSearch(true);
    }
  });
}

initSearch();

document.addEventListener('click', () => {
  document.querySelectorAll('.c-select.is-open').forEach(select => {
    select.classList.remove('is-open');
    gsap.to(select.querySelector('.c-select__dropdown'), {
      duration: 0.3,
      opacity: 0,
      y: -10,
      autoAlpha: 0,
      ease: 'power2.in'
    });
  });
});

document.addEventListener('click', (e) => {
  const searchBlock = document.querySelector('#search');
  const liveSearch = searchBlock?.querySelector('.live-search');
  const closeBtn = searchBlock?.querySelector('#search .search__list-close');
  if (searchBlock && !searchBlock.contains(e.target) && liveSearch?.classList.contains('is-open')) {
    liveSearch.classList.remove('is-open');
    if (closeBtn) closeBtn.classList.add('close-open');
    gsap.to(liveSearch, {
      duration: 0.3,
      opacity: 0,
      y: -10,
      autoAlpha: 0,
      ease: 'power2.in'
    });
  }
});

// Mobile menu toggle
function toggleMenu(open) {
  var menu = document.querySelector('.mobi_menu_box');
  var overlay = document.querySelector('.mobi_menu_overlay');
  if (!menu) return;

  if (open) {
    document.body.classList.add('is-menu-open');
    menu.classList.add('open');
    if (overlay) overlay.classList.add('is-active');
  } else {
    menu.classList.remove('open');
    document.body.classList.remove('is-menu-open');
    if (overlay) overlay.classList.remove('is-active');
  }
}

$('.mobi_menu_togle').on('click', function () { toggleMenu(true); });
$('.mobi_menu_togle_close').on('click', function () { toggleMenu(false); });
$(document).on('click', '.mobi_menu_overlay', function () { toggleMenu(false); });



