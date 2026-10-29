var HOME = BASE_URL + 'price_list/';

function goBack() {
  window.location.href = HOME;
}

function toggleActive(id, el) {
  let active = el.checked ? 1 : 0;  

  $.ajax({
    url:HOME + 'setActive',
    type:'POST',
    cache:false,
    data:{
      'id' : id,
      'active' : active
    },
    success:function(rs) {
      load_out();
      if(rs.trim() != 'success') {
        showError(rs);
        el.checked = !el.checked;        
      }      
    },
    error:function(rs) {      
      showError(rs);
    }
  });
}


function updatePosition(id, el) {
  let position = el.value;

  $.ajax({
    url:HOME + 'updatePosition',
    type:'POST',
    cache:false,
    data:{
      'id' : id,
      'position' : position
    },
    success:function(rs) {
      load_out();
      if(rs.trim() != 'success') {
        showError(rs);
        el.value = el.defaultValue;
      } else {
        el.defaultValue = position;
      }
    },
    error:function(rs) {
      showError(rs);
      el.value = el.defaultValue;
    }
  });
}


function syncData() {
  load_in();
  setTimeout(() => {
    $.ajax({
      url:HOME + 'syncData',
      type:'POST',
      cache:false,
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
    });
  }, 200);
}
