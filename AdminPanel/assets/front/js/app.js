$(function () {
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

  $('.faq__title').on("click", function () {
    $(this).parent().toggleClass('faq__item--open');
    $(this).next().slideToggle();
    return false;
  });

  var tsSettings = {
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
});

var loader = function (ele) {
  if ($(ele).length) {
    var html = '<div class="loader-container"><span class="loader"></span></div>';
    $(ele).html(html);
  }
};

var inputSeparator = function (value) {
  if (value) {
    value = value.toString();
    return value.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  }
  return value;
};