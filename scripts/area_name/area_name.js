var HOME = BASE_URL + 'area_name/';

function goBack() {
  window.location.href = HOME;
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
