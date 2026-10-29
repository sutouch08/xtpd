var HOME = `${BASE_URL}approver/`;
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


$('#max-amount').change(function() {
  let amount = parseDefaultFloat(removeCommas($('#max-amount').val()), 0);
  $('#max-amount').val(addCommas(amount.toFixed(2)));
});

$('#min-amount').change(function() {
  let amount = parseDefaultFloat(removeCommas($('#min-amount').val()), 0);
  $('#min-amount').val(addCommas(amount.toFixed(2)));
});

function add() {
  clearErrorByClass('e');

  let h = {    
    'uname' : $('#uname').val(),
    'emp_name' : $('#emp_name').val(),
    'min_amount' : parseDefaultFloat(removeCommas($('#min-amount').val()), 0),
    'max_amount' : parseDefaultFloat(removeCommas($('#max-amount').val()), 0),
    'status' : $("input[name='status']:checked").val()
  }

  if(h.uname == '') {
    $('#uname').hasError('Required');
    return false;
  }

  if(h.min_amount < 0) {
    $('#min-amount').hasError('Min. amount must be greater than or equal to 0');
    $('#min-amount').focus();
    return false;
  }

  if(h.max_amount <= 0) {
    $('#max-amount').hasError('Approve amount must be greater than 0');
    $('#max-amount').focus();
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
    'min_amount' : parseDefaultFloat(removeCommas($('#min-amount').val()), 0),
    'max_amount' : parseDefaultFloat(removeCommas($('#max-amount').val()), 0),    
    'status' : $("input[name='status']:checked").val()
  }

  if(h.min_amount < 0) {
    $('#min-amount').hasError("Min. amount must be greater than or equal to 0");
    $('#min-amount').focus();
    return false;
  }
  
  if(h.max_amount <= 0) {
    $('#max-amount').hasError("Approve amount must be greater than 0");
    $('#max-amount').focus();
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

function confirmDelete(id, uname) {
  swal({
    title:'Are sure ?',
    text:'Do you really want to delete '+ uname +' ? <br/> This process cannot be undone.',
    type:'warning',
    showCancelButton: true,
    confirmButtonColor: '#FA5858',
    confirmButtonText: 'Delete',
    cancelButtonText: 'Cancel',
    closeOnConfirm: true,
    html:true
  }, function() {
    setTimeout(() => {
      doDelete(id, uname);
    }, 100);    
  });
}


function doDelete(id, uname) {
  load_in();

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
  });
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
        showError(rs);
      }
    },
    error:function(rs) {
      load_out();
      showError(rs);
    }
  });
}