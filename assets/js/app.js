$(document).ready(function () {
  $('#sidebarCollapse').on('click', function () {
    $('#sidebar').toggleClass('active');
    $('#sidebar').toggleClass('active-small');
    $("#content").toggleClass('ml');
    $("#content").toggleClass('ml-small');
    $(".header").toggleClass('lg-desc-2');

  });

  // Datatable
  $('#dataTable').DataTable({
    scrollX: true,
    "order": [],
    "pageLength" : 25,
  });

  // Dropdown Toggle
  $('#dropdownMenuButton1').click(function() {
    dropDownFixPosition($('#dropdownMenuButton1'), $('.dropdown-menu'));
  })

  function dropDownFixPosition(button, dropdown) {
    var dropdownTop = button.offset().top + button.outerHeight();

    dropdown.css('top', dropdownTop + "px");
    dropdown.css('left', button.offset().left + "px");
  }

  // Tooltip
  $(function () {
    $('[data-toggle="tooltip"]').tooltip()
  })


  // Set Current Menu Active in Sidebar
  $('.sidebar-content a').each(function () {
    var current_path = window.location.href.split('/').pop();
    if(current_path == ""){
      $('.sidebar-content li:eq(0)').addClass('text-dark-blue');
    }
    if(current_path === "index.php") {
      if($(this).attr('href') === current_path) {
        $(this).find('.menu-icon').addClass('text-dark-blue');
        $(this).find('.menu-title').addClass('text-dark-blue');
      }
    }else {
      if($(this).attr('href') === current_path) {
        $(this).find('.menu-icon').addClass('text-dark-blue');
        $(this).find('.menu-title').addClass('text-dark-blue');

        var menu_icon_main = $('.menu-icon:eq(0)');
        menu_icon_main.find('path:eq(0)').css('fill', 'var(--light-blue)')
        menu_icon_main.find('path:eq(1)').css('fill', '#bebebe')
        menu_icon_main.find('path:eq(2)').css('fill', 'var(--light-blue)')
        menu_icon_main.find('path:eq(3)').css('fill', '#bebebe')
        $('.menu-title:eq(0)').css('color', 'var(--light-blue)')
      }
    }
  })

  // Show/Hide Modals
  let moduleSelected;
  $('.module').each(function () {
    $(this).click(function () {
      moduleSelected = $(this);
      $('#moduleModal').modal('show');
    });
  })

  $('#moduleModal-select').click(function () {
    $('#moduleModal').modal('hide');
    $('#flowtypeModal').modal('show');
  })

  $('#flowtypeModal-select').click(function () {
    $('#flowtypeModal').modal('hide');

    var flowtype = $("#flowtype-select").val();

    if(flowtype === "pre_sfgi") {
      $('#moduleFormModal').modal('show');
      $('#moduleForm2Modal').find("select").prop("disabled", true)
      $('#moduleForm2Modal').find("input").prop("disabled", true)

      $('#moduleFormModal').find("select").prop("disabled", false)
      $('#moduleFormModal').find("input").prop("disabled", false)
    } else {
      $('#moduleForm2Modal').modal('show');
      $('#moduleFormModal').find("select").prop("disabled", true)
      $('#moduleFormModal').find("input").prop("disabled", true)

      $('#moduleForm2Modal').find("select").prop("disabled", false)
      $('#moduleForm2Modal').find("input").prop("disabled", false)
    }
  })

  $('#moduleFormModal-clear').click(function () {
    $('#moduleFormModal').find('select').each(function () {
      $(this).first().prop('selectedIndex', 0)
    })
  })

  $('#moduleForm2Modal-clear').click(function () {
    $('#moduleForm2Modal').find('select').each(function () {
      $(this).first().prop('selectedIndex', 0)
    })

    $(".ppv_type").empty()
    $(".ppv_type").append('<option selected disabled="disabled">Select</option>');

    $(".ppv_platform_name").empty()
    $(".ppv_platform_name").append('<option selected disabled="disabled">Select</option>');
  })

  $('.mail-btn').click(function () {
    $('#emailModal').modal('show')
  })

  $('#moduleFormModal-add').click(function () {
    $('#moduleFormModal').find('select').each(function () {
      if(this.selectedIndex === 0) {
        moduleSelected.css('border', '2px solid #ff5454');
        moduleSelected.find('img').css('filter', 'grayscale(100%) brightness(40%) sepia(100%) hue-rotate(-50deg) saturate(600%) contrast(1.5)');
        $('#moduleFormModal').modal('hide');
        $('#btn-disabled').prop('disabled', true);
      } else {
        moduleSelected.css('border', '2px solid #00FF00');
        moduleSelected.find('img').css('filter', 'grayscale(100%) brightness(40%) sepia(100%) hue-rotate(100deg) saturate(600%) contrast(3)');
        $('#moduleFormModal').modal('hide');
        $('#btn-disabled').prop('disabled', false);
      }
    })
  })

  $('#moduleForm2Modal-add').click(function () {
    var ptp = $("#ptp").val()

    if(ptp == 0) {
      $('#moduleForm2Modal').find('select').not(".tester_platform select").each(function () {
        if(this.selectedIndex === 0) {
          moduleSelected.css('border', '2px solid #ff5454');
          moduleSelected.find('img').css('filter', 'grayscale(100%) brightness(40%) sepia(100%) hue-rotate(-50deg) saturate(600%) contrast(1.5)');
          $('#moduleForm2Modal').modal('hide');
          $('#btn-disabled').prop('disabled', true);
        } else {
          moduleSelected.css('border', '2px solid #00FF00');
          moduleSelected.find('img').css('filter', 'grayscale(100%) brightness(40%) sepia(100%) hue-rotate(100deg) saturate(600%) contrast(3)');
          $('#moduleForm2Modal').modal('hide');
          $('#btn-disabled').prop('disabled', false);
        }
      })
    } else {
      $('#moduleForm2Modal').find('select').each(function () {
        if(this.selectedIndex === 0) {
          moduleSelected.css('border', '2px solid #ff5454');
          moduleSelected.find('img').css('filter', 'grayscale(100%) brightness(40%) sepia(100%) hue-rotate(-50deg) saturate(600%) contrast(1.5)');
          $('#moduleForm2Modal').modal('hide');
          $('#btn-disabled').prop('disabled', true);
        } else {
          moduleSelected.css('border', '2px solid #00FF00');
          moduleSelected.find('img').css('filter', 'grayscale(100%) brightness(40%) sepia(100%) hue-rotate(100deg) saturate(600%) contrast(3)');
          $('#moduleForm2Modal').modal('hide');
          $('#btn-disabled').prop('disabled', false);
        }
      })
    }
  })

  $('#clear-admin').click( function () {
    $('#form-location').find('input').each(function () {
      $(this).val('');
    })
  })

  // Show Search Output on Code Search
  $('#show-result').click(function () {
    let opCode = $('#op_code').val();
    if(opCode == '') {
      $('#opcode-error').removeClass('d-none');
      return;
    }

    $('#opcode-error').addClass('d-none');
    $.ajax({
      type: "POST",
      url: 'includes/ajax.php',
      data: {opCode},
      success: function(response)
      {
        if(response !== 'false') {
          var jsonData = JSON.parse(response);
          $('#opcode-result').text(jsonData.location_code);
          $('#optype-result').text(jsonData.optype);
          $('#desc-result').text(jsonData.description);
          $('#result-card').removeClass('d-none');
          $('#opcode-error-result').addClass('d-none');
        } else {
          $('#result-card').addClass('d-none');
          $('#opcode-error-result').removeClass('d-none');
        }
      }
    });
  })

  var insertions = 1;
  $("#add-insertion").click(function () {
    insertions++;
    $(".tester_platform").append(`<div class="tester_platform_inner"><p class="f-14 mb-0 pb-0 w-500">PPV Manufacturing Flow STD (Insertion ${insertions})</p>
                                            <select class="form-select mb-3 pf-${insertions} ppv_selects ppv_flow" name="ppv_flow[]" id="pf">
                                                <option disabled selected>Select</option>
                                            </select>

                                            <p class="f-14 mb-0 pb-0 w-500">PPV Type</p>
                                            <select class="form-select mb-3 ppv_selects ppv_type ptt-${insertions}" name="ppv_type[${insertions}][]" id="pt">
                                                <option disabled selected>Select</option>
                                            </select>
                                            
                                            <div class="add-ppv-type">
                                                <button class="mb-3" id="add-ppv-type" type="button" style="width: 30px; height: 30px; background-color: var(--yellow); border: none; outline: none; font-size: 20px; font-weight: bold; border-radius: 5px; color: #ffffff">+</button>
                                            </div>

                                            <p class="f-14 mb-0 pb-0 w-500">Platform Name</p>
                                            <select class="form-select mb-3 ppv_platform_name pn-${insertions}" name="platform_names[]" id="pn">
                                                <option disabled selected>Select</option>
                                            </select>
                                            
                                            <div>
                                            <button class="mb-3 delete-insertion btn fw-bold" type="button">Delete insertion</button>
                                            </div></div>
                                            `);

    for (var i = 0; i < ppv_flow_stds.length; i++) {
      var optionElement = $("<option>");
      optionElement.attr("value", ppv_flow_stds[i].id);
      optionElement.text(ppv_flow_stds[i].name);

      // Append the option element to the select element
      $(`.pf-${insertions}`).append(optionElement);
    }

    // for (var i = 0; i < ppvs.length; i++) {
    //   var optionElement = $("<option>");
    //   optionElement.attr("value", ppvs[i].id);
    //   optionElement.text(ppvs[i].ppv_type);
    //
    //   // Append the option element to the select element
    //   $(`.ptt-${insertions}`).append(optionElement);
    //
    //   optionElement = $("<option>");
    //   optionElement.attr("value", ppvs[i].id);
    //   optionElement.text(ppvs[i].platform_name);
    //
    //   // Append the option element to the select element
    //   $(`.pn-${insertions}`).append(optionElement);
    // }
  })

  var types = 1;
  $("body").on("click", ".add-ppv-type", function () {
    types++;
    $(`<div class="ppv-types"><p class="f-14 mb-0 pb-0 w-500">PPV Type</p><select class="form-select mb-3 ppv_selects ppv_type pttt-${types}" name="ppv_type[${$(".add-ppv-type").index($(this)) + 1}][]" id="pt">
                                                <option disabled selected>Select</option>
                                            </select>
                                            <button class="mb-3 delete-ppv-type" type="button" style="width: 30px; height: 30px; background-color: var(--yellow); border: none; outline: none; font-size: 20px; font-weight: bold; border-radius: 5px; color: #ffffff">-</button>
                                            </div>
                                            `).insertBefore($(this));

    for (var i = 0; i < ppvs.length; i++) {
      var optionElement = $("<option>");
      optionElement.attr("value", ppvs[i].id);
      optionElement.text(ppvs[i].ppv_type);

      // Append the option element to the select element
      $(`.pttt-${types}`).append(optionElement);
    }
  })

  $("#ptp").on("change", function () {
    if($(this).val() == 0) {
      $(".tester_platform").addClass("d-none")
      $(".add-insertion-div").addClass("d-none")
      $(".add-ppv-type").addClass("d-none")
    } else {
      $(".tester_platform").removeClass("d-none")
      $(".add-insertion-div").removeClass("d-none")
      $(".add-ppv-type").removeClass("d-none")
    }
  })

  $("body").on('click', '.delete-insertion', function () {
    $(this).closest(".tester_platform_inner").remove()
  })

  $("body").on('click', '.delete-ppv-type', function () {
    $(this).closest(".ppv-types").remove()
  })
});