var HOME = BASE_URL + 'sales_team_condition/';

function goBack() {
  window.location.href = HOME;
}


function goAdd() {
  window.location.href = HOME + 'add_new';
}


function goEdit(id) {
  window.location.href = HOME + 'edit/'+id;
}


function viewDetail(id) {
  window.location.href = HOME + 'view_detail/'+id;
}


function add() {
  clearErrorByClass('e');

  let h = {
    'name' : $('#name').val().trim(),
    'team_id' : $('#team-id').val(),
    'area' : [],
    'approver' : []
  };

  if(h.name.length == 0) {
    $('#name').hasError('Required');
    return false;
  }

  if(h.team_id == "") {
    $('#team-id').hasError('Required');
    return false;
  }

  if($('.area:checked').length == 0) {
    $('#area').hasError('Required at least 1 area(s)');
    return false;
  }

  if($('.approver-list').length === 0) {
    $('#approver').hasError('Required at least 1 authorizer(s)');
    return false;
  }

  $('.area:checked').each(function() {
    h.area.push($(this).val());
  });

  $('.approver-list').each(function() {
    h.approver.push($(this).val());
  });

  if(h.approver.length === 0) {
    $('#approver').hasError('Required at least 1 authorizer(s)');
    return false;
  }

  if(h.area.length == 0) {
    $('#area').hasError('Required at least 1 area(s)');
    return false;
  }

  load_in();

  $.ajax({
    url:HOME + 'add',
    type:'POST',
    cache:false,
    data:{
      "data" : JSON.stringify(h)
    },
    success:function(rs) {
      load_out();

      if(rs.trim() === 'success') {
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });

        setTimeout(() => {
          goAdd();
        }, 1200);
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


function update() {
  clearErrorByClass('e');

  let h = {
    'id' : $('#id').val(),
    'name' : $('#name').val().trim(),
    'team_id' : $('#team-id').val(),
    'area' : [],
    'approver' : []
  };

  if(h.name.length == 0) {
    $('#name').hasError('Required');
    return false;
  }

  if(h.team_id == "") {
    $('#team-id').hasError('Required');
    return false;
  }

  if($('.area:checked').length === 0) {
    $('#area').hasError('Required at least 1 area(s)');
    return false;
  }

  if($('.approver-list').length === 0) {
    $('#approver').hasError('Required at least 1 authorizer(s)');
    return false;
  }

  $('.area:checked').each(function() {
    h.area.push($(this).val());
  });

  $('.approver-list').each(function() {
    h.approver.push($(this).val());
  });

  if(h.area.length === 0) {
    $('#area').hasError('Required at least 1 area(s)');
    return false;
  }

  if(h.approver.length === 0) {
    $('#approver').hasError('Required at least 1 authorizer(s)');
    return false;
  }

  load_in();

  $.ajax({
    url:HOME + 'update',
    type:'POST',
    cache:false,
    data:{
      "data" : JSON.stringify(h)
    },
    success:function(rs) {
      load_out();

      if(rs.trim() === 'success') {
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });

        setTimeout(() => {
          window.location.reload();
        }, 1200);
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


function getDelete(id, name){
  swal({
    title:'Are sure ?',
    text:'Do you really want to delete '+ name +' ? <br/> This process cannot be undone.',
    type:'warning',
    showCancelButton: true,
    confirmButtonColor: '#FA5858',
    confirmButtonText: 'Yes',
    cancelButtonText: 'No',
    closeOnConfirm: true,
    html:true
  },function() {
    load_in();

    setTimeout(() => {
      $.ajax({
        url: HOME + 'delete',
        type:'POST',
        cache:false,
        data:{
          'id' : id
        },
        success:function(rs) {
          load_out();
          if(rs == 'success'){
            swal({
              title:'Success',
              text:'Sales team condition has been deleted',
              type:'success',
              time: 1000
            });

            setTimeout(function(){
              window.location.reload();
            }, 1200)
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
    }, 200);

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
          title:'',
          text:rs,
          type:'info'
        })
      }
    }
  })
}


$('#area-all').change(function(){
  if($(this).is(':checked')) {
    $('.area').prop('checked', true);
  }
  else {
    $('.area').prop('checked', false);
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
