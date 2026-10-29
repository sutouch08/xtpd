var HOME = `${BASE_URL}report/customer_status/`;

function getReport() {       
  load_in();

  $.ajax({
    url:`${HOME}get_report`,
    type:'GET',
    cache:false,    
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);
        let source = $('#report-template').html();
        let output = $('#result');
        render(source, ds, output);
      }
      else {
        showError(rs);
      }
    },
    error:function(rs) {
      showError(rs);
    }
  });
}


function doExport() {  
  var token = $('#token').val();
  get_download(token);
  $('#reportForm').submit();
}


function openRequestForm(id) {
  const el = $(`#btn-request-${id}`);
  const h = {
    'code': el.data('code'),
    'name': el.data('name'),
    'duration': el.data('duration'),
    'invoiceCount': el.data('invoice'),
    'createDate':el.data('create')
  };

  $('#customer-title').text(`${h.code} : ${h.name}`);
  $('#create-date').val(h.createDate);
  $('#duration').val(h.duration);
  $('#invoice-count').val(h.invoiceCount);
  $('#customer-code').val(h.code);
  $('#customer-name').val(h.name);
  $('#customer-id').val(id);
  $('#request-modal').modal('show');
  // You can add additional logic here to populate the modal with relevant data
  dragElement('request-modal', 'request-header');
}


function submitRequest() {
  $('#payment-error').val('');
  $('#billing-error').val('');

  const req = {
    'customerCode': $('#customer-code').val(),
    'customerName': $('#customer-name').val(),
    'customerId': $('#customer-id').val(),
    'paymentMethod': $('input[name="payment-method"]:checked').val(),
    'paymentBilling': $('input[name="payment-billing"]:checked').val(),
    'billingDate': $('#billing-date').val(),
    'billingText': '',
    'paymentDate': $('#payment-date').val(),
    'paymentText': '',
    'createDate': $('#create-date').val(),
    'duration': $('#duration').val(),
    'invoiceCount': $('#invoice-count').val()
  };

  if(!req.paymentMethod) {
    $('#payment-error').val('กรุณาเลือกวิธีการชำระเงิน');
    return;
  }

  if(!req.paymentBilling) {
    $('#billing-error').val('กรุณาเลือกขั้นตอนการรับชำระเงินของลูกค้า');
    return;
  }

  if(req.paymentBilling == '2' && !req.billingDate) {
    $('#billing-error').val('กรุณาระบุวันที่วางบิล');
    return;
  }

  if(!req.paymentDate) {
    $('#billing-error').val('กรุณาระบุรอบการชำระเงิน');
    return;
  }

  if(req.paymentBilling == '2' && req.billingDate) {
    req.billingText = `วางบิลทุกวัน ${req.billingDate} ของเดือน`;
  }
  else {
    req.billingText = `ไม่วางบิล`;
  }

  if(req.paymentDate) {
    req.paymentText = `ชำระเงินทุกวัน ${req.paymentDate} ของเดือน`;
  }

  // If all validations pass, you can proceed to send the request

  $('#request-modal').modal('hide');

  load_in();

  $.ajax({
    url: `${HOME}send_request`,
    type: 'POST',
    data: req,
    success: function(rs) {
      load_out();
      if(isJson(rs)) {
        let ds = JSON.parse(rs);
        if(ds.status === 'success') {
          swal({
            title:'ส่งคำขอเรียบร้อยแล้ว',
            type:'success',
            timer:1000
          });

          $('#row-' + req.customerId).remove();
          reIndex();
        }
        else {
          showError(ds.message);
        }
      }
      else {
        showError(rs);
      }
    },
    error: function(rs) {
      showError(rs);
    }
  });
}
