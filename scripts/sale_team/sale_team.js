var HOME = BASE_URL + 'sale_team/';

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
  var el = $('#name');
  var label = $('#name-error');
  var name = el.val();
  var sale_person = $('#sale_person');
  var customer_team = $('#customer_team');
  var cLabel = $('#customer-team-error');
  var pLabel = $('#sale-person-error');

  if(name.length === 0) {
    set_error(el, label, "Required");
    return false;
  }
  else {
    clear_error(el, label);
  }

  if(sale_person.val() == "") {
    set_error(sale_person, pLabel, "Required");
    return false;
  }
  else {
    clear_error(sale_person, pLabel);
  }

  if(customer_team.val() == "") {
    set_error(customer_team, cLabel, "Required");
    return false;
  }
  else {
    clear_error(customer_team, cLabel);
  }


  let customerGroup = [];

  $('.chk').each(function() {
    if($(this).is(':checked')) {
      let cg = {"group_id" : $(this).val()};
      customerGroup.push(cg);
    }
  });

  var approver = [];

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

  var fd = new FormData();
  fd.append("name", name);
  fd.append("sale_person", sale_person.val());
  fd.append('customer_team', customer_team.val());
  fd.append("customer_group", JSON.stringify(customerGroup));
  fd.append("approver", JSON.stringify(approver));

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
          title:"Success",
          type:"success",
          timer:1000
        });

        setTimeout(function() {
          goAdd();
        }, 1200);
      }
      else {
        swal({
          title:"Error!",
          text:rs,
          type:"error"
        });
      }
    },
    error:function(xhr) {
      load_out();
      swal({
        title:"Error!",
        text:"Error-"+xhr.responseText,
        type:"error"
      });
    }
  })
}


function update() {
  var id = $('#id').val();
  var el = $('#name');
  var label = $('#name-error');
  var name = el.val();
  var sale_person = $('#sale_person');
  var pLabel = $('#sale-person-error');
  var customer_team = $('#customer_team');
  var cLabel = $('#customer-team-error');

  if(id == "") {
    swal("Error!", "Missing requierd parameter : id", "error");
    return false;
  }

  if(name.length === 0) {
    set_error(el, label, "Required");
    return false;
  }
  else {
    clear_error(el, label);
  }

  if(sale_person.val() == "") {
    set_error(sale_person, pLabel, "Required");
    return false;
  }
  else {
    clear_error(sale_person, pLabel);
  }

  if(customer_team.val() == "") {
    set_error(customer_team, cLabel, "Required");
    return false;
  }
  else {
    clear_error(customer_team, cLabel);
  }

  let customerGroup = [];

  $('.chk').each(function() {
    if($(this).is(':checked')) {
      let cg = {"group_id" : $(this).val(), };
      customerGroup.push(cg);
    }
  })


  var approver = [];

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

  var fd = new FormData();
  fd.append("name", name);
  fd.append("sale_person", sale_person.val());
  fd.append('customer_team', customer_team.val());
  fd.append("customer_group", JSON.stringify(customerGroup));
  fd.append("approver", JSON.stringify(approver));

  load_in();

  $.ajax({
    url:HOME + 'update/'+id,
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
          title:"Success",
          type:"success",
          timer:1000
        });

      }
      else {
        swal({
          title:"Error!",
          text:rs,
          type:"error"
        });
      }
    },
    error:function(xhr) {
      load_out();
      swal({
        title:"Error!",
        text:"Error-"+xhr.responseText,
        type:"error"
      });
    }
  })

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
      url: HOME + 'delete/'+id,
      type:'POST',
      cache:false,
      success:function(rs){
        if(rs == 'success'){
          swal({
            title:'Success',
            text:'Sales team has been deleted',
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




function viewMember(id) {
  $.ajax({
    url:HOME + 'get_member/'+id,
    type:'GET',
    cache:false,
    success:function(rs) {
      if(isJson(rs)) {
        var ds = $.parseJSON(rs);
        var source = $('#member-template').html();
        var output = $('#result');

        render(source, ds, output);

        $('#memberModal').modal('show');
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

$('#chk-all').change(function(){
  if($(this).is(':checked')) {
    $('.chk').prop('checked', true);
  }
  else {
    $('.chk').prop('checked', false);
  }
})



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
