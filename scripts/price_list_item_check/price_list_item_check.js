const HOME = `${BASE_URL}price_list_item_check/`;

function changePriceListType() {
  let pType = $('#price-list-type').val();
  let customer = $('#customer').val();
  let val = $('#priceList').val();
  let spId = $('#priceList option:selected').data('spid');

  load_in();

  $.ajax({
    url: `${HOME}get_price_list_by_type`,
    type: 'POST',
    cache: false,
    data: {
      'CardCode': customer,
      'type': pType
    },
    success: function (rs) {
      load_out();
      if (isJson(rs)) {
        let ds = JSON.parse(rs);
        $('#priceList').html(ds.priceList);
        $('#priceList').val('').trigger('change');

        if(val !== '') {
          let option = $('#priceList option[value="'+val+'"][data-spid="'+spId+'"]');
          option.prop('selected', true);

          getItemTemplate();          
        }
      }
      else {
        showError(rs);
      }
    },
    error: function (rs) {
      showError(rs);
    }
  })
}

function updatePriceList() {
  let customer = $('#customer').val();
  changePriceListType();
}

function resetItemList() {
  $('#item').html('<option value="">Select Item</option>');
  $('#item').select2();
}

function getItemTemplate() {  
  let isControl = $('#customer option:selected').data('control');
  let type = $('#customer option:selected').data('type');
  let priceList = $('#priceList').val();
  let spId = $('#priceList option:selected').data('spid');

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
      'spid': spId,
      'isControl': isControl,
      'customer_type': type
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
  let customer = $('#customer').val();
  let priceList = $('#priceList').val();
  let item = $('#item').val();
  let spId = $('#priceList option:selected').data('spid');

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
      'customer': customer,
      'priceList': priceList,
      'item': item,
      'spid': spId
    },
    success: function (rs) {
      load_out();
      if (isJson(rs)) {
        let ds = JSON.parse(rs);
        if (ds.status === 'success') {
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
    error: function (rs) {
      load_out();
      showError(rs);
    }
  })
}


function clearData() {
  window.location.href = HOME;
}