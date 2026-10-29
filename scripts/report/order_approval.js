var HOME = BASE_URL + 'report/approval_status/';

window.addEventListener('load', () => {
	resizeDisplay();
})

window.addEventListener('resize', () => {
	resizeDisplay();
});

function resizeDisplay() {
  let minHeight = 400;
	let height = $(window).height();
  let footerHeight = $('.footer-content').height();
	let pageContentHeight = height - footerHeight - 45 - 50;
  let titleHeight = $('.h-row').height();
  let filterHeight = $('.f-row').height();
  let paginationHeight = $('#pagination').height();
  let hrHeight = 10 + 10 + 20;
	let itemTableHeight = pageContentHeight - (titleHeight + filterHeight + paginationHeight + hrHeight);

  itemTableHeight = itemTableHeight < minHeight ? minHeight : itemTableHeight;

	$('.page-content').css('height', pageContentHeight + 'px');
	$('#item-div').css('height', itemTableHeight + 'px');
}


$('#fromDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#toDate').datepicker('option', 'minDate', sd);
  }
})


$('#toDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#fromDate').datepicker('option', 'maxDate', sd);
  }
})



function toggleAllCustomer(option) {
  $('#allCustomer').val(option);
  if(option == 1) {
    $('#btn-cus-all').addClass('btn-primary');
    $('#btn-cus-range').removeClass('btn-primary');
    $('#customerFrom').attr('disabled', 'disabled');
    $('#customerTo').attr('disabled', 'disabled');
    return;
  }

  if(option == 0) {
    $('#btn-cus-all').removeClass('btn-primary');
    $('#btn-cus-range').addClass('btn-primary');
    $('#customerFrom').removeAttr('disabled');
    $('#customerTo').removeAttr('disabled');
    $('#customerFrom').focus();
    return;
  }
}


$('#customerFrom').autocomplete({
  source:HOME + 'get_customer_code_and_name',
  autoFocus:true,
  close:function() {
    var data = $(this).val();
    var arr = data.split(' | ');
    if(arr.length == 2) {
      $(this).val(arr[0]);
      let from = arr[0];
      let to = $('#customerTo').val();
      if(to.length > 0 && to < from) {
        $(this).val(to);
        $('#customerTo').val(from);
      }
      else {
        $('#customerTo').focus();
      }
    }
    else {
      $(this).val('');
    }
  }
})


$('#customerTo').autocomplete({
  source:HOME + 'get_customer_code_and_name',
  autoFocus:true,
  close:function() {
    var data = $(this).val();
    var arr = data.split(' | ');
    if(arr.length == 2) {
      $(this).val(arr[0]);
      let from = $('#customerFrom').val();
      let to = arr[0];
      if(from.length > 0 && from > to) {
        $(this).val(from);
        $('#customerFrom').val(to);
      }
      else {
        $('#customerFrom').focus();
      }
    }
    else {
      $(this).val('');
    }
  }
})



function toggleAllCustomerGroup(option) {
  $('#allCustomerGroup').val(option);

  if(option == 1) {
    $('#btn-cg-all').addClass('btn-primary');
    $('#btn-cg-range').removeClass('btn-primary');
    $('#customer-group-modal').modal('hide');
    $('.chk-cg').prop('checked', false);
    return;
  }

  if(option == 0) {
    $('#btn-cg-all').removeClass('btn-primary');
    $('#btn-cg-range').addClass('btn-primary');
    $('#customer-group-modal').modal('show');
  }
}



function toggleAllApprover(option) {
  $('#allApprover').val(option);

  if(option == 1) {
    $('#btn-apv-all').addClass('btn-primary');
    $('#btn-apv-range').removeClass('btn-primary');
    $('#approver-modal').modal('hide');
    $('.chk-ap').prop('checked', false);
    return;
  }

  if(option == 0) {
    $('#btn-apv-all').removeClass('btn-primary');
    $('#btn-apv-range').addClass('btn-primary');
    $('#approver-modal').modal('show');
  }
}



function getReport() {
  let fromDate = $('#fromDate').val();
  let toDate = $('#toDate').val();
  let allCustomer = $('#allCustomer').val();
  let customerFrom = $('#customerFrom').val();
  let customerTo = $('#customerTo').val();
  let allCustomerGroup = $('#allCustomerGroup').val();
  let groupList = [];
  let allApprover = $('#allApprover').val();
  let approver = [];
  let approve_status = $('#approval_status').val();

  if(!isDate(fromDate) || !isDate(toDate)) {
    $('#fromDate').addClass('has-error');
    $('#toDate').addClass('has-error');
    swal('วันที่ไม่ถูกต้อง');
    return false;
  }
  else {
    $('#fromDate').removeClass('has-error');
    $('#toDate').removeClass('has-error');
  }

  if(allCustomer == 0 && (customerFrom.length == 0 || customerTo.length == 0)) {
    $('#customerFrom').addClass('has-error');
    $('#customerTo').addClass('has-error');
    swal('กรุณาระบุลูกค้า');
    return false;
  }
  else {
    $('#customerFrom').removeClass('has-error');
    $('#customerTo').removeClass('has-error');
  }

  if(allCustomerGroup == 0 && $('.chk-cg').is(':checked') == false) {
    $('#customer-group-modal').modal('show');
    return false;
  }

  if(allApprover == 0 && $('.chk-ap').is(':checked') == false) {
    $('#approver-modal').modal('show');
    return false;
  }

  if(allCustomerGroup == 0) {
    $('.chk-cg').each(function() {
      if($(this).is(':checked')) {
        groupList.push($(this).val());
      }
    })
  }

  if(allApprover == 0) {
    $('.chk-ap').each(function() {
      if($(this).is(':checked')) {
        approver.push($(this).val());
      }
    })
  }


  data = {
    "fromDate" : fromDate,
    "toDate" : toDate,
    "allCustomer" : allCustomer,
    "customerFrom" : customerFrom,
    "customerTo" : customerTo,
    "allCustomerGroup" : allCustomerGroup,
    "groupList" : groupList,
    "approver" : approver,
    "approval_status" : approve_status
  }

  load_in();
  $.ajax({
    url:HOME + 'get_report',
    type:'GET',
    cache:false,
    data:data,
    success:function(rs) {
      load_out();
      if(isJson(rs)) {
        var data = $.parseJSON(rs);
        var source = $('#report-template').html();
        var output = $('#result');

        render(source, data, output);
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

}



function doExport() {
  let fromDate = $('#fromDate').val();
  let toDate = $('#toDate').val();
  let allCustomer = $('#allCustomer').val();
  let customerFrom = $('#customerFrom').val();
  let customerTo = $('#customerTo').val();
  let allCustomerGroup = $('#allCustomerGroup').val();
  let groupList = [];
  let allApprover = $('#allApprover').val();
  let approver = [];
  let approve_status = $('#approval_status').val();

  if(!isDate(fromDate) || !isDate(toDate)) {
    $('#fromDate').addClass('has-error');
    $('#toDate').addClass('has-error');
    swal('วันที่ไม่ถูกต้อง');
    return false;
  }
  else {
    $('#fromDate').removeClass('has-error');
    $('#toDate').removeClass('has-error');
  }

  if(allCustomer == 0 && (customerFrom.length == 0 || customerTo.length == 0)) {
    $('#customerFrom').addClass('has-error');
    $('#customerTo').addClass('has-error');
    swal('กรุณาระบุลูกค้า');
    return false;
  }
  else {
    $('#customerFrom').removeClass('has-error');
    $('#customerTo').removeClass('has-error');
  }

  if(allCustomerGroup == 0 && $('.chk-cg').is(':checked') == false) {
    $('#customer-group-modal').modal('show');
    return false;
  }

  if(allApprover == 0 && $('.chk-ap').is(':checked') == false) {
    $('#approver-modal').modal('show');
    return false;
  }


  var token = $('#token').val();
  get_download(token);

  $('#reportForm').submit();

}
