var HOME = BASE_URL + 'approve_rule/';

function goBack() {
  window.location.href = HOME;
}


function goAdd() {
  window.location.href = HOME + 'add_new';
}


function goEdit(id) {
  window.location.href = HOME + 'edit/'+id;
}


function add() {
  clearErrorByClass('e');

  let h = {
    'conditions' : $('#conditions').val(),
    'amount' : parseDefault(parseFloat($('#amount').val()), 0),
    'sale_team' : $('#sale_team').val(),
    'is_price_list' : $('#is_price_list').is(':checked') ? 1 : 0,
    'status' : $('#status').is(':checked') ? 1 : 0
  }

  if(h.conditions == '') {
    $('#conditions').hasError('Required');
    return false;
  }

  if(h.sale_team == '') {
    $('#sale_team').hasError('Required');
    return false;
  }

  if(h.amount <= 0) {
    $('#amount').hasError('Amount must Greater than 0');
    $('#amount').focus();
    return false;
  }

  load_in();

  $.ajax({
    url:HOME + 'add',
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
    'conditions' : $('#conditions').val(),
    'amount' : parseDefault(parseFloat($('#amount').val()), 0),
    'sale_team' : $('#sale_team').val(),
    'is_price_list' : $('#is_price_list').is(':checked') ? 1 : 0,
    'status' : $('#status').is(':checked') ? 1 : 0
  }

  if(h.conditions == '') {
    $('#conditions').hasError('Required');
    return false;
  }

  if(h.sale_team == '') {
    $('#sale_team').hasError('Required');
    return false;
  }

  if(h.amount <= 0) {
    $('#amount').hasError('Amount must Greater than 0');
    $('#amount').focus();
    return false;
  }

  load_in();

  $.ajax({
    url:HOME + 'update',
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
