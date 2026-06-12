$(function() {
  var currentMarkId = null;

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
    items.forEach(function(item) {
      var nopt = document.createElement('option');
      nopt.value = item.value || item.model_id || item.category_id || '';
      nopt.textContent = item.name;
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
      span.textContent = item.name;
      opt.appendChild(span);
      dropdown.appendChild(opt);
    });
    if (items.length > 0) {
      label.innerHTML = items[0].name || labelText;
      native.value = items[0].value || items[0].model_id || items[0].category_id || '';
      dropdown.querySelector('.c-select__option').classList.add('is-active');
    } else {
      label.textContent = labelText;
    }
  }

  function setupDelegatedClicks() {
    document.querySelectorAll('.c-select__dropdown').forEach(function(dd) {
      dd.onclick = function(e) {
        var opt = e.target.closest('.c-select__option');
        if (!opt) return;
        var href = opt.dataset.href;
        if (href) {
          window.location.href = href;
          return;
        }
        var select = this.closest('.c-select');
        var label = select.querySelector('.c-select__label');
        var native = select.querySelector('.c-select__native');
        var dropdown = select.querySelector('.c-select__dropdown');
        var value = opt.dataset.value;
        label.innerHTML = opt.innerHTML;
        native.value = value;
        select.querySelectorAll('.c-select__option').forEach(function(o) {
          o.classList.remove('is-active');
        });
        opt.classList.add('is-active');
        select.classList.remove('is-open');
        dropdown.style.opacity = '0';
        dropdown.style.transform = 'translateY(-10px)';
        dropdown.style.visibility = 'hidden';
        var evt = new Event('change', { bubbles: true });
        native.dispatchEvent(evt);
      };
    });
  }

  function loadModels(markId) {
    currentMarkId = markId;
    $.getJSON('index.php?route=common/home/getModels&mark_id=' + markId, function(data) {
      var container = document.querySelector('#lm-model-select');
      rebuildSelect(container, data, $('.select__models .color-grey').text());
      setupDelegatedClicks();
      if (data.length > 0) loadCategories(markId, data[0].model_id);
    });
  }

  function loadCategories(markId, modelId) {
    $.getJSON('index.php?route=common/home/getCategories&mark_id=' + markId + '&model_id=' + modelId, function(data) {
      var container = document.querySelector('#lm-category-select');
      rebuildSelect(container, data, $('#lm-category-select').closest('.select__category').find('.color-grey').text());
      setupDelegatedClicks();
    });
    loadCategoryBottom(markId, modelId);
  }

  function loadCategoryBottom(markId, modelId) {
    $.getJSON('index.php?route=common/home/getCategoryBottom&mark_id=' + markId + '&model_id=' + modelId, function(data) {
      var wrapper = document.querySelector('.categories__bottom .swiper-wrapper');
      if (!wrapper) return;
      wrapper.innerHTML = '';
      data.forEach(function(item) {
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
        img.alt = item.name;
        pic.appendChild(img);
        div.appendChild(pic);
        a.appendChild(div);
        slide.appendChild(a);
        wrapper.appendChild(slide);
      });
      var swiperEl = document.querySelector('.categories__bottom');
      if (swiperEl && swiperEl.swiper) swiperEl.swiper.update();
    });
  }

  var swiperEl = document.querySelector('.categories__slider');
  if (swiperEl) {
    var check = setInterval(function() {
      if (swiperEl.swiper) {
        clearInterval(check);
        swiperEl.swiper.on('slideChange', function() {
          var slide = this.slides[this.activeIndex];
          var markId = slide.getAttribute('data-mark-id');
          if (markId) loadModels(markId);
        });
        var firstSlide = swiperEl.swiper.slides[swiperEl.swiper.activeIndex || 0];
        var firstMarkId = firstSlide.getAttribute('data-mark-id');
        if (firstMarkId) loadModels(firstMarkId);
      }
    }, 200);
  }

  $(document).on('change', '#lm-model-select .c-select__native', function() {
    var modelId = this.value;
    if (modelId && currentMarkId) loadCategories(currentMarkId, modelId);
  });

  $(document).on('change', '#lm-category-select .c-select__native', function() {
    var opt = this.options[this.selectedIndex];
    if (opt && opt.dataset.href) {
      window.location.href = opt.dataset.href;
    }
  });
});
