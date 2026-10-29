var HOME = `${BASE_URL}customer/`;

function goBack() {
  window.location.href = HOME;
}

function viewDetail(id) {
  window.location.href = `${HOME}view_detail/${id}`;
}

function edit(id) {
  window.location.href = `${HOME}edit/${id}`;
}

function syncData(all = 0) {
  load_in();
  setTimeout(() => {
    $.ajax({
      url: `${HOME}syncData`,
      type: 'POST',
      cache: false,
      data: {
        all: all
      },
      success: function (rs) {
        load_out();

        if (rs.trim() === 'success') {
          swal({
            title: 'Success',
            type: 'success',
            timer: 1000
          });

          setTimeout(() => {
            window.location.reload();
          }, 1200);
        }
        else {
          showError(rs);
        }
      },
      error: function (rs) {
        load_out();
        showError(rs);
      }
    });
  }, 200);
}
