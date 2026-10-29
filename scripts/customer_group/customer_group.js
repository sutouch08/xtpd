var HOME = `${BASE_URL}customer_group/`;
var click = 0;

function goBack() {
  window.location.href = HOME;
}

function addNew() {
  window.location.href = `${HOME}add_new`;
}

function viewDetail(id) {
  window.location.href = `${HOME}view_detail/${id}`;
}

function edit(id) {
  window.location.href = `${HOME}edit/${id}`;
}

function changeURL(id, tab) {
  var url = `${HOME}edit/${id}/${tab}`;
  var stObj = { stage: 'stage' };
  window.history.pushState(stObj, 'customer_group', url);
}

function add() {
  if(click == 0) {
    click = 1;
    let code = $('#code').val();
    let name = $('#name').val();
    let status = $('input[name="status"]:checked').val();

    clearErrorByClass('e');

    if(code.length == 0) {
      set_error($('#code'), $('#code-error'), 'Required');
      click = 0;
      return false;
    }

    if(name.length == 0) {
      set_error($('#name'), $('#name-error'), 'Required');
      click = 0;
      return false;
    }

    $.ajax({
      url: `${HOME}add`,
      type: 'POST',
      cache: false,
      data: {
        'code': code,
        'name': name,
        'status': status
      },
      success: function(rs) {
        click = 0;
        if(rs.trim() == 'success') {
          swal({
            title: 'Success',
            text: 'Customer group has been added. <br/>Do you want to add another one ?',
            type: 'success',
            html: true,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
            closeOnConfirm: true            
          }, function(isConfirm) {
            if(isConfirm) {
              window.location.reload();
            } else {
              goBack();
            }
          });
        }
        else {
          showError(rs);
        }
      },
      error: function(rs) {
        click = 0;
        showError(rs);
      }
    });
  }
}

function update() {
  let id = $('#id').val();
  let code = $('#code').val();
  let name = $('#name').val();
  let status = $('input[name="status"]:checked').val();

  clearErrorByClass('e');

  if(code.length == 0) {
    set_error($('#code'), $('#code-error'), 'Required');
    click = 0;
    return false;
  }

  if(name.length == 0) {
    set_error($('#name'), $('#name-error'), 'Required');
    click = 0;
    return false;
  }

  $.ajax({
    url: `${HOME}update`,
    type: 'POST',
    cache: false,
    data: {
      'id': id,
      'code': code,
      'name': name,
      'status': status
    },
    success: function(rs) {
      click = 0;
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
    error: function(rs) {
      click = 0;
      showError(rs);
    }
  });
}


function updateGroupDetails() {
  if(click == 0) {
    click = 1;
    const id = document.querySelector("#id").value;
    let lists = [];

    const rows = document.querySelectorAll("#group-list-table tbody .row-check");
    rows.forEach((row) => {
      lists.push({
        id: row.value,
        code: row.dataset.code
      });
    });

    $.ajax({
      url: `${HOME}update_group_details`,
      type: 'POST',
      cache: false,
      data: {
        'id': id,
        'lists': JSON.stringify(lists)
      },
      success: function(rs) {
        click = 0;
        if(rs.trim() == 'success') {
          swal({
            title: 'Success',
            text: 'Group details have been updated.',
            type: 'success',
            timer: 1000
          });
        }
        else {
          showError(rs);
        }
      },
      error: function(rs) {
        click = 0;
        showError(rs);
      }
    });
  }
}
      
function getImportTemplate() {
  const url = `${HOME}get_import_template`;
  window.location.href = url;
}


function toggleActive(id, el) {
  let active = el.checked ? 1 : 0;

  $.ajax({
    url: `${HOME}setActive`,
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