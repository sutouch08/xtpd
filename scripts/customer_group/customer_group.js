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

// --- Debounce Utility ---
function debounce(fn, delay = 300) {
  let timer;
  return (...args) => {
    clearTimeout(timer);
    timer = setTimeout(() => fn.apply(null, args), delay);
  };
}

// --- Cache table data in memory ---
function cacheTableData(tableSelector) {
  const table = document.querySelector(tableSelector);
  const rows = [...table.querySelectorAll("tbody tr")];

  return rows.map(row => {
    const cells = [...row.querySelectorAll("td")].map(td => td.innerText.toLowerCase());
    return {
      row,
      cells
    };
  });
}

// --- Render filtered rows ---
function renderRows(tableSelector, rows) {
  const tbody = document.querySelector(tableSelector).querySelector("tbody");
  tbody.innerHTML = ""; // clear
  const fragment = document.createDocumentFragment();

  rows.forEach(item => fragment.appendChild(item.row));
  tbody.appendChild(fragment);

  // Update the result count
  if (tableSelector === "#customer-list-table") {
    updateRowCount(tableSelector, "#list-result");
  } else if (tableSelector === "#group-list-table") {
    updateRowCount(tableSelector, "#group-result");
  }
}

// --- Main Filter Function ---
function filterData(searchText, cachedData, columnsToSearch) {
  const text = searchText.trim().toLowerCase();

  if (text === "") return cachedData; // no filter

  return cachedData.filter(item =>
    columnsToSearch.some(col => item.cells[col].includes(text))
  );
}


// --- Setup ---  
const custTable = "#customer-list-table";
const groupTable = "#group-list-table";
const columnsToSearch = [1, 2]; // configurable 
let custTextSearch = "";
let groupTextSearch = "";
let customerCache = cacheTableData(custTable);
let groupCache = cacheTableData(groupTable);


document.querySelector("#customer-search-box").addEventListener(
  "input",
  debounce(e => {
    custTextSearch = e.target.value;
    applyCustFilter();
  }, 300)
);


document.querySelector("#group-search-box").addEventListener(
  "input",
  debounce(e => {
    groupTextSearch = e.target.value;
    applyGroupFilter();
  }, 300)
);


function filterTable(cache, searchText, columnsToSearch) {
  const text = searchText.trim().toLowerCase();
  if (text === "") return cache;

  return cache.filter(item =>
    columnsToSearch.some(col => item.cells[col].includes(text))
  );
}

function applyCustFilter(uncheckAll = false) {
  const filtered = filterTable(customerCache, custTextSearch, columnsToSearch);
  renderRows(custTable, filtered);

  if (uncheckAll) {
    const checkboxes = document.querySelectorAll("#customer-list-table .row-check");
    checkboxes.forEach(chk => chk.checked = false);
  }
}

function applyGroupFilter(uncheckAll = false) {
  const filtered = filterTable(groupCache, groupTextSearch, columnsToSearch);
  renderRows(groupTable, filtered);

  if (uncheckAll) {
    const checkboxes = document.querySelectorAll("#group-list-table .row-check");
    checkboxes.forEach(chk => chk.checked = false);
  }
}


function clearSearchText(inputId) {
  const input = document.getElementById(inputId);
  if (input) {
    input.value = "";
    if (inputId === "customer-search-box") {
      custTextSearch = "";
      applyCustFilter();
    } else if (inputId === "group-search-box") {
      groupTextSearch = "";
      applyGroupFilter();
    }
  }
}

function toggleAllGroups(checkbox) {
  const rows = document.querySelectorAll("#group-list-table tbody .row-check");
  rows.forEach(chk => chk.checked = checkbox.checked);
}

function toggleAllCust(checkbox) {
  const rows = document.querySelectorAll("#customer-list-table tbody .row-check");
  rows.forEach(chk => chk.checked = checkbox.checked);
}

function addToGroup() {
  moveSelected(customerCache, groupCache, "#customer-list-table", "#group-list-table");
  applyCustFilter(); // re-render customer list
  applyGroupFilter(true); // re-render group list and uncheck all checkboxes
}

function removeFromGroup() {
  moveSelected(groupCache, customerCache, "#group-list-table", "#customer-list-table");
  applyGroupFilter(); // re-render group list
  applyCustFilter(true); // re-render customer list and uncheck all checkboxes
}

function moveSelected(fromCache, toCache, fromSelector, toSelector) {
  const checkedRows = document.querySelectorAll(`${fromSelector} .row-check:checked`);

  checkedRows.forEach(chk => {
    const row = chk.closest("tr");

    const index = fromCache.findIndex(item => item.row === row);
    if (index !== -1) {
      const item = fromCache[index];

      // remove from origin/destination
      fromCache.splice(index, 1);

      // add to destination/origin
      toCache.push(item);
    }
  });
}

function updateRowCount(tableSelector, labelSelector) {
  const visibleRows = document.querySelectorAll(`${tableSelector} tbody tr`);
  document.querySelector(labelSelector).innerText = visibleRows.length;
}

function getFile() {
  $('#uploadFile').click();
}

const uploadFile = document.getElementById('uploadFile');

uploadFile.addEventListener('change', async function () {
  if (this.files.length > 0) {
    const file = this.files[0];

    if (file.size > 5000000) {
      swal("ขนาดไฟล์ใหญ่เกินไป", "ไฟล์แนบต้องมีขนาดไม่เกิน 5 MB", "error");
      this.value = '';
      return false;
    }

    await uploadfile(file);
  }
})


async function uploadfile(file) {
  const id = $('#id').val();
  const fd = new FormData();
  const url = `${HOME}import_details/${id}`;
  fd.append('uploadFile', file);

  const res = await fetch(url, {
    method: 'POST',
    body: fd
  });

  const result = await res.json();

  if (result && result.status) {
    const data = result;

    if (data.status === 'success') {
      swal({
        title: 'นำเข้าเรียบร้อยแล้ว',
        text: data.message,
        type: 'success',
        html: true
      },
        function () {
          window.location.reload();
        });
    } else {
      showError(data.message);
    }
  } else {
    showError('เกิดข้อผิดพลาดในการนำเข้าไฟล์');
  }
}

function exportGroupDetails() {
  const url = `${HOME}export_details`;
  const token = generateUID();
  $('#token').val(token);
  get_download(token);
  $('#export-form').submit();
}