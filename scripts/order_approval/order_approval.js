var HOME = `${BASE_URL}order_approval/`;
var ORDER = `${BASE_URL}orders/`;

function goBack() {
  window.location.href = HOME;
}

function showAuthorize(code) {
  $.ajax({
    url: `${ORDER}get_authorizer`,
    type: 'GET',
    cache: false,
    data: {
      'code': code
    },
    success: function (rs) {
      if (isJson(rs)) {
        let source = $('#authorizer-template').html();
        let data = $.parseJSON(rs);
        let output = $('#authorizer-table');

        render(source, data, output);

        $('#authorizer-modal').modal('show');
      }
      else {
        swal({
          title: "Error!",
          text: rs,
          type: 'error'
        })
      }
    }
  })
}


function preview(code, status) {
  $('.a-btn').attr('disabled', 'disabled'); 
  $('.a-btn').addClass('hide');
  
  load_in();

  $('#OrderCode').val(code);

  $.ajax({
    url: `${HOME}get_detail`,
    type: 'GET',
    cache: false,
    data: {
      'code': code
    },
    success: function (rs) {
      load_out();

      if (isJson(rs)) {
        let source = $('#preview-template').html();
        let data = JSON.parse(rs);
        let output = $('#result');

        render(source, data, output);

        if (data.CanApprove == true) {         
          $('.a-btn').removeClass('hide');
        }              

        $('#previewModal').modal('show');
      }
      else {
        showError(rs);
      }
    }
  });
}


function doApprove() {
  clearErrorByClass('reject-box');

  let ds = {
    'code' : $('#OrderCode').val(),
    'items' : []
  }
  
  let check = 0;
  let err = 0;  

  $('.check-item').each(function() {    
    if($(this).is(':checked')) {      
      ds.items.push({"id" : $(this).val(), "status" : "A"});      
      check++;
    }
    else {
      let id = $(this).val();
      let reject_text = $('#reject-item-'+ id).val().trim();

      if(reject_text.length) {        
        items.push({"id" : id, "status" : "R", "reject_text" : reject_text });
      }
      else {
        $('#reject-item-'+id).hasError();
        err++;
      }
    }
  });

  if(err > 0) {
    showError('กรุณาระบุเหตุผลในการ Reject ทุกรายการที่ไม่อนุมัติ');
    return false;
  }

  if(check == 0 && ds.items.length == 0) {
    showError('กรุณาเลือกรายการที่ต้องการอนุมัติ');
    return false;
  }

  $('#previewModal').modal('hide');

  load_in();

  $.ajax({
    url: `${HOME}do_approve`,
    type: 'POST',
    cache: false,
    data: {      
      'data': JSON.stringify(ds)
    },
    success: function (rs) {
      load_out();
      
      if (rs.trim() === 'success') {
        swal({
          title: "Success",
          type: 'success',
          timer: 1000
        });

        setTimeout(function () {
          window.location.reload();
        }, 1200);
      }
      else {
        showError(rs);
      }
    }
  });
}


function doReject() {
  clearErrorByClass('reject-box');
  
  let ds = {
    'code' : $('#OrderCode').val(),
    'items' : []
  };
  
  let check = 0;
  let err = 0;
  let count = 0;  

  $('.check-item').each(function() {    
    if($(this).is(':checked')) {
      let id = $(this).val();
      let reject_text = $("#reject-item-"+id).val();
      if(reject_text.length) {
        ds.items.push({"id" : id, "reject_text" : reject_text});
        $('#reject-item-'+id).removeClass('has-error');
      }
      else {
        $('#reject-item-'+id).addClass('has-error');
        err++;
      }
      check++;
    }
    count++;
  });

  if(count > 0 && check != count) {
    showWarning('กรุณาเลือกรายการทั้งหมด');
    return false;
  }

  if(err > 0) {
    showWarning('กรุณาระบุเหตุผลในการ Reject ทุกรายการ');
    return false;
  }

  $('#previewModal').modal('hide');

  load_in();

  $.ajax({
    url: `${HOME}do_reject`,
    type: 'POST',
    cache: false,
    data: {
      'data': JSON.stringify(ds)
    },
    success: function (rs) {
      load_out();
      if (rs.trim() === 'success') {
        swal({
          title: "Success",
          type: 'success',
          timer: 1000
        });

        setTimeout(function () {
          window.location.reload();
        }, 1200);
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


function toggleApprove() {
  let check = 0;

  $('.check-item').each(function(){
    if($(this).is(':checked')) {
      check++;
    }
  });

  if(check == 0) {
    $('#btn-approve').attr('disabled', 'disabled');
    $('#btn-reject').attr('disabled', 'disabled');
  }
  else {
    if(check > 0) {
      $('#btn-approve').removeAttr('disabled');
      $('#btn-reject').removeAttr('disabled');
    }
  }
}


$("#fromDate").datepicker({
	dateFormat: 'dd-mm-yy',
	onClose: function(ds){
		$("#toDate").datepicker("option", "minDate", ds);
	}
});

$("#toDate").datepicker({
	dateFormat: 'dd-mm-yy',
	onClose: function(ds){
		$("#fromDate").datepicker("option", "maxDate", ds);
	}
});

