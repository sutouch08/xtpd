var HOME = `${BASE_URL}payment_term_discount/`;


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
  const height = 750;
  const left = (screen.width - width) / 2;
  const top = (screen.height - height) / 2;

  window.open(url, '_blank', `width=${width},height=${height},left=${left},top=${top}`);  
}

function toggleCheckSpecialPriceListAll(el) {
  if(el.checked) {
    $('.sp-chk').prop('checked', true);
  }
  else {
    $('.sp-chk').prop('checked', false);
  }
}


function toggleCheckPriceListAll(el) {
  if(el.checked) {
    $('.pl-chk').prop('checked', true);
  }
  else {
    $('.pl-chk').prop('checked', false);
  }
}


function add() {
  clearErrorByClass('e');

  let h = {
    'GroupNum' : $('#payment-term').val(),
    'PymntGroup' : $('#payment-term option:selected').text(),
    'name' : $('#name').val().trim(),
    'DiscPrcnt' : parseDefault(parseFloat($('#disc').val()), 0),
    'position' : $('#position').val(),
    'canChange' : $('#allow-change').is(':checked') ? 1 : 0,
    'active' : $('input[name="active"]:checked').val(),
    'priceList' : []
  };

  if(h.GroupNum == "") {
    $('#payment-term').hasError('Required');
    return false;
  }

  if(h.name.length == 0) {
    $('#name').hasError('Required');
    return false;
  }

  if(h.DiscPrcnt < 0) {
    $('#disc').hasError('Discount must between 0 - 100');
    return false;
  }

  $('.chk').each(function() {
    if($(this).is(':checked')) {
      let ps = {
        'id' : $(this).val(),
        'special_price_id' : $(this).data('spid')
      };

      h.priceList.push(ps);
    }
  });

  load_in();

  $.ajax({
    url:`${HOME}add`,
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
          text:'สร้างรายการสำเร็จ ต้องการสร้างรายการอื่นต่อหรือไม่ ?',
          type:'success',
          showCancelButton:true,
          confirmButtonText:'Yes',
          cancelButtonText:'No'
        }, function(isConfirm) {
          if(isConfirm) {
            addNew();
          }
          else {
            goBack();
          }
        })
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


function update() {
  clearErrorByClass('e');

  let h = {
    'id' : $('#id').val(),
    'GroupNum' : $('#payment-term').val(),
    'PymntGroup' : $('#payment-term option:selected').text(),
    'name' : $('#name').val().trim(),
    'DiscPrcnt' : parseDefault(parseFloat($('#disc').val()), 0),
    'position' : $('#position').val(),
    'canChange' : $('#allow-change').is(':checked') ? 1 : 0,
    'active' : $('input[name="active"]:checked').val(),
    'priceList' : []
  };

  if(h.GroupNum == "") {
    $('#payment-term').hasError('Required');
    return false;
  }

  if(h.name.length == 0) {
    $('#name').hasError('Required');
    return false;
  }

  if(h.DiscPrcnt < 0) {
    $('#disc').hasError('Discount must between 0 - 100');
    return false;
  }

  $('.chk').each(function() {
    if($(this).is(':checked')) {
      let ps = {
        'id' : $(this).val(),
        'special_price_id' : $(this).data('spid')
      };

      h.priceList.push(ps);
    }
  });

  load_in();

  $.ajax({
    url:`${HOME}update`,
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
    title:'Are you sure ?',
    text:'Do you really want to delete '+ name +' ? <br/> This process cannot be undone.',
    type:'warning',
    showCancelButton: true,
    confirmButtonColor: '#FA5858',
    confirmButtonText: 'Delete',
    cancelButtonText: 'Cancel',
    closeOnConfirm: false,
    html:true
  },function(){
    $.ajax({
      url: `${HOME}delete`,
      type:'POST',
      cache:false,
      data:{
        'id' : id
      },
      success:function(rs){
        if(rs == 'success'){
          swal({
            title:'Success',
            text:'Payment Term list has been deleted',
            type:'success',
            timer: 1000
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


function toggleActive(id, el) {
  let active = el.checked ? 1 : 0;

  $.ajax({
    url: `${HOME}set_active`,
    type: 'POST',
    cache: false,
    data: {
      'id': id,
      'active': active
    },
    success: function (rs) {
      load_out();
      if (rs.trim() != 'success') {
        showError(rs);
        el.checked = !el.checked;
      }
    },
    error: function (rs) {
      showError(rs);
    }
  });
}