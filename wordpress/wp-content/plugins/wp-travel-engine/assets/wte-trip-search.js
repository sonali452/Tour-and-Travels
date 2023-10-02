(function (window, document, $, undefined) {
  jQuery(document).ready(function ($) {
    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
      // true for mobile device
      $('.advanced-search-field.search-trip-type').removeClass('wte-list-opn');
      $(".advanced-search-field ul.wte-terms-list").slideUp(350);
    } else {
      // false for not mobile device
      $(".advanced-search-field ul.wte-terms-list").slideDown(350);
    }
    $(".trip-type").on("click", function () {
      $(this).parent().find("ul.wte-terms-list").slideToggle();
      $(this).parent().toggleClass("wte-list-opn");
    });
    $(".trip-activities").on("click", function () {
      $(this).parent().find("ul.wte-terms-list").slideToggle();
      $(this).parent().toggleClass("wte-list-opn");
    });
    $(".trip-destination").on("click", function () {
      $(this).parent().find("ul.wte-terms-list").slideToggle();
      $(this).parent().toggleClass("wte-list-opn");
    });
    $(".trip-fsd-title").on("click", function () {
      $("ul.wte-fsd-list").slideToggle();
      $(this).parent().toggleClass("wte-list-opn");
    });

    var choices = {};
    setTimeout(function () {
      $(".wte-filter-item").each(function (index) {
        if ($(this).prop("checked")) {
          $(this)
            .parent("label")
            .siblings("ul")
            .find("input[type=checkbox]")
            .prop("checked", this.checked);
          var parent = $(this).parents(
            ".advanced-search-field ul.wte-terms-list"
          );
          parent.slideDown();
        }
      });
    }, 500);

    $(".advanced-search-field ul.wte-terms-list").mCustomScrollbar();

    $("#duration-slider-range").slider({
      range: true,
      min: +wte_advanced_search.min_duration,
      max: +wte_advanced_search.max_duration,
      values: [
        wte_advanced_search.selected_min_duration,
        wte_advanced_search.selected_max_duration,
      ],
      slide: function (event, ui) {
        maxdur = ui.values[1];
        mindur = ui.values[0];
        maxdur_html = ui.values[1] + " " + wte_advanced_search.days_text;
        mindur_html = ui.values[0] + " " + wte_advanced_search.days_text;
        $("#min-duration").html(mindur_html);
        $("#max-duration").html(maxdur_html);
      },
      stop: function (event, ui) {
        $("#duration-slider-range").slider("disable");
        maxdur = ui.values[1];
        mindur = ui.values[0];
        maxdur_html = ui.values[1] + " " + wte_advanced_search.days_text;
        mindur_html = ui.values[0] + " " + wte_advanced_search.days_text;
        $("#min-duration").html(mindur_html);
        $("#max-duration").html(maxdur_html);
        $("#min-duration").val(mindur);
        $("#max-duration").val(maxdur);
        mincost = $("#min-cost").val();
        maxcost = $("#max-cost").val();
        nonce = $("#search-nonce").val();
        $(".advanced-search-field input[type=checkbox]").each(function () {
          if ($(this).is(":checked")) {
            if (!choices.hasOwnProperty(this.name)) {
              choices[this.name] = [];
            }
            choices[this.name].push(this.value);
          }
        });
        mode = $(".wte-view-mode-selection.active").attr("data-mode");
        sort = $(".orderby").children("option:selected").val();
        date = $("input[name='trip-chosen-date']:checked").val();
        jQuery.ajax({
          type: "post",
          url: wte_advanced_search.ajax_url,
          data: {
            action: "wte_show_ajax_result",
            maxcost: maxcost,
            date: date,
            mincost: mincost,
            maxdur: maxdur,
            mindur: mindur,
            result: choices,
            nonce: nonce,
            mode: mode,
            sort: sort,
          },
          beforeSend: function () {
            $("#loader").fadeIn(500);
          },
          success: function (response) {
            $(".wte-category-outer-wrap").html(response.data.data);
            $(".wte-filter-foundposts .searchFoundPosts").html(
              response.data.foundposts
            );
            $(".trip-pagination").remove();
            $("#duration-slider-range").slider("enable");
          },
          complete: function () {
            $("#loader").fadeOut(500);
            wte_search_results_rating_star_initializer();
          },
        });
      },
    });

    $("#cost-slider-range").slider({
      range: true,
      min: +wte_advanced_search.min_cost,
      max: +wte_advanced_search.max_cost,
      values: [
        wte_advanced_search.selected_min_cost,
        wte_advanced_search.selected_max_cost,
      ],
      slide: function (event, ui) {
        maxcost = ui.values[1];
        mincost = ui.values[0];
        maxcost_html = wte_advanced_search.cur_symbol + " " + ui.values[1];
        mincost_html = wte_advanced_search.cur_symbol + " " + ui.values[0];
        $("#min-cost").html(mincost_html);
        $("#max-cost").html(maxcost_html);
      },
      stop: function (event, ui) {
        $("#cost-slider-range").slider("disable");
        maxcost = ui.values[1];
        mincost = ui.values[0];
        maxcost_html = wte_advanced_search.cur_symbol + " " + ui.values[1];
        mincost_html = wte_advanced_search.cur_symbol + " " + ui.values[0];
        $("#min-cost").html(mincost_html);
        $("#max-cost").html(maxcost_html);
        if (
          "undefined" !== typeof WTE_CC_convData &&
          WTE_CC_convData.reverse_rate
        ) {
          var reverse_rate = parseFloat(WTE_CC_convData.reverse_rate);
          mincost = Math.round(mincost * reverse_rate);
          maxcost = Math.round(maxcost * reverse_rate);
        }

        $("#min-cost").val(mincost);
        $("#max-cost").val(maxcost);

        maxdur = $("#max-duration").val();
        mindur = $("#min-duration").val();
        nonce = $("#search-nonce").val();
        $(".advanced-search-field input[type=checkbox]").each(function () {
          if ($(this).is(":checked")) {
            if (!choices.hasOwnProperty(this.name)) {
              choices[this.name] = [];
            }
            choices[this.name].push(this.value);
          }
        });
        mode = $(".wte-view-mode-selection.active").attr("data-mode");
        sort = $(".orderby").children("option:selected").val();
        date = $("input[name='trip-chosen-date']:checked").val();
        jQuery.ajax({
          type: "post",
          url: wte_advanced_search.ajax_url,
          data: {
            action: "wte_show_ajax_result",
            maxcost: maxcost,
            date: date,
            mincost: mincost,
            maxdur: maxdur,
            mindur: mindur,
            result: choices,
            nonce: nonce,
            mode: mode,
            sort: sort,
          },
          beforeSend: function () {
            $("#loader").fadeIn(500);
          },
          success: function (response) {
            $(".wte-category-outer-wrap").html(response.data.data);
            $(".wte-filter-foundposts .searchFoundPosts").html(
              response.data.foundposts
            );
            $(".trip-pagination").remove();
            $("#cost-slider-range").slider("enable");
          },
          complete: function () {
            $("#loader").fadeOut(500);
            wte_search_results_rating_star_initializer();
          },
        });
      },
    });

    $("body").on("change", ".trip-date-select", function () {
      nonce = $("#search-nonce").val();
      date = $(this).val();
      maxdur = $("#max-duration").text();
      mindur = $("#min-duration").text();
      mincost = $("#min-cost").text();
      maxcost = $("#max-cost").text();
      $(".advanced-search-field input[type=checkbox]").each(function () {
        if ($(this).is(":checked")) {
          if (!choices.hasOwnProperty(this.name)) {
            choices[this.name] = [];
          }
          choices[this.name].push(this.value);
        }
      });
      mode = $(".wte-view-mode-selection.active").attr("data-mode");
      sort = $(".orderby").children("option:selected").val();
      jQuery.ajax({
        type: "post",
        url: wte_advanced_search.ajax_url,
        data: {
          action: "wte_show_ajax_result",
          date: date,
          maxcost: maxcost,
          mincost: mincost,
          maxdur: maxdur,
          mindur: mindur,
          result: choices,
          nonce: nonce,
          mode: mode,
          sort: sort,
        },
        beforeSend: function () {
          $("#loader").fadeIn(500);
        },
        success: function (response) {
          $(".wte-category-outer-wrap").html(response.data.data);
          $(".wte-filter-foundposts .searchFoundPosts").html(
            response.data.foundposts
          );
          $(".trip-pagination").remove();
        },
        complete: function () {
          $("#loader").fadeOut(500);
          wte_search_results_rating_star_initializer();
        },
      });
    });

    $("body").on("change", "input[name='trip-chosen-date']", function () {
      nonce = $("#search-nonce").val();
      date = $("input[name='trip-chosen-date']:checked").val();
      maxdur = $("#max-duration").text();
      mindur = $("#min-duration").text();
      mincost = $("#min-cost").text();
      maxcost = $("#max-cost").text();
      $(".advanced-search-field input[type=checkbox]").each(function () {
        if ($(this).is(":checked")) {
          if (!choices.hasOwnProperty(this.name)) {
            choices[this.name] = [];
          }
          choices[this.name].push(this.value);
        }
      });
      mode = $(".wte-view-mode-selection.active").attr("data-mode");
      sort = $(".orderby").children("option:selected").val();
      jQuery.ajax({
        type: "post",
        url: wte_advanced_search.ajax_url,
        data: {
          action: "wte_show_ajax_result",
          date: date,
          maxcost: maxcost,
          mincost: mincost,
          maxdur: maxdur,
          mindur: mindur,
          result: choices,
          nonce: nonce,
          mode: mode,
          sort: sort,
        },
        beforeSend: function () {
          $("#loader").fadeIn(500);
        },
        success: function (response) {
          $(".wte-category-outer-wrap").html(response.data.data);
          $(".wte-filter-foundposts .searchFoundPosts").html(
            response.data.foundposts
          );
          $(".trip-pagination").remove();
        },
        complete: function () {
          $("#loader").fadeOut(500);
          wte_search_results_rating_star_initializer();
        },
      });
    });

    $("body").on(
      "change",
      ".advanced-search-field input[type=checkbox]",
      function () {
        mincost = $("#min-cost").val();
        maxcost = $("#max-cost").val();
        mindur = $("#min-duration").val();
        maxdur = $("#max-duration").val();
        $(".advanced-search-field input[type=checkbox]").each(function () {
          if ($(this).is(":checked")) {
            if (!choices.hasOwnProperty(this.name)) {
              choices[this.name] = [];
            }
            var idx = $.inArray(this.value, choices[this.name]);
            if (idx == -1) {
              choices[this.name].push(this.value);
            }
          }
        });
        value = this.value;
        if (!$(this).is(":checked")) {
          var idx = choices[this.name].indexOf(value);
          if (idx > -1) {
            choices[this.name].splice(idx, 1);
          }
        }
        nonce = $("#search-nonce").val();
        mode = $(".wte-view-mode-selection.active").attr("data-mode");
        sort = $(".orderby").children("option:selected").val();
        date = $("input[name='trip-chosen-date']:checked").val();
        jQuery.ajax({
          type: "post",
          url: wte_advanced_search.ajax_url,
          data: {
            action: "wte_show_ajax_result",
            maxcost: maxcost,
            date: date,
            mincost: mincost,
            maxdur: maxdur,
            mindur: mindur,
            result: choices,
            nonce: nonce,
            mode: mode,
            sort: sort,
          },
          beforeSend: function () {
            $("#loader").fadeIn(500);
          },
          success: function (response) {
            $(".wte-category-outer-wrap").html(response.data.data);
            debugger;
            console.log(response.data);
            $(".wte-filter-foundposts .searchFoundPosts").html(
              response.data.foundposts
            );
            $(".trip-pagination").remove();
          },
          complete: function () {
            $("#loader").fadeOut(500);
            wte_search_results_rating_star_initializer();
          },
        });
      }
    );

    $("body").on("click", ".load-more-search", function (e) {
      e.preventDefault();

      var button = $(this),
        current_page = button.attr("data-current-page"),
        max_page = button.attr("data-max-page"),
        mode = $(".wte-view-mode-selection.active").attr("data-mode"),
        data = {
          action: "wte_show_ajax_result_load",
          query: button.attr("data-query-vars"),
          page: current_page,
          mode: mode,
        };

      $.ajax({
        // you can also use $.post here
        url: WTEAjaxData.ajaxurl, // AJAX handler
        data: data,
        type: "POST",
        beforeSend: function (xhr) {
          $("#loader").fadeIn(500); // change the button text, you can also add a preloader image
        },
        success: function (response) {
          $(".wte-category-outer-wrap .category-main-wrap").append(response);
          current_page++;
          button.attr("data-current-page", current_page);
          if (current_page == max_page) button.parent().remove();
        },
        complete: function () {
          $("#loader").fadeOut(500);
          wte_search_results_rating_star_initializer();
        },
      });
    });
    $(
      'select[name="destination"], .advanced-search-field select[name="cat"], select[name="activities"], .advanced-search-field .trip-date-select'
    ).niceSelect();
  });

  function wte_search_results_rating_star_initializer() {
    if ($(document).find(".trip-review-stars").length) {
      $(document)
        .find(".trip-review-stars")
        .each(function () {
          var rating_value = $(this).data("rating-value");
          starSvgIcon = $(this).data("icon-type");
          var starSvgIcon = starSvgIcon !== "" ? starSvgIcon : "";
          $(this).rateYo({
            rating: rating_value,
            starSvg: starSvgIcon,
          });
        });
    }
  }
})(window, document, jQuery);
