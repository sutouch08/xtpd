var HOME = BASE_URL + "special_price_list/";
var click = 0;

function goBack() {
  window.location.href = HOME;
}


function addNew() {
  window.location.href = `${HOME}add_new`;
}


function edit(id) {
  setTimeout(() => {
    load_in();
  }, 200);

  window.location.href = `${HOME}edit/${id}`;
}


function editItem(item_id, pop = false) {  
  if(pop) {
    const url = `${HOME}edit_item/${item_id}?nomenu&nonavbar`;
    const width = 1000;
    const height = 680;
    const center = ($(document).width() - width) / 2;
    const prop = `width=${width}, height=${height}, left=${center}, top=100 scrollbars=yes`;
    window.open(url, '_blank', prop);
  }
  else {
    window.location.href = `${HOME}edit_item/${item_id}`;
  }
}


function viewItem(item_id) {
  const url = `${HOME}view_item/${item_id}?nomenu&nonavbar`;
  let width = 1000;
  let height = 680;
  let center = ($(document).width() - width)/2;
  let prop = `width=${width}, height=${height}, left=${center}, top=100 scrollbars=yes`;
  window.open(url, '_blank', prop);
}


function viewDetail(id) {
  window.location.href = `${HOME}view_detail/${id}`;
}

function changeURL(id, tab) {
  var url = `${HOME}edit/${id}/${tab}`;
  var stObj = { stage: 'stage' };
  window.history.pushState(stObj, 'price_list', url);
}

const bindDateTimeRange = (fromSelector, toSelector) => {
  const fromInput = document.querySelector(fromSelector);
  const toInput = document.querySelector(toSelector);

  if (!fromInput || !toInput) return;

  // เมื่อ from เปลี่ยนค่า
  fromInput.addEventListener("change", () => {
    const fromVal = fromInput.value;
    const toVal = toInput.value;

    // อัปเดต min ของ to
    toInput.min = fromVal;

    // ถ้า to น้อยกว่า from → ปรับให้เท่ากับ from
    if (toVal && toVal < fromVal) {
      toInput.value = fromVal;
    }
  });

  // เมื่อ to เปลี่ยนค่า
  toInput.addEventListener("change", () => {
    const fromVal = fromInput.value;
    const toVal = toInput.value;

    // อัปเดต max ของ from
    fromInput.max = toVal;

    // ถ้า from มากกว่า to → ปรับให้เท่ากับ to
    if (fromVal && fromVal > toVal) {
      fromInput.value = toVal;
    }
  });
};

$('#fromDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#toDate').datepicker('option', 'minDate', sd);
  }
});

$('#toDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#fromDate').datepicker('option', 'maxDate', sd);
  }
});

function update_uom(el) {
  let uom = $('#item option:selected').data('uom');

  $('#uom').val(uom);
}

function add() {
  if(click == 0) {
    click = 1;

    $('.e').removeClass('has-error');

    let h = {
      'name': $('#name').val(),
      'type' : $('#type').val(),
      'start_date' : $('#start-date').val(),
      'end_date' : $('#end-date').val(),
      'active': $('input[name="status"]:checked').val()
    }
   
    if (h.name == "") {
      $('#name').hasError();
      click = 0;
      return false;
    }

    if(h.type == "") {
      $('#type').hasError();
      click = 0;
      return false;
    }

    if(h.start_date == "") {
      $('#start-date').hasError();
      click = 0;
      return false;
    }

    if(h.end_date == "") {
      $('#end-date').hasError();
      click = 0;
      return false;
    }

    load_in();

    $.ajax({
      url: `${HOME}add`,
      type: 'POST',
      cache: false,
      data: {
        'data': JSON.stringify(h)
      },
      success: function (rs) {
        load_out();

        if (isJson(rs)) {
          let ds = JSON.parse(rs);

          if (ds.status.trim() === 'success') {
            edit(ds.id);
          }
          else {
            showError(rs);
          }
        }
        else {
          showError(rs);
        }
      },
      error: function (rs) {
        load_out();
        showError(rs);
      }
    })
  }  
}


function update() {
  clearErrorByClass('e');

  let h = {
    'id': $('#id').val(),
    'name': $('#name').val(),
    'type': $('#type').val(),
    'start_date': $('#start-date').val(),
    'end_date': $('#end-date').val(),
    'active': $('input[name="status"]:checked').val(),
    'all_customer': $('input[name="all_customer"]:checked').val(),
    'customer_groups': []
  }
  
  if(h.name.length == 0) {
    $('#name').hasError();
    return false;
  }

  if(h.type == "") {
    $('#type').hasError();
    return false;
  }

  if(h.start_date == "") {
    $('#start-date').hasError();
    return false;
  }

  if(h.end_date == "") {
    $('#end-date').hasError();
    return false;
  }

  $('.cus-chk:checked').each(function() {
    let id = $(this).val();
    h.customer_groups.push(id);
  });

  if(h.all_customer == 0 && h.customer_groups.length == 0) {
    swal({
      title: 'Error!',
      text: 'Please select at least one customer group',
      type: 'error'
    });

    return false;
  }

  load_in();

  $.ajax({
    url:`${HOME}update`,
    type:'POST',
    cache:false,
    data:{
      'data' : JSON.stringify(h)
    },
    success:function(rs) {
      load_out();

      if(rs.trim() == 'success') {
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });

        setTimeout(() => {
          window.location.reload();
        }, 1200);
      }
      else {
        showError(rs);
      }
    },
    error:function(rs) {
      load_out();
      showError(rs);
    }
  })
}


function getDelete(id, name){
  swal({
    title:'Are sure ?',
    text:`Do you really want to delete ${name} ? <br/> This process cannot be undone.`,
    type:'warning',
    showCancelButton: true,
    confirmButtonColor: '#FA5858',
    confirmButtonText: 'Delete',
    cancelButtonText: 'Cancle',
    closeOnConfirm: false,
    html:true
  },function(){
    $.ajax({
      url: HOME + 'delete',
      type:'POST',
      cache:false,
      data:{
        'id' : id
      },
      success:function(rs){
        if(rs == 'success'){
          swal({
            title:'Success',
            text:'Price list has been deleted',
            type:'success',
            time: 1000
          });

          setTimeout(function(){
            window.location.reload();
          }, 1200)
        }
        else {
          swal({
            title:'Error!',
            text:rs,
            type:'error'
          });
        }
      }
    })
  })
}


function addItem() {
  load_in();
  let id = $('#id').val();
  let itemCode = $('#item').val();
  let itemName = $('#item option:selected').text();

  if(itemCode.length == 0 || itemName.length == 0) {
    $('#item').hasError();
    return false;
  }

  load_in();

  $.ajax({
    url:`${HOME}add_item`,
    type:'POST',
    cache:false,
    data:{
      'id' : id,
      'itemCode' : itemCode,
      'itemName' : itemName
    },
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status == 'success') {
          let source = $('#item-row-template').html();
          let output = $('#item-table');
          render_append(source, ds, output);
          $('#item').val('');
          $('#uom').val('');
          reIndex();

          editItem(ds.id, true);
        }
        else {
          showError(ds.message);
        }
      }
      else {
        showError(rs);
      }
    },
    error:function(rs) {
      load_out();
      showError(rs);
    }
  })
}


function saveItem() {
  clearErrorByClass('e');

  let err = 0;
  let id = $('#id').val();
  let uom = $('#uom').val();

  let h = {
    'id' : id,    
    'rows' : []
  }


  if($('.chk').length) {
    $('.chk').each(function() {
      let no = $(this).data('no');
      let name = $('#step-name-'+no).val().trim();
      let stepQty = parseDefault(parseFloat($('#step-qty-'+no).val()), 0);
      let sellPrice = parseDefault(parseFloat($('#sell-price-'+no).val()), 0);
      let freeQty = parseDefault(parseFloat($('#free-qty-'+no).val()), 0);
      let pos = parseDefault(parseFloat($('#pos-'+no).val()), 10);      

      if(stepQty > 0 && sellPrice > 0) {
        if(stepQty <= 0) {
          $('#step-qty-'+no).hasError();
          err++;
        }

        if(sellPrice <= 0) {
          $('#sell-price-'+no).hasError();
          err++;
        }

        if(freeQty < 0) {
          $('#free-qty-'+no).hasError();
          err++;
        }

        let row = {
          'step_id' : id,
          'name' : name,
          'Qty' : stepQty,
          'SellPrice' : sellPrice,
          'freeQty' : freeQty,
          'position' : pos
        };

        h.rows.push(row);
      }
    });
  }

  if(err > 0) {
    return false;
  }

  load_in();

  $.ajax({
    url:`${HOME}save_item`,
    type:'POST',
    cache:false,
    data:{
      'data' : JSON.stringify(h)
    },
    success:function(rs) {
      load_out();

      if(rs.trim() == 'success') {
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });

        setTimeout(() => {
          window.location.reload();
        }, 1200);
      }
      else {
        swal({
          title:'Error!',
          text:rs,
          type:'error',
          html:true
        })
      }
    },
    error:function(rs) {
      load_out();
      swal({
        title:'Error!',
        text:rs.responseText,
        type:'error',
        html:true
      })
    }
  })
}


function deleteItem(id, name){
  swal({
    title:'Are sure ?',
    text:`Do you really want to delete ${name} ? <br/> This process cannot be undone.`,
    type:'warning',
    showCancelButton: true,
    confirmButtonColor: '#FA5858',
    confirmButtonText: 'Delete',
    cancelButtonText: 'Cancle',
    closeOnConfirm: false,
    html:true
  },function(){
    $.ajax({
      url: `${HOME}delete_item`,
      type:'POST',
      cache:false,
      data:{
        'id' : id
      },
      success:function(rs){
        if(rs == 'success'){
          swal({
            title:'Success',
            text:'Item has been deleted',
            type:'success',
            time: 1000
          });

          setTimeout(function(){
            window.location.reload();
          }, 1200)
        }
        else {
          swal({
            title:'Error!',
            text:rs,
            type:'error'
          });
        }
      }
    })
  })
}


function addRow() {
  let no = parseDefault(parseFloat($('#top-row').val()), 0);

  let source = $('#row-template').html();
  let ds = {'no' : no};
  let output = $('#detail-table');

  render_append(source, ds, output);
  no++;
  $('#top-row').val(no);

  reIndex();
}


function removeRow() {
  if($('.chk:checked').length) {
    $('.chk:checked').each(function() {
      let id = parseDefault(parseInt($(this).data('id')), 0);
      let no = $(this).data('no');

      if(id > 0) {
        let source = $('#delete-template').html();
        let data = {'row_id' : id};
        let output = $('#deleted-table');

        render_append(source, data, output);
      }

      $('#row-'+no).remove();

      reIndex();
    });
  }
}

function toggleActive(id, el) {
  let active = el.checked ? 1 : 0;

  $.ajax({
    url: `${HOME}setActive`,
    type: 'POST',
    cache: false,
    data: {
      'id': id,
      'active': active
    },
    success: function (rs) {
      load_out();
      if (rs.trim() != 'success') {
        showError(rs);
        el.checked = !el.checked;
      }
    },
    error: function (rs) {
      showError(rs);
    }
  });
}


function toggleActiveItem(id, el) {
  let active = el.checked ? 1 : 0;

  $.ajax({
    url: `${HOME}setActiveItem`,
    type: 'POST',
    cache: false,
    data: {
      'id': id,
      'active': active
    },
    success: function (rs) {
      load_out();
      if (rs.trim() != 'success') {
        showError(rs);
        el.checked = !el.checked;
      }
    },
    error: function (rs) {
      showError(rs);
    }
  });
}

function toggleCustomerSelection(value) {
  if(value == 1) {
    $('#customer-selection').addClass('hidden');
  }
  else {
    $('#customer-selection').removeClass('hidden');
  }
}

function checkAllCustomerGroups(el) {
  if(el.is(':checked')) {
    $('.cus-chk').prop('checked', true);
  }
  else {
    $('.cus-chk').prop('checked', false);
  }
}


function checkAll() {
  if($('#check-all').is(':checked')) {
    $('.chk').prop('checked', true);
  }
  else {
    $('.chk').prop('checked', false);
  }
}

function getImportTemplate() {
  const url = `${HOME}get_import_template`;
  window.location.href = url;
}
