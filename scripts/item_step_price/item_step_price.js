var HOME = BASE_URL + "item_step_price/";


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


function update_uom(el) {
  let uom = $('#item option:selected').data('uom');

  $('#uom').val(uom);
}


function add() {
  $('.e').removeClass('has-error');

  let h = {
    'ItemCode' : $('#item').val(),
    'ItemName' : $('#item option:selected').text(),
    'uom' : $('#item option:selected').data('uom'),
    'active' : $('#active').val()
  }

  if(h.ItemCode == "") {
    $('#item').hasError();
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

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status.trim() === 'success') {
          goEdit(ds.id);
        }
        else {
          showError(rs);
        }
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


function save() {
  clearErrorByClass('e');

  let err = 0;
  let id = $('#id').val();
  let uom = $('#uom').val();

  let h = {
    'id' : id,
    'active' : $('#active').val(),
    'rows' : []
  }


  if($('.chk').length) {
    $('.chk').each(function() {
      let no = $(this).data('no');
      let name = $('#step-name-'+no).val().trim();
      let stepQty = parseDefault(parseFloat($('#step-qty-'+no).val()), 0);
      let sellPrice = parseDefault(parseFloat($('#sell-price-'+no).val()), 0);
      let freeQty = parseDefault(parseFloat($('#free-qty-'+no).val()), 0);
      let pos = parseDefault(parseFloat($('#pos-'+no).val()), 10);
      let active = $('#active-'+no).is(':checked') ? 1 : 0;

      if(stepQty > 0 && sellPrice > 0) {
        if(stepQty <= 0) {
          $('#step-qty-'+no).hasError();
          err++;
        }

        if(sellPrice <= 0) {
          $('#sell-price-'+no).hasError();
          err++;
        }

        if(freeQty < 0) {
          $('#free-qty-'+no).hasError();
          err++;
        }

        let row = {
          'step_id' : id,
          'name' : name,
          'Qty' : stepQty,
          'SellPrice' : sellPrice,
          'freeQty' : freeQty,
          'position' : pos
        };

        h.rows.push(row);
      }
    });
  }

  if(err > 0) {
    return false;
  }

  load_in();

  $.ajax({
    url:HOME + 'save',
    type:'POST',
    cache:false,
    data:{
      'data' : JSON.stringify(h)
    },
    success:function(rs) {
      load_out();

      if(rs.trim() == 'success') {
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
        swal({
          title:'Error!',
          text:rs,
          type:'error',
          html:true
        })
      }
    },
    error:function(rs) {
      load_out();
      swal({
        title:'Error!',
        text:rs.responseText,
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
            text:'Price list has been deleted',
            type:'success',
            time: 1000
          });

          setTimeout(function(){
            window.location.reload();
          }, 1200)
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


function toggleActive(option) {
  $('#active').val(option);

  if(option == 1) {
    $('#btn-active').addClass('btn-primary');
    $('#btn-inactive').removeClass('btn-danger');
  }

  if(option == 0) {
    $('#btn-inactive').addClass('btn-danger');
    $('#btn-active').removeClass('btn-primary');
  }
}


function addRow() {
  let no = parseDefault(parseFloat($('#top-row').val()), 0);

  let source = $('#row-template').html();
  let ds = {'no' : no};
  let output = $('#detail-table');

  render_append(source, ds, output);
  no++;
  $('#top-row').val(no);

  reIndex();
}


function removeRow() {
  if($('.chk:checked').length) {
    $('.chk:checked').each(function() {
      let id = parseDefault(parseInt($(this).data('id')), 0);
      let no = $(this).data('no');

      if(id > 0) {
        let source = $('#delete-template').html();
        let data = {'row_id' : id};
        let output = $('#deleted-table');

        render_append(source, data, output);
      }

      $('#row-'+no).remove();

      reIndex();
    });
  }
}


function checkAll() {
  if($('#check-all').is(':checked')) {
    $('.chk').prop('checked', true);
  }
  else {
    $('.chk').prop('checked', false);
  }
}
