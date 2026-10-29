const HOME = `${BASE_URL}product_lot_check/`;

function resetItemList() {
  $('#item').html('<option value="">Select Item</option>');
  $('#item').select2();
}

function getItemTemplate() {
  let pl = $('#priceList');
  let priceList = pl.val();
  let spid = pl.find(':selected').data('spid');
  
  if(priceList === '') {
    resetItemList();
    return false;
  }
  
  load_in();

  $.ajax({
    url: `${HOME}get_item_template`,
    type: "POST",
    cache: false,
    data: {
      'priceList': priceList,
      'spid': spid
    },
    success: function (rs) {
      load_out();

      if (isJson(rs)) {
        let ds = JSON.parse(rs);
        if (ds.status === 'success') {
          $('#item').html(ds.template);
          $('#item').select2();
        }
        else {
          showError(ds.message);
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

function getData() {
  clearErrorByClass('r');    
  let priceList = $('#priceList').val();  
  let item = $('#item').val();  

  if(priceList === '') {
    $('#priceList').hasError();
    return false;
  }

  if(item === '') {
    $('#item').hasError();
    return false;
  }

  load_in();

  $.ajax({
    url: `${HOME}get_data`,
    type: "POST",
    cache: false,
    data: {      
      'item': item
    },
    success: function (rs) {
      load_out();
      if (isJson(rs)) {
        let ds = JSON.parse(rs);
        if (ds.status === 'success') {
          let source = $('#item-template').html();
          let output = $('#item-table');
          render(source, ds.data, output);
        }
        else {
          $('#item-table').html('<tr><td colspan="6" class="text-center">--- No Data ---</td></tr>');
          showError(ds.message);
        }
      }
      else {
        $('#item-table').html('<tr><td colspan="6" class="text-center">--- No Data ---</td></tr>');
        showError(rs);
      }
    },
    error: function (rs) {      
      showError(rs);
    }
  })
}


function clearData() {
  window.location.href = HOME;
}