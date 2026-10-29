var HOME = BASE_URL + "promotion/";


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
  var name = $('#name').val();
  var start_date = $('#start_date').val();
  var end_date = $('#end_date').val();
  var status = $('#status').is(':checked') ? 1 : 0;
  var items = [];
  var error = 0;

  if(name.length == 0)
  {
    $('#name').addClass('has-error');
    return false;
  }
  else  {
    $('#name').removeClass('has-error');
  }


  if(!isDate(start_date)) {
    $('#start_date').addClass('has-error');
    return false;
  }
  else {
    $('#start_date').removeClass('has-error');
  }

  if(!isDate(end_date)) {
    $('#end_date').addClass('has-error');
    return false;
  }
  else {
    $('#end_date').removeClass('has-error');
  }




  $('.input-qty').each(function() {
    let no = $(this).data('no');
    let itemCode = $('#item-'+no).val();
    let itemName = $('#item-'+no+' :selected').text();
    let qty = parseDefault(parseFloat($('#input-qty-'+no).val()), 0);
    let price = parseDefault(parseFloat($('#input-price-'+no).val()), 0);
    let uom = $('#uom-'+no).val();

    var err = 0;

    if(itemCode == "") {
      $('#item-'+no).addClass('has-error');
      error++;
      err++;
    }
    else {
      $('#item-'+no).removeClass('has-error');
    }

    if(qty <= 0) {
      $('#input-qty-'+no).addClass('has-error');
      error++;
      err++;
    }
    else {
      $('#input-qty-'+no).removeClass('has-error');
    }


    if(price <= 0) {
      $('#input-price-'+no).addClass('has-error');
      error++;
      err++;
    }
    else {
      $('#input-price-'+no).removeClass('has-error');
    }


    if(err === 0) {
      let arr = {
        "itemCode" : itemCode,
        "itemName" : itemName,
        "qty" : qty,
        "price" : price,
        "uom" : uom
      }

      items.push(arr);
    }
  });


  if(error > 0) {
    return false;
  }


  if(items.length == 0) {
    swal({
      title:"No item found",
      text:"Please fill item, qty, proce at least 1 row(s)",
      type:'warning'
    })

    return false;
  }


  load_in();

  $.ajax({
    url:HOME + 'add',
    type:'POST',
    cache:false,
    data: {
      'name' : name,
      'start_date' : start_date,
      'end_date' : end_date,
      'status' : status,
      'items' : JSON.stringify(items)
    },
    success:function(rs) {
      load_out();
      if(rs === 'success') {
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });

        setTimeout(function() {
          goAdd();
        }, 1200);
      }
      else {
        swal({
          title:'Error!',
          text:rs,
          type:'error'
        })
      }
    },
    error:function(xhr) {
      load_out();
      swal({
        title:'Error!',
        text:'Error : '+xhr.responseText,
        type:'error',
        html:true
      })
    }
  })
}



function update() {
  var id = $('#id').val();
  var name = $('#name').val();
  var start_date = $('#start_date').val();
  var end_date = $('#end_date').val();
  var status = $('#status').is(':checked') ? 1 : 0;
  var items = [];
  var error = 0;

  if(name.length == 0)
  {
    $('#name').addClass('has-error');
    return false;
  }
  else  {
    $('#name').removeClass('has-error');
  }


  if(!isDate(start_date)) {
    $('#start_date').addClass('has-error');
    return false;
  }
  else {
    $('#start_date').removeClass('has-error');
  }

  if(!isDate(end_date)) {
    $('#end_date').addClass('has-error');
    return false;
  }
  else {
    $('#end_date').removeClass('has-error');
  }




  $('.input-qty').each(function() {
    let no = $(this).data('no');
    let itemCode = $('#item-'+no).val();
    let itemName = $('#item-'+no+' :selected').text();
    let qty = parseDefault(parseFloat($('#input-qty-'+no).val()), 0);
    let price = parseDefault(parseFloat($('#input-price-'+no).val()), 0);
    let uom = $('#uom-'+no).val();

    var err = 0;

    if(itemCode == "") {
      $('#item-'+no).addClass('has-error');
      error++;
      err++;
    }
    else {
      $('#item-'+no).removeClass('has-error');
    }

    if(qty <= 0) {
      $('#input-qty-'+no).addClass('has-error');
      error++;
      err++;
    }
    else {
      $('#input-qty-'+no).removeClass('has-error');
    }


    if(price <= 0) {
      $('#input-price-'+no).addClass('has-error');
      error++;
      err++;
    }
    else {
      $('#input-price-'+no).removeClass('has-error');
    }


    if(err === 0) {
      let arr = {
        "itemCode" : itemCode,
        "itemName" : itemName,
        "qty" : qty,
        "price" : price,
        "uom" : uom
      }

      items.push(arr);
    }
  });


  if(error > 0) {
    return false;
  }


  if(items.length == 0) {
    swal({
      title:"No item found",
      text:"Please fill item, qty, proce at least 1 row(s)",
      type:'warning'
    })

    return false;
  }


  load_in();

  $.ajax({
    url:HOME + 'update/'+id,
    type:'POST',
    cache:false,
    data: {
      'name' : name,
      'start_date' : start_date,
      'end_date' : end_date,
      'status' : status,
      'items' : JSON.stringify(items)
    },
    success:function(rs) {
      load_out();
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
    },
    error:function(xhr) {
      load_out();
      swal({
        title:'Error!',
        text:'Error : '+xhr.responseText,
        type:'error',
        html:true
      })
    }
  })
}



function preview(id) {
  load_in();

  $.ajax({
    url:HOME + 'preview/'+id,
    type:'GET',
    cache:false,
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        var source = $('#preview-template').html();
        var data = $.parseJSON(rs);
        var output = $('#result');

        render(source, data, output);

        $('#preview-modal').modal('show');
      }
      else {
        swal({
          title:'Error!',
          text:rs,
          type:'error'
        })
      }
    },
    error:function(xhr) {
      load_out();
      swal({
        title:'Error!',
        text:"Error : "+xhr.responseText,
        type:'error',
        html:true
      })
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
            text:'Promotion has been deleted',
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



$('#start_date').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#end_date').datepicker("option", "minDate", sd);
  }
});


$('#end_date').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#start_date').datepicker('option', 'maxDate', sd);
  }
})


function addRow() {
  var no = parseDefault(parseInt($('#top-row').val()), 1);
  var ds = {"no" : no};
  var source = $('#row-template').html();
  var output = $('#detail-table');

  render_append(source, ds, output);
  reIndex();
  init(no);
  no++;
  $('#top-row').val(no);
}




function removeRow() {
	$('.chk').each(function(){
		if($(this).is(':checked')) {
			var no = $(this).val();
			$('#row-'+no).remove();
		}
	})

	reIndex();
}


function update_uom(el) {
  let no = el.data('no');
  let uom = $('#item-'+no).find(':selected').data('uom');

  $('#uom-'+no).val(uom);
  $('#input-qty-'+no).focus();
}


function init(no) {
  $('#item-'+no).select2();

}


$(document).ready(function(){
  init(1);
})
