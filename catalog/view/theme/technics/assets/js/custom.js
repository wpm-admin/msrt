var gsap = window.gsap;
var ScrollTrigger = window.ScrollTrigger;

function decodeEntities(str) {
  var txt = document.createElement('textarea');
  txt.innerHTML = str;
  return txt.value;
}

$(function () {
  // Init sliders via App bundle
  App.initSwiper('.hero-slider', {
    autoplay: {
      delay: 5000,
    },
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
      nopt.value = item.value || item.model_id || item.product_id || item.category_id || '';
      nopt.textContent = decodeEntities(item.name);
      if (item.href) nopt.setAttribute('data-href', item.href);
      native.appendChild(nopt);
      var opt = document.createElement('div');
      opt.className = 'c-select__option';
      opt.dataset.value = item.value || item.model_id || item.product_id || item.category_id || '';
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
      native.value = items[0].value || items[0].model_id || items[0].product_id || items[0].category_id || '';
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

  var modelHrefs = {};
  // Pre-populate from initial PHP-rendered options
  (function initModelHrefs() {
    var native = document.querySelector('#lm-model-select .c-select__native');
    if (native) {
      for (var i = 0; i < native.options.length; i++) {
        var opt = native.options[i];
        if (opt.value) modelHrefs[opt.value] = opt.getAttribute('data-href') || '';
      }
    }
  })();

  setupDelegatedClicks();

  function updateBtnView(modelId) {
    var btnView = document.querySelector('#categories-view-all');
    if (btnView && modelHrefs[modelId]) {
      btnView.href = modelHrefs[modelId];
    }
  }

  function loadModels(markId) {
    lastRequestedMarkId = markId;
    $.getJSON('index.php?route=extension/module/lm_category_section/models&mark_id=' + markId, function (data) {
      if (lastRequestedMarkId !== markId) return;
      var container = document.querySelector('#lm-model-select');
      rebuildSelect(container, data, $('.select__models .color-grey').text());
      setupDelegatedClicks();
      modelHrefs = {};
      data.forEach(function (m) { modelHrefs[m.model_id] = m.href || ''; });
      if (data.length > 0) {
        loadCategories(data[0].model_id);
        updateBtnView(data[0].model_id);
      }
      document.dispatchEvent(new CustomEvent('lm:content-updated'));
      ScrollTrigger.refresh();
    });
  }

  function updateFooterCategories(items) {
    var mid = Math.ceil(items.length / 2);
    var col1 = items.slice(0, mid);
    var col2 = items.slice(mid);
    var list1 = document.querySelector('#footer-categories-col1');
    var list2 = document.querySelector('#footer-categories-col2');
    if (list1) {
      list1.innerHTML = '';
      col1.forEach(function (item) {
        var li = document.createElement('li');
        var a = document.createElement('a');
        a.href = item.href;
        a.textContent = decodeEntities(item.name);
        li.appendChild(a);
        list1.appendChild(li);
      });
    }
    if (list2) {
      list2.innerHTML = '';
      col2.forEach(function (item) {
        var li = document.createElement('li');
        var a = document.createElement('a');
        a.href = item.href;
        a.textContent = decodeEntities(item.name);
        li.appendChild(a);
        list2.appendChild(li);
      });
    }
  }

  function loadCategories(modelId) {
    $.getJSON('index.php?route=extension/module/lm_category_section/categories&model_id=' + modelId, function (data) {
      var container = document.querySelector('#lm-category-select');
      rebuildSelect(container, data, $('#lm-category-select').closest('.select__category').find('.color-grey').text());
      setupDelegatedClicks();
      var openLink = document.querySelector('#category-open-link');
      if (openLink && data.length > 0 && data[0].href) openLink.href = data[0].href;
      updateFooterCategories(data);
      document.dispatchEvent(new CustomEvent('lm:content-updated'));
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
      document.dispatchEvent(new CustomEvent('lm:content-updated'));
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
      }
    }, 200);
  }

  $(document).on('change', '#lm-model-select .c-select__native', function () {
    var modelId = this.value;
    if (!modelId) return;
    loadCategories(modelId);
    updateBtnView(modelId);
  });

  $(document).on('change', '#lm-category-select .c-select__native', function () {
    var opt = this.options[this.selectedIndex];
    if (opt && opt.dataset.href) {
      window.location.href = opt.dataset.href;
    }
  });
});



// Search — scroll to results only when real data arrives
function initSearch() {
  var searchBlock = document.querySelector('#search');
  if (!searchBlock) return;

  var searchWrapper = searchBlock.querySelector('.search__wrapper');
  var liveSearch = searchBlock.querySelector('.live-search');
  var input = searchBlock.querySelector('input[name="search"]');
  var searchIcon = searchBlock.querySelector('.search__btn-icon');
  var closeBtn = searchBlock.querySelector('.search__list-close');
  var searchTextBtn = searchBlock.querySelector('.search__btn-text');
  var header = document.querySelector('.header');
  if (!liveSearch) return;

  var pendingScroll = false;

  function doScroll() {
    var headerH = header ? header.offsetHeight : 80;
    var targetY = window.scrollY + searchWrapper.getBoundingClientRect().top - headerH - 20;
    gsap.to(window, { scrollTo: { y: Math.max(targetY, 0) }, duration: 0.2, ease: 'sine.inOut' });
  }

  function openSearch() {
    if (liveSearch.classList.contains('is-open')) return;
    liveSearch.classList.add('is-open');

    gsap.killTweensOf(liveSearch);
    gsap.set(liveSearch, { opacity: 0 });
    var headerH = header ? header.offsetHeight : 80;
    var offset = liveSearch.getBoundingClientRect().top - searchWrapper.getBoundingClientRect().top;
    var maxH = window.innerHeight - (headerH + 20 + offset) - 25;
    gsap.set(liveSearch, { maxHeight: Math.max(maxH, 150) });
    gsap.to(liveSearch, { opacity: 1, duration: 0.3, ease: 'power2.out' });

    pendingScroll = true;
  }

  function closeSearch() {
    if (!liveSearch.classList.contains('is-open')) return;
    pendingScroll = false;
    gsap.killTweensOf(liveSearch);
    gsap.to(liveSearch, {
      opacity: 0,
      duration: 0.3,
      ease: 'power2.in',
      onComplete: function () {
        liveSearch.classList.remove('is-open');
      }
    });
  }

  // Scroll only when 2+ real items appear
  var searchUl = liveSearch.querySelector('ul');
  if (searchUl) {
    new MutationObserver(function () {
      if (!pendingScroll) return;
      var items = searchUl.querySelectorAll('li');
      if (items.length < 2) return;
      pendingScroll = false;
      doScroll();
    }).observe(searchUl, { childList: true, subtree: true });
  }

  if (searchIcon) {
    searchIcon.addEventListener('click', function (e) {
      e.preventDefault();
      input.focus();
      openSearch();
    });
  }

  if (closeBtn) {
    closeBtn.addEventListener('click', function (e) {
      e.preventDefault();
      closeSearch();
    });
  }

  if (searchTextBtn) {
    searchTextBtn.addEventListener('click', function (e) {
      e.preventDefault();
      if (input.value.trim()) {
        window.location.href = 'index.php?route=product/search&search=' + encodeURIComponent(input.value.trim());
      }
    });
  }

  input.addEventListener('focus', function () {
    if (input.value.trim().length > 0) openSearch();
  });

  input.addEventListener('input', function () {
    if (input.value.trim().length > 0) { openSearch(); }
    else { closeSearch(); }
  });

  input.addEventListener('keydown', function (e) {
    if (e.key === 'Enter') {
      e.preventDefault();
      if (input.value.trim()) {
        window.location.href = 'index.php?route=product/search&search=' + encodeURIComponent(input.value.trim());
      }
    }
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && liveSearch.classList.contains('is-open')) {
      input.blur();
      closeSearch();
    }
  });

  document.addEventListener('click', function (e) {
    if (liveSearch.classList.contains('is-open') && !searchBlock.contains(e.target)) {
      closeSearch();
    }
  });
}

initSearch();

document.addEventListener('click', function () {
  document.querySelectorAll('.c-select.is-open').forEach(function (select) {
    select.classList.remove('is-open');
    gsap.to(select.querySelector('.c-select__dropdown'), {
      duration: 0.4,
      opacity: 0,
      y: -10,
      autoAlpha: 0,
      ease: 'power2.in'
    });
  });
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



