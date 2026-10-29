var HOME = `${BASE_URL}step_rule/`;
var click = 0;

function goBack() {
  window.location.href = HOME;
}


function addNew() {
  window.location.href = `${HOME}add_new`;
}


function edit(id) {
  window.location.href = `${HOME}edit/${id}`;
}


function viewDetail(id) {
  const url = `${HOME}view_detail/${id}?nomenu&nonavbar`;
  const width = 1000;
  const height = 600;
  const left = (screen.width - width) / 2;
  const top = 100;
  window.open(url, '_blank', `width=${width}, height=${height}, left=${left}, top=${top}, scrollbars=yes`);
}

function changeURL(id, tab) {
  var url = `${HOME}edit/${id}/${tab}`;
  var stObj = { stage: 'stage' };
  window.history.pushState(stObj, 'step_rule', url);
}


function add() {
  if(click == 0) {
    click = 1;
    
    clearErrorByClass('e');

    let h = {
      'price_list' : $('#price-list').val(),
      'price_list_name' : $('#price-list option:selected').text(),
      'name' : $('#name').val().trim(),
      'active' : $('input[name="active"]:checked').val()
    }

    if(h.price_list == "") {
      $('#price-list').hasError('Please select price list');
      click = 0;
      return false;
    }

    if(h.name.length == 0) {
      $('#name').hasError('Please enter description');
      click = 0;
      return false;
    }

    $.ajax({
      url:`${HOME}is_exists/${h.price_list}`,
      type:'GET',
      cache:false,
      success:function(rs) {
        if(rs.trim() !== 'exists') {
          $.ajax({
            url: `${HOME}add`,
            type: 'POST',
            cache: false,
            data: {
              'data': JSON.stringify(h)
            },
            success: function (rs) {
              load_out();
              click = 0;
              if (rs.trim() == 'success') {
                edit(h.price_list);
              }
              else {
                showError(rs);
              }
            },
            error: function (rs) {
              showError(rs);
            }
          });
        }
        else {
          $('#price-list').hasError('Price list already exists');
          click = 0;
          return false;          
        }
      }
    });     
  }  
}


function update() {
  clearErrorByClass('e');

  let h = {
    'price_list' : $('#price-list').val(),
    'price_list_name' : $('#price-list-name').val(),
    'name' : $('#name').val().trim(),
    'active' : $('input[name="active"]:checked').val()
  }

  if(h.name.length == 0) {
    $('#name').hasError('Please enter description');
    return false;
  }

  $.ajax({
    url: `${HOME}update`,
    type: 'POST',
    cache: false,
    data: {
      'data': JSON.stringify(h)
    },
    success: function (rs) {
      if(rs.trim() == 'success') {
        swal({
          title: 'Success',
          type: 'success',
          timer: 1000
        });
      }
      else {
        showError(rs);
      }
    },
    error: function (rs) {
      showError(rs);
    }
  });
}



function update_details() {
  clearErrorByClass('e');
  let err = 0;

  let d = {    
    'price_list' : $('#price-list').val(),    
    'rows' : [],
    'delete_rows' : []
  }  

  if($('.chk').length) {
    $('.chk').each(function() {
      let no = $(this).data('no');
      let id = $(this).data('id');
      let label = $('#label-'+no).val().trim();
      let stepQty = parseDefault(parseFloat($('#step-qty-'+no).val()), 0);
      let limitQty = parseDefault(parseFloat($('#limit-qty-'+no).val()), 0);
      let freeQty = parseDefault(parseFloat($('#free-qty-'+no).val()), 0);
      let active = $('#active-'+no).val();
      let force = $('#force-'+no).is(':checked') ? 1 : 0;
      let highlight = $('#highlight-'+no).is(':checked') ? 1 : 0;
      let position = parseDefault(parseInt($('#pos-'+no).val()), 0);

      if(label.length == 0) {
        $('#label-'+no).hasError();
        err++;
      }

      if(stepQty <= 0) {
        $('#step-qty-'+no).hasError();
        err++;
      }

      if(freeQty < 0) {
        $('#free-qty-'+no).hasError();
        err++;
      }

      if(limitQty < 0) {
        $('#limit-qty-'+no).hasError();
        err++;
      }

      let row = {
        'id' : id,
        'label' : label,
        'stepQty' : stepQty,
        'freeQty' : freeQty,
        'limitQty' : limitQty,
        'active' : active,
        'is_force' : force,
        'highlight' : highlight,
        'position' : position
      };

      d.rows.push(row);
    });
  }

  $('.delete-row').each(function() {
    d.delete_rows.push($(this).val());
  })

  if(err > 0) {
    return false;
  }

  load_in();

  $.ajax({
    url:`${HOME}update_details`,
    type:'POST',
    cache:false,
    data:{
      'data' : JSON.stringify(d)
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
        showError(rs);

      }
    },
    error:function(rs) {      
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
      url: `${HOME}delete`,
      type:'POST',
      cache:false,
      data:{
        'price_list' : id
      },
      success:function(rs){
        if(rs == 'success'){
          swal({
            title:'Success',
            text:'Price list has been deleted',
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
      error:function(rs){
        showError(rs);
      }
    })
  })
}


function toggleActive(id, el) {
  let active = $(el).is(':checked') ? 1 : 0;
  $.ajax({
    url: `${HOME}set_active`,
    type: 'POST',
    cache: false,
    data: {
      'id': id,
      'active': active
    },
    success: function(rs) {
      if(rs != 'success') {
        showError(rs);
      }
    },
    error: function(rs) {
      showError(rs);
    }
  });
}


function addRow() {
  let no = parseDefault(parseInt($('#top-row').val()), 0);

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
