  if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
    $("html").addClass("mobile");
  } else {
    $("html").addClass("desktop");
  }


  let options = {
    offset: ".container",
    offsetSide: "top",
    classes: {
      clone: "header-links--clone",
      stick: "header-links--stick",
      unstick: "header-links--unstick",
    },
  };

  new Headhesive(".header-links", options);

  $(".scroll[href^='#']").click(function () {
    var _href = $(this).attr("href");
    $("html, body").animate({ scrollTop: $(_href).offset().top - 60 + "px" });
    return false;
  });

  // menu-btn (mob)

  $(".menu-btn").on("click", function () {
    $(this).toggleClass("menu-btn--open");
    $(".mob-menu").toggleClass("mob-menu--open");
    $("body").toggleClass("hidden");
    $(".menu-overlay").fadeToggle();
  });

  $(".page").append('<div class="menu-overlay"></div>');
  $(".header-top").append('<div class="mob-menu"></div>');
  $(".header").find(".menu").clone().appendTo(".mob-menu");
  $(".header").find(".phone").clone().appendTo(".mob-menu");

  $(".mobile .menu__link--sub").on("click", function () {
    $(this).toggleClass("menu__link--open");
    $(this).closest(".menu__item").find(".menu-dropdown").slideToggle(200);
    return false;
  });

  $(".menu-overlay").on("click", function () {
    $(".menu-btn").removeClass("menu-btn--open");
    $(".mob-menu").removeClass("mob-menu--open");
    $(".mobile-filter").removeClass("mobile-filter--open");
    $(".base-page__aside").removeClass("base-page__aside--open");
    $("body").removeClass("hidden");
    $(this).hide();
  });

  $(".open-filter-btn").click(function () {
    $(".mobile-filter").toggleClass("mobile-filter--open");
    $("body").toggleClass("hidden");
    $(".menu-overlay").fadeToggle();
  });

  $(".btn-help-menu").click(function () {
    $(".base-page__aside").toggleClass("base-page__aside--open");
    $("body").toggleClass("hidden");
    $(".menu-overlay").fadeToggle();
  });

  // language

  let language = $(".language");

  $(".language__select").on("click", function () {
    $(this).parents(".language").toggleClass("language--open");
    $(this).parents(".language").find(".language__dropdown").slideToggle("fast");
  });

  let phone = $(".phone");

  $(".phone__select").on("click", function () {
    $(this).toggleClass("phone__select--open");
    $(this).parents(".phone").find(".phone__dropdown").slideToggle(200);
  });

  // Количество товара

  let $count__field = $(".count__field");
  let count__val = parseInt($count__field.val());

  if (count__val <= 1) {
    $(".count__minus").addClass("count__minus--disabled");
  }

  $(".count__minus").click(function () {
    let $input = $(this).parent().find(".count__field");
    let count = parseInt($input.val()) - 1;
    count = count < 1 ? 1 : count;
    if (count <= 1) {
      $(this).addClass("count__minus--disabled");
    } else {
      $(this).removeClass("count__minus--disabled");
    }
    if ($input.attr("max")) {
      let max = parseInt($input.attr("max"));
      if (count < max) {
        $(this).closest(".count").find(".count__plus").removeClass("count__plus--disabled");
      }
    }
    $input.val(count);
    $input.change();
    return false;
  });

  $(".count__plus").click(function () {
    let $input = $(this).parent().find(".count__field");
    let count = parseInt($input.val()) + 1;
    if (count >= 1) {
      $(this).closest(".count").find(".count__minus").removeClass("count__minus--disabled");
    }
    if ($input.attr("max")) {
      let max = parseInt($input.attr("max"));
      if (count >= max) {
        count = max;
        $(this).addClass("count__plus--disabled");
      }
    }
    $input.val(count);
    $input.change();
    return false;
  });



  $count__field.on("keydown", function (e) {
    if (e.key.length == 1 && e.key.match(/[^0-9'".]/)) {
      return false;
    }
  });

  // select-custom

  let sc = ".select-custom";
  let scItem = ".select-custom__item";
  let scItemActive = "select-custom__item--active";
  let scDrop = ".select-custom__dropdown";
  let scIcon = ".select-custom__icon";
  let scCurrent = ".select-custom__current";
  let scCurrentOpen = "select-custom__current--open";

  $(scCurrent).on("click", function () {
    let th = $(this);

    $(scCurrent).not(this).removeClass(scCurrentOpen);
    $(scCurrent).not(this).closest(sc).find(scDrop).slideUp(200);

    th.toggleClass(scCurrentOpen);
    th.closest(sc).find(scDrop).slideToggle(200);
  });

  $(scItem).on("click", function () {
    let th = $(this);
    let f = th.closest(sc);
    let selectImage = th.find(scIcon).attr("src");
    let selectText = th.find("span").text();

    f.find(scCurrent).find(scIcon).attr("src", selectImage);
    f.find(scCurrent).find("span").text(selectText);
    f.find(scDrop).slideUp(200);
    f.find(scCurrent).removeClass(scCurrentOpen);
    f.find(scItem).removeClass(scItemActive);
    th.addClass(scItemActive);
  });

  // faq toggle

  $(".faq__title").on("click", function () {
    $(this).parent().toggleClass("faq__item--open");
    $(this).next().slideToggle();
    return false;
  });

  let slideArhive = $(".box-vps .swiper-slide").detach();
  slideArhive.appendTo(".tariff-slider .swiper-wrapper");

  /*   slider   */

  let tsSettings = {
    dir: "rtl",
    navigation: {
      nextEl: ".tariff-slider__button-next",
      prevEl: ".tariff-slider__button-prev",
    },
    pagination: {
      el: ".tariff-slider__pagination",
      clickable: true,
      type: "fraction",
    },
    observer: true,
    observeParents: true,
    observeSlideChildren: true,
    simulateTouch: false,
    watchSlidesVisibility: true,
    watchSlidesProgress: true,
    watchOverflow: true,
    breakpoints: {
      300: {
        speed: 100,
        slidesPerView: 1,
        slidesPerGroup: 1,
        spaceBetween: 20,
        loop: true,
        /*autoHeight: false,*/
      },
      660: {
        slidesPerView: 2,
        slidesPerGroup: 2,
        spaceBetween: 20,
        loop: true,
      },
      993: {
        slidesPerView: 3,
        slidesPerGroup: 3,
        spaceBetween: 20,
        loop: true,
      },
      1101: {
        slidesPerView: 3,
        slidesPerGroup: 3,
        spaceBetween: 20,
        loop: true,
      },
      1200: {
        slidesPerView: 3,
        slidesPerGroup: 3,
        spaceBetween: 30,
        loop: false,
      },
    },
  };

  new Swiper(".tariff-slider__container", tsSettings);

  new Swiper(".payments-slider", {
    navigation: {
      nextEl: ".swiper-button-next",
    },
    slidesPerView: "auto",
    loop: true,
    loopedSlides: 2,
    spaceBetween: 10,
    grabCursor: true,
  });

  new Swiper(".header-slider", {
    slidesPerView: 1,
    slidesPerGroup: 1,
    spaceBetween: 30,
    loop: false,
    autoHeight: true,
    navigation: {
      nextEl: ".swiper-button-prev",
      prevEl: ".swiper-button-next",
    },
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
  });

  new Swiper(".slider-news", {
    pagination: {
      el: ".pagination-bullet--news",
      clickable: true,
    },
    breakpoints: {
      300: {
        slidesPerView: 1,
        slidesPerGroup: 1,
        spaceBetween: 20,
        autoHeight: true,
        loop: true,
      },
      650: {
        slidesPerView: 2,
        slidesPerGroup: 1,
        spaceBetween: 20,
        autoHeight: false,
        loop: true,
      },
      993: {
        slidesPerView: 3,
        slidesPerGroup: 3,
        spaceBetween: 20,
        allowTouchMove: false,
        autoHeight: false,
        loop: false,
      },
      1200: {
        slidesPerView: 3,
        slidesPerGroup: 3,
        spaceBetween: 30,
        allowTouchMove: false,
        autoHeight: false,
        loop: false,
      },
    },
  });

  $(document).on("click", function (e) {
    if (!language.is(e.target) && language.has(e.target).length === 0) {
      language.removeClass("language--open");
      $(".language__dropdown").slideUp("fast");
    }

    if (!phone.is(e.target) && phone.has(e.target).length === 0) {
      $(".phone__select").removeClass("phone__select--open");
      $(".phone__dropdown").slideUp(200);
    }

    if (!$(sc).is(e.target) && $(sc).has(e.target).length === 0) {
      $(scCurrent).removeClass(scCurrentOpen);
      $(scCurrent).closest(sc).find(scDrop).slideUp(200);
    }
  });

  $(".custom-select").each(function () {
    let classes = $(this).attr("class"),
      id = $(this).attr("id"),
      name = $(this).attr("name");
    let template = '<div class="' + classes + '" data-filter-group="">';
    template += '<span class="custom-select-trigger">' + $(this).attr("placeholder") + "</span>";
    template += '<div class="custom-options">';
    $(this)
      .find("option")
      .each(function () {
        template +=
          '<span class="custom-option ' +
          $(this).attr("class") +
          '" data-value="' +
          $(this).attr("value") +
          '">' +
          $(this).html() +
          "</span>";
      });
    template += "</div></div>";

    $(this).wrap('<div class="custom-select-wrapper"></div>');
    $(this).hide();
    $(this).after(template);
  });

  $(".custom-option:first-of-type").hover(
    function () {
      $(this).parents(".custom-options").addClass("option-hover");
    },
    function () {
      $(this).parents(".custom-options").removeClass("option-hover");
    }
  );

  $(".custom-select-trigger").on("click", function (event) {
    $("html").one("click", function () {
      $(".custom-select").removeClass("opened");
    });
    $(this).parents(".custom-select").toggleClass("opened");
    event.stopPropagation();
  });

  // servers ranges
  if ($(".js-range-slider").length) {
    let $rangeA = $('[data-ref="range-slider-a"]');
    let $rangeB = $('[data-ref="range-slider-b"]');
    let $rangeC = $('[data-ref="range-slider-c"]');

    $rangeA.ionRangeSlider({
      skin: "round",
      type: "double",
      onChange: handleRangeInputChange,
    });

    $rangeB.ionRangeSlider({
      skin: "round",
      type: "double",
      onChange: handleRangeInputChange,
    });

    $rangeC.ionRangeSlider({
      skin: "round",
      type: "double",
      onChange: handleRangeInputChange,
    });

    let instanceA = $rangeA.data("ionRangeSlider");
    let instanceB = $rangeB.data("ionRangeSlider");
    let instanceC = $rangeC.data("ionRangeSlider");

    function getRange() {
      let aMin = Number(instanceA.result.from);
      let aMax = Number(instanceA.result.to);
      let bMin = Number(instanceB.result.from);
      let bMax = Number(instanceB.result.to);
      let cMin = Number(instanceC.result.from);
      let cMax = Number(instanceC.result.to);
      return {
        aMin: aMin,
        aMax: aMax,
        bMin: bMin,
        bMax: bMax,
        cMin: cMin,
        cMax: cMax,
      };
    }

    function handleRangeInputChange() {
      mixer.filter(mixer.getState().activeFilter);
    }

    function filterTestResult(testResult, target) {
      let a = Number(target.dom.el.getAttribute("data-a"));
      let b = Number(target.dom.el.getAttribute("data-b"));
      let c = Number(target.dom.el.getAttribute("data-c"));
      let range = getRange();

      if (
        a < range.aMin ||
        a > range.aMax ||
        b < range.bMin ||
        b > range.bMax ||
        c < range.cMin ||
        c > range.cMax
      ) {
        testResult = false;
      }
      return testResult;
    }

    mixitup.Mixer.registerFilter("testResultEvaluateHideShow", "range", filterTestResult);
  }