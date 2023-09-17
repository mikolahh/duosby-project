// Код на jquery

$(function () {
  // popup картинок товара
  $(function () {
    $('.part-block__img.img1').click(function (e) {
      $('.popup-img').addClass('show');
      $('.popup-img__img1').addClass('show');
      $('.popup-img-overlay').addClass('show'); 
    })
    $('.part-block__img.img2').click(function (e) {
      $('.popup-img').addClass('show');
      $('.popup-img__img2').addClass('show');
      $('.popup-img-overlay').addClass('show');       
    })  
    $('.popup-img button').click(function (e) {    
      $('.popup-img').removeClass('show');
      $('.popup-img-overlay').removeClass('show');
      $('.popup-img__img1').removeClass('show');
      $('.popup-img__img2').removeClass('show');      
    })   
  });
  // Отправка данных со страницы регистрации нового админа
  $(function () {
    $('#register-admin-result').click(function () {
      $('#register-admin-result').html('');
    })
    $('#new_admin_button').on('click', function () {
      let loginValue = $('#new_admin_login').val();
      let passwordValue = $('#new_admin_password').val();
      let cpasswordValue = $('#new_admin_cpassword').val();
      let buttonValue = $('#new_admin_button').val();
      let values = {
        login: loginValue,
        password: passwordValue,
        cpassword: cpasswordValue,
        button: buttonValue,
      }
      $.ajax({
        url: '/admin/registerduosadmin',
        method: 'post',
        dataType: 'html',
        data: values,
        success: function (response) {
          let resp = JSON.parse(response);      
          let loginValid = resp.login_valid;
          let passwordValid = resp.password_valid;
          let cpasswordValid = resp.cpassword_valid;
          let responseSuccesse = resp.response_successe;
          let responseFail = resp.response_fail;
  
          if (responseSuccesse) {
            $('#register-admin-result').html(`<div class="alert alert-success">${responseSuccesse}</div>`);
          }
          if (responseFail) {
            $('#register-admin-result').html(`<div class="alert alert-danger">${responseFail}</div>`);
          }
          if (loginValid) {
            $('#login-admin-valid').html(`<span class="text-danger">${loginValid}</span>`)
          }
          if (passwordValid) {
            $('#password-admin-valid').html(`<span class="text-danger">${passwordValid}</span>`)
          }
          if (cpasswordValid) {
            $('#cpassword-admin-valid').html(`<span class="text-danger">${cpasswordValid}</span>`)
          }
        }
        })
    })
    
  });
  // Адаптивное меню
  $(function () {
    $(".header-nav__burger").click(function (e) {
      $(".header-nav__list").toggleClass("show-menu");
      $(".header-nav__btn.btn-close").toggle();
    });
    $(".header-nav__btn").click(function (e) {
      $(".header-nav__btn").toggle();
      $(".header-nav__list").toggleClass("show-menu");
    });
  });
  // Управление табами на странице администратора
  $(function () {
    $("ul.admin-tabs__caption").each(function (i) {
      var storage = localStorage.getItem("tab" + i);
      if (storage) {
        $(this)
          .find("li")
          .removeClass("actived")
          .eq(storage)
          .addClass("actived")
          .closest("div.admin-tabs")
          .find("div.admin-tabs__content")
          .removeClass("actived")
          .eq(storage)
          .addClass("actived");
      }
    });
    $("ul.admin-tabs__caption").on("click", "li:not(.actived)", function () {
      $(this)
        .addClass("actived")
        .siblings()
        .removeClass("actived")
        .closest("div.admin-tabs")
        .find("div.admin-tabs__content")
        .removeClass("actived")
        .eq($(this).index())
        .addClass("actived");
      var ulIndex = $("ul.admin-tabs__caption").index(
        $(this).parents("ul.admin-tabs__caption")
      );
      localStorage.removeItem("tab" + ulIndex);
      localStorage.setItem("tab" + ulIndex, $(this).index());
    });
  });
  // Загрузка прайса
  $(function () {
    $("#upload-price-result").hover(function (e) {
      $("#upload-price-result").html("");
    });
    $("#send-price-input").hover(function (e) {
      $("#xls-valid").html("");
    });
    $("#send-price-button").click(function (e) {
      e.preventDefault();
      let form = $("#send-price-form")[0];
      let data = new FormData(form);
      $.ajax({
        type: "post",
        url: "/admin/getprice",
        data: data,
        enctype: "multipart/form-data",
        processData: false,
        contentType: false,
        cache: false,
        dataType: "json",
        success: function (response) {
          let resp = response;          
          let validStatus = resp.valid_status;
          let xlsValid = resp.xls_valid;
          let uploadStatus = resp.upload_status;
          if (validStatus) {
            if (uploadStatus) {
              $("#upload-price-result").html(
                `<div class="alert alert-success">Данные из прайса успешно загружены в базу данных</div>`
              );
            } else {
              $("#upload-price-result").html(
                `<div class="alert alert-danger">Что-то пошло не так. Данные не загружены</div>`
              );
            }
          } else {
            $("#xls-valid").html(
              `<span class="text-danger">${xlsValid}</span>`
            );
          }
          $("#send-price-input").val("");
        },
      });
    });
  });
  // Поиск комплектующих для редактирования и  загрузки картинок
  $(function () {
    $("#admin_search .search-valid-message").hover(function (e) {
      $("#admin_search .search-valid-message").html("");
    });
    $("#admin_search .search-result-message").hover(function (e) {
      $("#admin_search .search-result-message").html("");
    });
    $("#admin_search input").focus(function (e) {
      $("#admin_search .search-result-list").html("");
    });
    $("#admin_search button").click(function (e) {
      let textSearch = $("#admin_search input").val();
      $.ajax({
        method: "POST",
        url: "admin/search-for-edit",
        data: {
          textSearch: textSearch,
        },
        success: function (response) {
          $("#admin_search input").val("");
          let resp = response;          
          let validStatus = resp.valid_status;
          let searchStatus = resp.search_status;
          let searchValidMessage = resp.search_valid_message;
          let searchMessage = resp.search_message;
          if (!validStatus) {
            $("#admin_search .search-valid-message").html(searchValidMessage);
          }
          if (searchStatus) {
            let partsSearchRes = JSON.parse(resp.parts_search_res);
            $("#admin_search .search-result-message").html(searchMessage);
            $.each(partsSearchRes, function (index, part) {
              $("#admin_search .search-result-list").append(        
                `<li>
                  <span>${part.part_h}</span>
                  <a href="admin/part-for-img/${part.part_slug}#start">Загрузка картинок</a>
                  <a class="text-danger" href="admin/part-for-delete/${part.part_slug}#start">Удалить</a>                     
                </li>`
              );
            });
          }
        },
      });
    });
  });
  // Загрузка фото
  $(function () {
    $("#send-photo2-input").click(function (e) {
      $("#photo2-valid").html("");
    });
    $("#send-photo1-input").click(function (e) {
      $("#photo1-valid").html("");
    });
    $("#send-photo-button").click(function (e) {
      e.preventDefault();
      let form = $("#send-photo-form")[0];
      let data = new FormData(form);
      $.ajax({
        type: "post",
        url: "/admin/getphoto",
        data: data,
        enctype: "multipart/form-data",
        processData: false,
        contentType: false,
        cache: false,
        dataType: "json",
        success: function (response) {
          let resp = response; 
          console.log(resp);       
          let validStatus = resp.valid_status;
          let photo1Valid = resp.photo1_valid;
          let photo2Valid = resp.photo2_valid;
          let uploadStatus = resp.upload_status;
          if (validStatus) {
            if (uploadStatus) {
              $("#upload-photo-result").html(
                `<div class="alert alert-success">Фотографии успешно загружены</div>`
              );
            } else {
              $("#upload-photo-result").html(
                `<div class="alert alert-danger">Что-то пошло не так. Фотографии не загружены</div>`
              );
            }
          } else {
            $("#photo1-valid").html(
              `<span class="text-danger">${photo1Valid}</span>`
            );
            $("#photo2-valid").html(
              `<span class="text-danger">${photo2Valid}</span>`
            );
          }
        },
      });
    });
    $("#upload-photo-result").click(function (e) {
      $("#upload-photo-result").html("");
      location.reload(true);
    });
  });
  // Подтверждение удаления part
  $(function () {
    $('.delete-part .delete-result').click(function () {
      $('.delete-part .delete-result').html('');      
    })   
      
    $('.delete-part button.delete-button').on('click', function () {       
      let buttonValue = $('.delete-part button.delete-button').val();
      let values = {                
        id: buttonValue,
        action: 'delete'
      } 
      $.ajax({
        url: '/admin/delete-part',
        method: 'post',
        dataType: 'html',
        data: values,
        success: function (response) {
          let resp = JSON.parse(response);                            
          let responseSuccesse = resp.response_successe;
          let responseFail = resp.response_fail;
          if (responseSuccesse) {
            $('.delete-part .delete-result').html(`<div class="alert alert-success">${responseSuccesse}</div>`);
          }
          if (responseFail) {
            $('.delete-part .delete-result').html(`<div class="alert alert-danger">${responseFail}</div>`);
          }          
        }
      })
    })    
  }) 
  // Smart-search
  $(function () {
    // stage-1
    $("#smart_search select").hover(function (e) {
      $("#smart_search .text-danger").html("");
    });
    $(".smart-search-result .btn-danger").click(function (e) {
      $('.smart-search-overlay').removeClass('show');
      $(".smart-search-result button.prev").removeClass("show");
      $(".smart-search-result button.next").removeClass("show");
      $(".smart-search-result .categories__list.first").html("");
      $(".smart-search-result .categories__list.sec").html("");
      $(".smart-search-result .search-res-message").html("");
      $(".smart-search-result .action-message").html("");
      $(".smart-search-result").removeClass("show");
      $(".smart-search-result .search-res-message").removeClass("hidden");
    });
    $("#smart_search button.stage-1").click(function (e) {
      let partKind = $("#smart_search select").val();
      let textSearch = $("#smart_search input").val();
      $.ajax({
        method: "POST",
        url: "/part-kind-search",
        data: {
          part_kind: partKind,
          text_search: textSearch,
        },
        success: function (response) {
          let resp = response;          
          if (!resp.valid_status) {
            let partKindValidMessage = resp.part_kind_valid_message;
            $("#smart_search .text-danger").html(partKindValidMessage);
          } else {
            $(".smart-search-result").addClass("show");
            $('.smart-search-overlay').addClass('show');
            $(".smart-search-result .categories__list.first").addClass("show");
            let actionNumb = resp.action_numb;
            let searchResMessage = resp.search_res_message;
            let actionMessage = resp.action_message;
            let arrData = resp.arr_data;
            $.each(arrData, function (index, item) {
              switch (true) {
                case actionNumb == 1:
                  $(".smart-search-result .categories__list.first").append(
                    `<li class="categories__item"><button type="submit" class="btn btn-info" data-action="${item.action}" data-first_param="${item.title}" data-second_param="${item.part_kind}">${item.title}</button></li>`
                  );
                  $(".smart-search-result .search-res-message").html(
                    searchResMessage
                  );
                  $(".smart-search-result .action-message").html(actionMessage);
                  break;
                case actionNumb == 3:
                  $(".smart-search-result .categories__list.first").append(
                    `<li class="categories__item"><button type="submit" class="btn btn-info stage-2" data-action="${item.action}" data-first_param="${item.part_sub_kind}" data-second_param="${item.part_kind}">${item.title}</button></li>`
                  );
                  $(".smart-search-result .search-res-message").html(
                    searchResMessage
                  );
                  $(".smart-search-result .action-message").html(actionMessage);
                  break;
                case actionNumb == 2 || actionNumb == 4:
                  $(".smart-search-result .categories__list.first").append(
                    `<li class="categories__item"><a class="btn btn-info" href="${item.link}#start">${item.title}</a></li>`
                  );
                  $(".smart-search-result .search-res-message").html(
                    searchResMessage
                  );
                  $(".smart-search-result .action-message").html(actionMessage);
                  break;
              }
            });
          }
        },
      });
    });
    // stage-2
    $(".smart-search-result .categories__list").click(function (e) {
      let elTarget = $(e.target);
      let firstParam = elTarget.attr("data-first_param");
      let secondParam = elTarget.attr("data-second_param");
      let action = elTarget.attr("data-action");
      $(".smart-search-result .categories__list").html("");
      $(".smart-search-result .search-res-message").html("");
      $(".smart-search-result .action-message").html("");
      $(".smart-search-result .categories__list.first").removeClass("show");
      $('.smart-search-overlay').removeClass('show');
      $(".smart-search-result").removeClass("show");
      $.ajax({
        method: "POST",
        url: action,
        data: {
          first_param: firstParam,
          second_param: secondParam,
        },
        success: function (response) {
          let resp = response;          
          let actionNumb = resp.action_numb;
          let searchResMessage = resp.search_res_message;
          let actionMessage = resp.action_message;
          let arrDataFirst = resp.arr_data[0];
          let arrDataSec = resp.arr_data[1];
          $(".smart-search-result").addClass("show");
          $('.smart-search-overlay').addClass('show');
          $(".smart-search-result .categories__list.first").addClass("show");
          $.each(arrDataFirst, function (index, item) {
            switch (true) {
              case actionNumb == 1:
                $(".smart-search-result .categories__list.first").append(
                  `<li class="categories__item"><button type="submit" class="btn btn-info" data-action="${item.action}" data-first_param="${item.title}" data-second_param="${item.part_kind}">${item.title}</button></li>`
                );
                $(".smart-search-result .search-res-message").html(
                  searchResMessage
                );
                $(".smart-search-result .action-message").html(actionMessage);
                break;
              case actionNumb == 3:
                $(".smart-search-result .categories__list.first").append(
                  `<li class="categories__item"><button type="submit" class="btn btn-info stage-2" data-action="${item.action}" data-first_param="${item.part_sub_kind}" data-second_param="${item.part_kind}">${item.title}</button></li>`
                );
                $(".smart-search-result .search-res-message").html(
                  searchResMessage
                );
                $(".smart-search-result .action-message").html(actionMessage);
                break;
              case actionNumb == 2 || actionNumb == 4:
                $(".smart-search-result .categories__list.first").append(
                  `<li class="categories__item"><a class="btn btn-info" href="${item.link}#start">${item.title}</a></li>`
                );
                $(".smart-search-result .search-res-message").html(
                  searchResMessage
                );
                $(".smart-search-result .action-message").html(actionMessage);
                break;
            }
          });
          if (arrDataSec) {
            $.each(arrDataSec, function (index, item) {
              $(".smart-search-result .categories__list.sec").append(
                `<li class="categories__item"><a class="btn btn-info" href="${item.link}#start">${item.title}</a></li>`
              );
            });
            $(".smart-search-result button.next").addClass("show");
          }
        },
      });
      $(".smart-search-result button.next").click(function (e) {
        $(".smart-search-result .categories__list.first").removeClass("show");
        $(".smart-search-result .categories__list.sec").addClass("show");
        $(".smart-search-result button.prev").addClass("show");
        $(".smart-search-result button.next").removeClass("show");
        $(".smart-search-result .search-res-message").addClass("hidden");
      });
      $(".smart-search-result button.prev").click(function (e) {
        $(".smart-search-result .categories__list.first").addClass("show");
        $(".smart-search-result .categories__list.sec").removeClass("show");
        $(".smart-search-result button.prev").removeClass("show");
        $(".smart-search-result button.next").addClass("show");
        $(".smart-search-result .search-res-message").removeClass("hidden");
      });
    });
  });
});
