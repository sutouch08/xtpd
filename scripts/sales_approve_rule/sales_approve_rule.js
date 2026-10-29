var HOME = BASE_URL + 'sales_approve_rule/';

function goBack() {
  window.location.href = HOME;
}


function goAdd() {
  window.location.href = HOME + 'add_new';
}


function goEdit(id) {
  window.location.href = HOME + 'edit/'+id;
}



function saveAdd() {
  var conditions = $('#conditions').val();
  var amount = parseDefault(parseFloat($('#amount').val()),0);
  var sale_id = $('#sale_id').val();
  var is_price_list = $('#is_price_list').is(':checked') ? 1 : 0;
  var status = $('#status').is(':checked') ? 1 : 0;
  var approver = [];

  if(conditions == "") {
    $('#conditions').addClass('has-error');
    $('#conditions-error').text('Required');
    return false;
  }
  else {
    $('#conditions').removeClass('has-error');
    $('#conditions-error').text('');
  }


  if(sale_id == "") {
    $('#sale_id').addClass('has-error');
    $('#sale_id-error').text('Required');
    return false;
  }
  else {
    $('#sale_id').removeClass('has-error');
    $('#sale_id-error').text('');
  }


  if(amount < 0) {
    $('#amount').addClass('has-error');
    $('#amount-error').text('Amount must Greater than 0');
    $('#amount').focus();
    return false;
  }
  else {
    $('#amount').removeClass('has-error');
    $('#amount-error').text('');
  }


  if($('.approver-list').length === 0) {
    $('#approver').addClass('has-error');
    $('#approver-error').text('Required at least 1 authorizer(s)');
    return false;
  }
  else {
    $('#approver').removeClass('has-error');
    $('#approver-error').text('');

    $('.approver-list').each(function() {
      let user_id = $(this).val();

      approver.push(user_id);
    })
  }


  if(approver.length === 0) {
    $('#approver').addClass('has-error');
    $('#approver-error').text('Required at least 1 authorizer(s)');
    return false;
  }
  else {
    $('#approver').removeClass('has-error');
    $('#approver-error').text('');
  }


  let customerGroup = [];

  $('.chk').each(function() {
    if($(this).is(':checked')) {
      let cg = {"group_id" : $(this).val(), };
      customerGroup.push(cg);
    }
  })


  var fd = new FormData();

  fd.append("conditions", conditions);
  fd.append("amount", amount);
  fd.append("sale_id", sale_id);
  fd.append("is_price_list", is_price_list);
  fd.append("status" , status);
  fd.append("approver", JSON.stringify(approver));
  fd.append("customerGroup", JSON.stringify(customerGroup));

  load_in();

  $.ajax({
    url:HOME + 'add',
    type:'POST',
    cache:false,
    data:fd,
    processData:false,
    contentType:false,
    success:function(rs) {
      load_out();
      var rs = $.trim(rs);
      if(rs === 'success') {
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });

        setTimeout(function(){
          goAdd();
        }, 1200);
      }
      else {
        swal({
          title:"Error",
          text:rs,
          type:'error'
        })
      }
    },
    error:function(xhr) {
      load_out();
      swal({
        title:"Error!",
        text:"Error : "+xhr.responseText,
        type:"error",
        html:true
      })
    }
  })

}




function update() {
  var rule_id = $('#rule_id').val();
  var conditions = $('#conditions').val();
  var amount = parseDefault(parseFloat($('#amount').val()),0);
  var sale_id = $('#sale_id').val();
  var is_price_list = $('#is_price_list').is(':checked') ? 1 : 0;
  var status = $('#status').is(':checked') ? 1 : 0;
  var approver = [];

  if(conditions == "") {
    $('#conditions').addClass('has-error');
    $('#conditions-error').text('Required');
    return false;
  }
  else {
    $('#conditions').removeClass('has-error');
    $('#conditions-error').text('');
  }


  if(amount < 0) {
    $('#amount').addClass('has-error');
    $('#amount-error').text('Amount must Greater than 0');
    $('#amount').focus();
    return false;
  }
  else {
    $('#amount').removeClass('has-error');
    $('#amount-error').text('');
  }

  if(sale_id == "") {
    $('#sale_id').addClass('has-error');
    $('#sale_id-error').text('Required');
    return false;
  }
  else {
    $('#sale_id').removeClass('has-error');
    $('#sale_id-error').text('');
  }


  if($('.approver-list').length === 0) {
    $('#approver').addClass('has-error');
    $('#approver-error').text('Required at least 1 authorizer(s)');
    return false;
  }
  else {
    $('#approver').removeClass('has-error');
    $('#approver-error').text('');

    $('.approver-list').each(function() {
      let user_id = $(this).val();

      approver.push(user_id);
    })
  }


  if(approver.length === 0) {
    $('#approver').addClass('has-error');
    $('#approver-error').text('Required at least 1 authorizer(s)');
    return false;
  }
  else {
    $('#approver').removeClass('has-error');
    $('#approver-error').text('');
  }

  let customerGroup = [];

  $('.chk').each(function() {
    if($(this).is(':checked')) {
      let cg = {"group_id" : $(this).val(), };
      customerGroup.push(cg);
    }
  })



  var fd = new FormData();

  fd.append("rule_id", rule_id);
  fd.append("conditions", conditions);
  fd.append("amount", amount);
  fd.append("sale_id", sale_id);
  fd.append("is_price_list", is_price_list);
  fd.append("status" , status);
  fd.append("approver", JSON.stringify(approver));
  fd.append("customerGroup", JSON.stringify(customerGroup));

  load_in();

  $.ajax({
    url:HOME + 'update',
    type:'POST',
    cache:false,
    data:fd,
    processData:false,
    contentType:false,
    success:function(rs) {
      load_out();
      var rs = $.trim(rs);
      if(rs === 'success') {
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });

        setTimeout(function() {
          window.location.reload();
        }, 1200);
      }
      else {
        swal({
          title:"Error",
          text:rs,
          type:'error'
        })
      }
    },
    error:function(xhr) {
      load_out();
      swal({
        title:"Error!",
        text:"Error : "+xhr.responseText,
        type:"error",
        html:true
      })
    }
  })

}



function addApprover() {
  let no = $('#no').val();
  no = parseDefault(parseInt(no), 1);
  let user_id = $('#approver').val();
  let name = $('#approver option:selected').text();

  if(user_id == "") {
    $('#approver').addClass('has-error');
    return false;
  }
  else {
    $('#approver').removeClass('has-error');
  }

  let source = $('#tag-template').html();
  let data = {
    "no" : no,
    "user_id" : user_id,
    "name" : name
  };

  let output = $('#approver-list');

  render_append(source, data, output);

  $('#approver').val('');
  $('#no').val(no+1);
  $('#authorizer-list').removeClass('hide');
}



function removeTag(id) {
  $('#tag-'+id).remove();
  $('#approver-'+id).remove();
  if($('.approver-list').length == 0) {
    $('#authorizer-list').addClass('hide');
  }
}


	function showApprover(id, code) {

    $.ajax({
      url:HOME + 'get_rule_approver_list/'+id,
      type:"GET",
      cache:false,
      success:function(rs) {
        if(isJson(rs)) {
          var data = $.parseJSON(rs);
          var source = $('#approver-template').html();
          var output = $('#result');

          render(source, data, output);
        }
      }
    })

		$('#modal-title').text(code);

		$('#approver-modal').modal('show');
	}



  function getDelete(id, name){
    swal({
      title:'Are sure ?',
      text:'Do you really want to delete '+ name +' ? <br/> This process cannot be undone.',
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
              text:'Approve rule has been deleted',
              type:'success',
              time: 1000
            });

            setTimeout(function(){
              window.location.reload();
            }, 1500)

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
