var HOME = BASE_URL + 'order_promotion/';

function doApprove() {
  var code = $('#code').val();
  $.ajax({
    url:HOME + 'approve',
    type:'POST',
    cache:false,
    data:{
      'code' : code
    },
    success:function(rs) {
      var rs = $.trim(rs);
      if(rs === 'success') {
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });

        setTimeout(function(){
          window.location.reload();
        }, 1200);
      }
      else {
        swal({
          title:'Error',
          text:rs,
          type:'error'
        })
      }
    }
  })
}



function doReject() {
  var code = $('#code').val();
  $.ajax({
    url:HOME + 'reject',
    type:'POST',
    cache:false,
    data:{
      'code' : code
    },
    success:function(rs) {
      var rs = $.trim(rs);
      if(rs === 'success') {
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });

        setTimeout(function(){
          window.location.reload();
        }, 1200);
      }
      else {
        swal({
          title:'Error',
          text:rs,
          type:'error'
        })
      }
    }
  })
}



function sendToSAP() {
  var code = $('#OrderCode').val();
  load_in();
  $.ajax({
    url:HOME + 'sendToSAP',
    type:'POST',
    cache:false,
    data:{
      'code' : code
    },
    success:function(rs) {
      load_out();
      var rs = $.trim(rs);
      if(rs === 'success') {
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });

      }
      else {
        swal({
          title:'Error!',
          text:rs,
          type:'error'
        })
      }
    }
  })
}




function goBack(){
  window.location.href = HOME;
}

function leave(){
  swal({
    title:'คุณแน่ใจ ?',
    text:'รายการทั้งหมดจะไม่ถูกบันทึก ต้องการออกหรือไม่ ?',
    type:'warning',
    showCancelButton:true,
    cancelButtonText:'ไม่ใช่',
    confirmButtonText:'ออกจากหน้านี้',
  },
  function(){
    goBack();
  });
}


function goAdd(){
  window.location.href = HOME + 'add_new';
}


function goEdit(code){
  window.location.href = HOME + 'edit/'+code;
}



function goDetail(code){
	window.location.href = HOME + 'view_detail/'+code;
}




function preview(code, status) {
  load_in();

  $('#OrderCode').val(code);

  $.ajax({
    url:BASE_URL + 'orders/get_detail',
    type:'GET',
    cache:false,
    data:{
      'code' : code
    },
    success:function(rs) {
      load_out();
      if(isJson(rs)) {

        let source = $('#preview-template').html();
        let data = $.parseJSON(rs);
        let output = $('#result');

        if(data.Approved == 'P') {
          if(data.CanApprove == true) {
            $('#btn-approve').removeClass('hide');
            $('#btn-reject').removeClass('hide');
          }
          else {
            $('#btn-approve').addClass('hide');
            $('#btn-reject').addClass('hide');
          }

          $('#btn-temp').addClass('hide');
        }
        else {
          $('#btn-approve').addClass('hide');
          $('#btn-reject').addClass('hide');

          if(data.Approved == 'A') {
            $('#btn-temp').removeClass('hide');
          }
        }

        render(source, data, output);

        $('#previewModal').modal('show');
      }
      else {
        swal({
          title:"Error!",
          text:rs,
          type:'error'
        })
      }
    }
  })
}




function showAuthorize(code) {
  $.ajax({
    url:HOME + 'get_authorizer',
    type:'GET',
    cache:false,
    data:{
      'code' : code
    },
    success:function(rs) {
      if(isJson(rs)) {
        let source = $('#authorizer-template').html();
        let data = $.parseJSON(rs);
        let output = $('#authorizer-table');

        render(source, data, output);

        $('#authorizer-modal').modal('show');
      }
      else {
        swal({
          title:"Error!",
          text:rs,
          type:'error'
        })
      }
    }
  })
}



function doApprove() {

  let code = $('#OrderCode').val();
  let check = 0;
  let items = [];

  $('.check-item').each(function(){
    if($(this).is(':checked')) {
      let item = {"id" : $(this).val(), "status" : "A"}
      items.push(item);
      check++;
    }
    else {
      let item = {"id" : $(this).val(), "status" : "R"}
      items.push(item);
    }
  });


  if(check > 0) {
    $('#previewModal').modal('hide');

    load_in();

    $.ajax({
      url:HOME + 'do_approve',
      type:'POST',
      cache:false,
      data:{
        'code' : code,
        'items' : JSON.stringify(items)
      },
      success:function(rs) {
        load_out();
        var rs = $.trim(rs);
        if(rs === 'success') {
          swal({
            title:"Success",
            type:'success',
            timer:1000
          });

          setTimeout(function() {
            window.location.reload();
          }, 1200);
        }
        else {
          swal({
            title:"Error!",
            text: rs,
            type:'error'
          });
        }
      }
    });
  }
  else {
    return false;
  }
}




function doReject() {

  let code = $('#OrderCode').val();
  let check = 0;
  let count = 0;

  $('.check-item').each(function(){
    if($(this).is(':checked')) {
      check++;
    }

    count++;
  });


  if(check == count) {
    $('#previewModal').modal('hide');

    load_in();

    $.ajax({
      url:HOME + 'do_reject',
      type:'POST',
      cache:false,
      data:{
        'code' : code
      },
      success:function(rs) {
        load_out();
        var rs = $.trim(rs);
        if(rs === 'success') {
          swal({
            title:"Success",
            type:'success',
            timer:1000
          });

          setTimeout(function() {
            window.location.reload();
          }, 1200);
        }
        else {
          swal({
            title:"Error!",
            text: rs,
            type:'error'
          });
        }
      }
    });
  }
  else {
    swal({
      title:"",
      text:"กรุณาเลือกรายการทั้งหมด",
      type:"warning"
    });
    return false;
  }
}




function toggleApprove() {
  let check = 0;
  let count = 0;

  $('.check-item').each(function(){
    if($(this).is(':checked')) {
      check++;
    }

    count++;
  });

  if(check == 0) {
    $('#btn-approve').attr('disabled', 'disabled');
    $('#btn-reject').attr('disabled', 'disabled');
  }
  else {
    if(check > 0) {
      $('#btn-approve').removeAttr('disabled');
    }

    if(check == count) {
      $('#btn-reject').removeAttr('disabled');
    }
    else {
      $('#btn-reject').attr('disabled', 'disabled');
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


$('#DocDueDate').datepicker({
  dateFormat:'dd-mm-yy'
});




function viewDetail(code) {
  $.ajax({
    url:HOME + 'get_temp_data',
    type:'GET',
    data:{
      'code' : code //--- U_WEBORDER
    },
    success:function(rs) {
      var rs = $.trim(rs);
      if(isJson(rs)) {
        var data = $.parseJSON(rs);
        var source = $('#temp-template').html();
        var output = $('#temp-table');

        render(source, data, output);

        $('#tempModal').modal('show');
      }
      else {
        swal({
          title:'Error!',
          text:rs,
          type:'error'
        })
      }
    }
  })
}


function removeTemp() {

  $('#tempModal').modal('hide');

  var code = $('#U_WEB_ORNO').val();
  $.ajax({
    url:HOME + 'remove_temp',
    type:'POST',
    data:{
      'code' : code
    },
    success:function(rs) {
      var rs = $.trim(rs);
      if(rs === 'success') {
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });

        setTimeout(function(){
          window.location.reload();
        }, 1000);
      }
      else {
        swal({
          title:'Error!',
          text:rs,
          type:'error'
        })
      }
    }
  })
}

function closeModal(name) {
  $('#'+name).modal('hide');
}


function cancleOrder() {
  $('#tempModal').modal('hide');

  var code = $('#U_WEB_ORNO').val();

  swal({
		title: "คุณแน่ใจ ?",
		text: "ต้องการยกเลิก '"+code+"' หรือไม่?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: 'ยืนยัน',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: true
  }, function() {
      load_in();
      $.ajax({
        url:HOME + 'cancle_order',
        type:'POST',
        data:{
          'code' : code
        },
        success:function(rs) {
          load_out();
          var rs = $.trim(rs);
          if(rs === 'success') {
            setTimeout(function() {
              swal({
                title:'Success',
                type:'success',
                timer:1000
              });

              setTimeout(function(){
                window.location.reload();
              }, 1000);
            }, 200);
          }
          else {
            setTimeout(function() {
              swal({
                title:'Error!',
                text:rs,
                type:'error'
              });
            }, 200);
          }
        }
      })
  });

}
