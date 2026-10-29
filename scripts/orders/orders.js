var HOME = `${BASE_URL}orders/`;

function sendToSAP() {
  var code = $('#OrderCode').val();
  load_in();
  $.ajax({
    url:`${HOME}sendToSAP`,
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
  load_in();
  window.location.href = `${HOME}add_new`;
}

function goEdit(code){
  window.location.href = `${HOME}edit/${code}`;
}

function goDetail(code){
	window.location.href = `${HOME}view_detail/${code}`;
}

function preview(code, status) {
  $('.a-btn').addClass('hide');
  load_in();

  $('#OrderCode').val(code);

  $.ajax({
    url:`${HOME}get_detail`,
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

        if(data.status != -1) {
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
    url:`${HOME}get_authorizer`,
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
  let err = 0;
  let items = [];

  $('.check-item').each(function() {
    let id = $(this).val();
    if($(this).is(':checked')) {
      let item = {"id" : id, "status" : "A"}
      items.push(item);
      $('#reject-item-'+ id).removeClass('has-error');
      check++;
    }
    else {
      let reject_text = $('#reject-item-'+ id).val();
      if(reject_text.length) {
        let item = {"id" : id, "status" : "R", "reject_text" : reject_text }
        items.push(item);
      }
      else {
        $('#reject-item-'+id).addClass('has-error');
        err++;
      }
    }
  });

  if(err > 0) {
    swal({
      title:'Required',
      text:'กรุณาระบุเหตุผลในการ Reject ทุกรายการที่ไม่อนุมัติ',
      type:'warning'
    });

    return false;
  }

  if(check > 0) {
    $('#previewModal').modal('hide');

    load_in();

    $.ajax({
      url:`${HOME}do_approve`,
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
  let err = 0;
  let count = 0;
  let items = [];

  $('.check-item').each(function() {
    if($(this).is(':checked')) {
      let id = $(this).val();
      let reject_text = $("#reject-item-"+id).val();
      if(reject_text.length) {
        items.push({"id" : id, "reject_text" : reject_text});
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


  if(check == count) {
    if(err > 0) {
      swal({
        title:'Required',
        text:'กรุณาระบุเหตุผลในการ Reject ทุกรายการ',
        type:'warning'
      });

      return false;
    }

    $('#previewModal').modal('hide');

    load_in();

    $.ajax({
      url:`${HOME}do_reject`,
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

$('#DocDueDate').datepicker({
  dateFormat:'dd-mm-yy'
});

function viewDetail(code) {
  $.ajax({
    url:`${HOME}get_temp_data`,
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
    url:`${HOME}remove_temp`,
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

function cancelOrder() {
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
        url:`${HOME}cancel_order`,
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

function openFile(fileName) {
  let width = 1000;
  let height = 600;
  let left = (screen.width - width) / 2;
  let top = (screen.height - height) / 2;
  let target = `${HOME}open_file/${fileName}?nomenu&nonavbar`;
  window.open(target, "_blank", `width=${width}, height=${height}, left=${left}, top=${top}`);
}