$(document).ready(function () {
  servicesClass.init();
});

var servicesClass = {
  init: function () {
    servicesClass.eventHandler();
  },
  eventHandler: function () {
    $(".js-tab-trigger").on("click", function (e) {
      e.preventDefault();
      e.stopPropagation();
      e.stopImmediatePropagation();
      var id = $(this).data("id");
      $(".js-tab-trigger").removeClass("js-tab-trigger--active");
      $(this).addClass("js-tab-trigger--active");
      servicesClass.getSevices(id);
    });

    $(document).on("click", ".slocation-ajax-btn", function (e) {
      e.preventDefault();
      e.stopPropagation();
      e.stopImmediatePropagation();
      var mainFilter = $(this).parents("ul.main-filter-countries__list");
      mainFilter.find("li").removeClass("main-filter-countries__link--active");
      $(this).parent("li").addClass("main-filter-countries__link--active");

      var categoryId = $(this).data("category");
      var locationId = $(this).data("id");

      servicesClass.getSevices(categoryId, locationId);
    });

    $(document).on("click", ".servies-order-hours-input", function () {
      var values = $(this).attr("data-values");
      try {
        var values = JSON.parse(window.atob(values));

        var parent = $(this).parents(".trf-item");
        var tvalues = parent.find(".trf-rate-value");

        var servicePrice = values.total_price;
        servicePrice = parseFloat(servicePrice).toFixed(0);
        var html = `<b><var>${inputSeparator(servicePrice)}</var> <i>تومان</i></b>`;
        html += `<span>${values.value} ${values.label}</span>`;
        tvalues.html(html);
      } catch (err) {}
    });
    $(document).on("click", ".btn-order-services", function (e) {
      e.preventDefault();
      e.stopPropagation();
      e.stopImmediatePropagation();
      var serviceId = $(this).data("id");
      var locationId = $(this).data("location");
      var hours = "";

      var serviceItem = $(this).parents(".service-item");
      var selectedHours = serviceItem.find(".servies-order-hours-input:checked");
      if (selectedHours && selectedHours.length) {
        hours = selectedHours.val();
      }

      var qsObj = {
        service_id: serviceId,
      };
      if (hours) {
        qsObj.hours = hours;
      }
      if (locationId) {
        qsObj.location_id = locationId;
      }

      var queryStrArr = Object.keys(qsObj).map(function (key) {
        return `${key}=${qsObj[key]}`;
      });
      var queryStr = queryStrArr.join("&");
      var url = siteUrl(`user/services/add`);
      if (queryStr.length) {
        url += `?${queryStr}`;
      }

      if (!userLogin) {
        var backUrl = window.btoa(url);
        var url = siteUrl("login?back-url=" + backUrl);
      }
      document.location.href = url;
    });
  },
  getSevices: function (catId, locationId = null) {
    var servicesWrapper = "#services-list";
    var locationWrapper = "#services-locations";

    var useSlider = $(servicesWrapper).hasClass("swiper-wrapper");

    $(servicesWrapper).html("");
    $.ajax({
      url: siteUrl(`ajax/services/search`),
      type: "POST",
      data: {
        category_id: catId,
        location_id: locationId,
        use_slider: useSlider,
      },
      beforeSend: function () {
        loader(servicesWrapper);
      },
      success: function (response) {
        $(servicesWrapper).html("");
        if (response.s_html) {
          $(servicesWrapper).html(response.s_html);
        }
        if (response.sl_html) {
          $(locationWrapper).html(response.sl_html);
        }
      },
      error: function () {
        $(servicesWrapper).html("");
      },
    });
  },
};
