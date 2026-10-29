var HOME = BASE_URL + "step_rule_check/";

function getItemTemplate() {
  let priceList = $('#price-list').val();

  $('#step-table').html('');

  if(priceList == "0") {
    $('#item').html('<option value="0" data-uom="">Select</option>');
    $('#item').select2();
  }
  else {
    load_in();

    $.ajax({
      url:HOME + 'get_item_template',
      type:'POST',
      cache:false,
      data:{
        'priceList' : priceList
      },
      success:function(rs) {
        load_out();

        if(isJson(rs)) {
          let ds = JSON.parse(rs);

          if(ds.status == 'success') {
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
      error:function(rs) {
        load_out();
        showError(rs);
      }
    })
  }
}

function getData() {
  let itemCode = $('#item').val().trim();
  let priceList = $('#price-list').val().trim();

  if(priceList.length == 0) {
    $('#price-list').hasError();
    return false;
  }

  if(itemCode.length == 0) {
    $('#item').hasError();
    return false;
  }

  load_in();

  $.ajax({
    url:HOME + 'get_data',
    type:'POST',
    cache:false,
    data:{
      'itemCode' : itemCode,
      'priceList' : priceList
    },
    success:function(rs) {
      load_out();
      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status == 'success') {
          let source = $('#step-template').html();
          let output = $('#step-table');

          render(source, ds.data, output);
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
      showError(rs);
    }
  })
}

function clearData() {
  $('#price-list').val('0');
  $('#step-table').html('');
  $('#item').html('<option value="0" data-uom="">Select</option>');
  $('#item').select2();
}
