var HOME = BASE_URL + 'price_list_group/';
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
  window.location.href = `${HOME}view_detail/${id}`;
}

function add() {
  if( click == 0) {
    click = 1;
    let h = {
      'name' : $('#name').val().trim(),
      'active' : $('input[name="status"]:checked').val(),
      'lists' : []
    }

    $('.chk').each(function() {
      if($(this).is(':checked')) {
        h.lists.push($(this).val());
      }
    });

    if(h.name === '') {
      swal('Required', 'Please enter group name', 'warning');
      click = 0;
      return false;
    }

    if(h.lists.length === 0) {
      swal('Required', 'Please select at least one price list', 'warning');
      click = 0;
      return false;
    }

    load_in();
    $.ajax({
      url: HOME + 'add',
      type: 'POST',
      cache: false,
      data: {
        'data' : JSON.stringify(h)
      },
      success: function(rs) {
        click = 0;
        load_out();
        if (rs.trim() == 'success') {
          swal({
            title: 'Success',
            text: 'Price list group added successfully<br/>Do you want to add another group?',
            type: 'success',
            html: true,
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
            closeOnConfirm: true
          }, function(isConfirm) {
            if (isConfirm) {
              window.location.href = `${HOME}add_new`;
            } else {
              window.location.href = HOME;
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
  let h = {
    'id' : $('#id').val(),
    'name' : $('#name').val().trim(),
    'active' : $('input[name="status"]:checked').val(),
    'lists' : []
  }

  $('.chk').each(function() {
    if($(this).is(':checked')) {
      h.lists.push($(this).val());
    }
  });

  if(h.name === '') {
    swal('Required', 'Please enter group name', 'warning');
    return false;
  }

  if(h.lists.length === 0) {
    swal('Required', 'Please select at least one price list', 'warning');
    return false;
  }

  load_in();
  $.ajax({
    url: `${HOME}update`,
    type: 'POST',
    cache: false,
    data: {
      'data' : JSON.stringify(h)
    },
    success: function(rs) {
      load_out();
      if (rs.trim() == 'success') {
        swal({
          title: 'Success',
          text: 'Price list group updated successfully',
          type: 'success',
          html: true,
          timer: 1000          
        });
      } 
      else {
        showError(rs);
      }
    },
    error: function(rs) {
      showError(rs);
    }
  });
}

function confirmDelete(id, name) {
  swal({
    title: 'Are you sure?',
    text: `Do you want to delete ${name}?`,
    type: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#DD6B55',
    confirmButtonText: 'Yes, delete it!',
    cancelButtonText: 'No, cancel',
    closeOnConfirm: true
  }, function() {
    setTimeout(() => {
      doDelete(id);
    }, 100);
  });     
}


function doDelete(id) {
  $.ajax({
    url: `${HOME}delete`,
    type: 'POST',
    cache: false,
    data: {
      'id': id
    },
    success: function(rs) {
      load_out();
      if (rs.trim() == 'success') {
        swal({
          title: 'Deleted!',
          text: 'Price list group has been deleted.',
          type: 'success',
          html: true,
          timer: 1000
        });

        $(`#row-${id}`).remove();
        reIndex();
      } 
      else {
        showError(rs);
      }
    },
    error: function(rs) {
      showError(rs);
    }
  });
}

function toggleActive(id, el) {
  let active = el.checked ? 1 : 0;

  $.ajax({
    url: HOME + 'setActive',
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

$('#chk-all').change(function () {
  if ($(this).is(':checked')) {
    $('.chk').prop('checked', true);
  } else {
    $('.chk').prop('checked', false);
  }
});

