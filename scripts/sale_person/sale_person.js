var HOME = BASE_URL + 'sale_person/';

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


  if(name.length === 0) {
    set_error(el, label, "Required");
    return false;
  }
  else {
    clear_error(el, label);
  }


  load_in();

  $.ajax({
    url:HOME + 'add',
    type:'POST',
    cache:false,
    data:{
      'name' : name
    },
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


  load_in();

  $.ajax({
    url:HOME + 'update/'+id,
    type:'POST',
    cache:false,
    data:{
      'name' : name
    },
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
            text:'Sales person has been deleted',
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


$('#name').keyup(function(e){
  if(e.keyCode == 13) {
    $('#btn-save').click();
  }
})
