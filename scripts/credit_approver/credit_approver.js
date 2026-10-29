var HOME = `${BASE_URL}credit_approver/`
;
var uname_error = 1;
var amount_error = 1;

function goBack() {
  window.location.href = HOME;
}


function addNew() {
  window.location.href = `${HOME}add_new`;
}


function edit(id) {
  window.location.href = `${HOME}edit/${id}`;
}


$('#uname').change(function() {
  let uname = $('#uname').val();
  let name = $('#uname option:selected').data('name');
  $('#emp_name').val(name);
});


function add() {
  clearErrorByClass('e');

  let h = {
    'uname' : $('#uname').val(),
    'user_id' : $('#uname option:selected').data('id'),
    'emp_name' : $('#emp_name').val(),
    'amount' : parseDefaultFloat($('#amount').val(), 0),
    'status' : $('input[name="status"]:checked').val(),
    'can_approve' : $('input[name="can_approve"]:checked').val(),
    'can_review' : $('input[name="can_review"]:checked').val()
  }

  if(h.uname == '') {
    $('#uname').hasError('Required');
    return false;
  }

  if(h.amount <= 0 && h.can_approve == 1) {
    $('#amount').hasError('Approve amount must be greater than 0');
    $('#amount').focus();
    return false;
  }

  load_in();

  $.ajax({
    url: `${HOME}add`,
    type:'POST',
    cache:false,
    data:{
      'data' : JSON.stringify(h)
    },
    success:function(rs) {
      load_out();

      if(rs.trim() === 'success') {
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });

        setTimeout(function(){
          addNew();
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
    'uname' : $('#uname').val(),
    'amount' : parseDefaultFloat($('#amount').val(), 0),
    'status' : $('input[name="status"]:checked').val(),
    'can_approve' : $('input[name="can_approve"]:checked').val(),
    'can_review' : $('input[name="can_review"]:checked').val()
  }

  if(h.amount <= 0 && h.can_approve == 1) {
    $('#amount').hasError("Approve amount must be greater than 0");
    $('#amount').focus();
    return false;
  }

  load_in();

  $.ajax({
    url: `${HOME}update`,
    type:'POST',
    cache:false,
    data:{
      'data' : JSON.stringify(h)
    },
    success:function(rs) {
      load_out();

      if(rs.trim() === 'success') {
        swal({
          title:'Success',
          type:'success',
          timer:1200
        });
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


function getDelete(id, uname){
  swal({
    title:'Are sure ?',
    text:'Do you really want to delete '+ uname +' ? <br/> This process cannot be undone.',
    type:'warning',
    showCancelButton: true,
		confirmButtonColor: '#FA5858',
		confirmButtonText: 'Delete',
		cancelButtonText: 'Cancle',
		closeOnConfirm: true,
    html:true
  }, function() {
    load_in();

    setTimeout(() => {
      $.ajax({
        url: `${HOME}delete`,
        type:'POST',
        cache:false,
        data:{
          'id' : id,
          'uname' : uname
        },
        success:function(rs) {
          load_out();

          if(rs.trim() === 'success') {
            swal({
              title:'Success',
              text:'Approver has been deleted',
              type:'success',
              timer: 1000
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
    }, 100);    
  })
}


function toggleActive(id, el) {
  let active = $(el).is(':checked') ? 1 : 0;

  $.ajax({
    url: `${HOME}set_active`,
    type:'POST',
    cache:false,
    data:{
      'id' : id,
      'active' : active
    },
    success:function(rs) {     
      if(rs.trim() !== 'success') {
        $(el).prop('checked', !active);
        showError(rs);
      }      
    },
    error:function(rs) {     
      $(el).prop('checked', !can_approve); 
      showError(rs);
    }
  });
}


function toggleCanApprove(id, el) {
  let can_approve = $(el).is(':checked') ? 1 : 0;

  $.ajax({
    url: `${HOME}set_can_approve`,
    type:'POST',
    cache:false,
    data:{
      'id' : id,
      'can_approve' : can_approve
    },
    success:function(rs) {
      if(rs.trim() !== 'success') {
        $(el).prop('checked', !can_approve);
        showError(rs);
      }
    },
    error:function(rs) {
      $(el).prop('checked', !can_approve);
      showError(rs);
    }
  });
}


function toggleCanReview(id, el) {
  let can_review = $(el).is(':checked') ? 1 : 0;

  $.ajax({
    url: `${HOME}set_can_review`,
    type:'POST',
    cache:false,
    data:{
      'id' : id,
      'can_review' : can_review
    },
    success:function(rs) {
      if(rs.trim() !== 'success') {
        $(el).prop('checked', !can_review);
        showError(rs);
      }
    },
    error:function(rs) {
      $(el).prop('checked', !can_review);
      showError(rs);
    }
  });
}