<?php $this->load->view('include/header'); ?>
<style>
  .tableNarrow>tbody>tr>td {
    font-size: 12px;
    padding: 2px 5px !important;
  }

  .box-title {    
    border:solid 1px #ccc;
    padding: 10px;
    background-color: #f5f5f5;
    margin-bottom: 30px;
  }
</style>
<?php
$tab = isset($tab) ? $tab : 'info';
$info = $tab === 'info' ? 'active in' : '';
$customers = $tab === 'customers' ? 'active in' : '';
?>
<div class="row">
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <h4 class="title"><?php echo $this->title; ?></h4>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <p class="pull-right top-p">
      <button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
      <button type="button" class="btn btn-sm btn-purple" title="Download Template" onclick="getImportTemplate()"><i class="fa fa-download"></i> Template</button>
    </p>
  </div>
</div><!-- End Row -->
<hr class="padding-5 margin-bottom-0" />
<div class="row">
  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12 padding-5 padding-right-0" style="padding-top:5px;">
    <ul id="myTab1" class="setting-tabs" style="margin-left:0px;">
      <li class="li-block <?php echo $info; ?>" onclick="changeURL(<?php echo $data->id; ?>, 'info')"><a href="#info" data-toggle="tab">Customer Group</a></li>
      <li class="li-block <?php echo $customers; ?>" onclick="changeURL(<?php echo $data->id; ?>, 'customers')"><a href="#customers" data-toggle="tab">Members</a></li>
    </ul>
  </div>
  <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12 padding-5" style="padding-top:5px; border-left:solid 1px #ccc; min-height:600px; max-height:1500px;">
    <div class="tab-content" style="border:0px;">
      <div class="tab-pane fade <?php echo $info; ?>" id="info">
        <div class="form-horizontal">
          <div class="form-group margin-top-30">
            <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Code</label>
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
              <input type="text" id="code" class="width-100 e" maxlength="20" value="<?php echo $data->code; ?>" autocomplete="off" />
              <input type="hidden" id="id" value="<?php echo $data->id; ?>" />
            </div>
            <div class="help-block col-xs-12 col-sm-reset inline red" id="code-error"></div>
          </div>
          <div class="form-group">
            <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Description</label>
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
              <input type="text" id="name" class="width-100 e" maxlength="50" value="<?php echo $data->name; ?>" autocomplete="off" />
            </div>
            <div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
          </div>

          <div class="form-group">
            <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Status</label>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-top-7">
              <label class="fix-width-100">
                <input type="radio" class="ace" name="status" value="1" <?php echo $data->active == 1 ? 'checked' : ''; ?> />
                <span class="lbl">&nbsp; &nbsp;Active</span>
              </label>
              <label class="fix-width-100">
                <input type="radio" class="ace" name="status" value="0" <?php echo $data->active == 0 ? 'checked' : ''; ?> />
                <span class="lbl">&nbsp; &nbsp;Inactive</span>
              </label>
            </div>
            <div class="help-block col-xs-12 col-sm-reset inline red" id="status-error"></div>
          </div>

          <div class="divider-hidden"></div>
          <div class="divider-hidden"></div>
          <div class="divider-hidden"></div>

          <div class="form-group">
            <div class="col-lg-7 col-lg-offset-3 col-md-9 col-lg-offset-3 col-sm-9 col-sm-offset-3 col-xs-12">
              <button type="button" class="btn btn-sm btn-success btn-100" onclick="update()">Update</button>
            </div>
          </div>
        </div>
      </div><!--/ tab-pane  -->

      <div class="tab-pane fade <?php echo $customers; ?>" id="customers">
        <div class="row">
          <div class="col-lg-5-harf col-md-5-harf col-sm-5-harf col-xs-12">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-0">
              <h5 class="box-title text-center">Members List</h5>
            </div>            
            <div class="col-lg-10 col-md-9 col-sm-9 col-xs-8 padding-5" style="padding-left: 0px;">
              <input type="text" class="form-control input-sm" id="group-search-box" placeholder="Search..." />
            </div>
            <div class="col-lg-2 col-md-3 col-sm-3 col-xs-4 padding-0 padding-left-5">
              <button type="button" class="btn btn-xs btn-primary btn-block" onclick="clearSearchText('group-search-box')"><i class="fa fa-refresh"></i> Clear</button>
            </div>
            <div class="divider-hidden"></div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
              Results: <span id="group-result"><?php echo empty($details) ? 0 : count($details); ?></span> records
            </div>
            <div class="divider-hidden"></div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-0 border-1" style="height:400px; overflow-x:hidden; overflow-y:scroll;">
              <table class="table table-striped tableNarrow tableFixHead" id="group-list-table" style="margin-left:-1px; margin-top:-1px;">
                <thead>
                  <tr>
                    <th class="fix-width-40 text-center fix-header">
                      <input type="checkbox" class="" onchange="toggleAllGroups(this)" />
                    </th>
                    <th class="fix-width-120 fix-header">Code</th>
                    <th class="fix-header">Name</th>
                  </tr>
                </thead>
                <tbody id="group-list-body">
                  <?php if (!empty($details)) : ?>
                    <?php foreach ($details as $rs) : ?>
                      <tr id="group-<?php echo $rs->id; ?>">
                        <td class="middle text-center">
                          <input type="checkbox" class="row-check" value="<?php echo $rs->id; ?>" data-code="<?php echo $rs->CardCode; ?>" />
                        </td>
                        <td class="middle"><?php echo $rs->CardCode; ?></td>
                        <td class="middle"><?php echo $rs->CardName; ?></td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>

          <div class="col-lg-1 col-md-1 col-sm-1 hidden-xs padding-5 text-center" style="padding-top: 200px;">
            <button type="button" class="btn btn-sm btn-primary btn-block margin-bottom-10" onclick="addToGroup()"><i class="fa fa-arrow-left"></i></button>
            <button type="button" class="btn btn-sm btn-danger btn-block" onclick="removeFromGroup()"><i class="fa fa-arrow-right"></i></button>
          </div>
          <div class="col-xs-12 visible-xs padding-5 text-center margin-top-10 margin-bottom-10">
            <button type="button" class="btn btn-sm btn-primary btn-50" onclick="addToGroup()"><i class="fa fa-arrow-up"></i></button>
            <button type="button" class="btn btn-sm btn-danger btn-50" onclick="removeFromGroup()"><i class="fa fa-arrow-down"></i></button>
          </div>

          <div class="col-lg-5-harf col-md-5-harf col-sm-5-harf col-xs-12">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-0">
              <h5 class="box-title text-center">Customer List</h5>
            </div>            
            <div class="col-lg-10 col-md-9 col-sm-9 col-xs-8 padding-5" style="padding-left: 0px;">
              <input type="text" class="form-control input-sm" id="customer-search-box" placeholder="Search..." />
            </div>
            <div class="col-lg-2 col-md-3 col-sm-3 col-xs-4 padding-0 padding-left-5">
              <button type="button" class="btn btn-xs btn-primary btn-block" onclick="clearSearchText('customer-search-box')"><i class="fa fa-refresh"></i> Clear</button>
            </div>
            <div class="divider-hidden"></div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
              Results: <span id="list-result"><?php echo empty($list) ? 0 : count($list); ?></span> records
            </div>
            <div class="divider-hidden"></div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-0 border-1" style="height:400px; overflow-x:hidden; overflow-y:scroll;">
              <table class="table table-striped tableNarrow tableFixHead" id="customer-list-table" style="margin-left:-1px; margin-top:-1px;">
                <thead>
                  <tr>
                    <th class="fix-width-40 text-center fix-header">
                      <input type="checkbox" class="" onchange="toggleAllCust(this)" />
                    </th>
                    <th class="fix-width-120 fix-header">Code</th>
                    <th class="fix-header">Name</th>
                  </tr>
                </thead>
                <tbody id="customer-list-body">
                  <?php if (!empty($list)) : ?>
                    <?php foreach ($list as $rs) : ?>
                      <tr id="cust-<?php echo $rs->id; ?>">
                        <td class="middle text-center">
                          <input type="checkbox" class="row-check" value="<?php echo $rs->id; ?>" data-code="<?php echo $rs->CardCode; ?>" />
                        </td>
                        <td class="middle"><?php echo $rs->CardCode; ?></td>
                        <td class="middle"><?php echo $rs->CardName; ?></td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="divider"></div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
          <button type="button" class="btn btn-white btn-success btn-100 top-btn" onclick="updateGroupDetails()"><i class="fa fa-save"></i> Save</button>
          <span>--- OR ---</span>
          <button type="button" class="btn btn-white btn-primary btn-100 top-btn" onclick="showImportModal()"><i class="fa fa-upload"></i> Import</button>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
          <?php $this->load->view('customer_group/import_customer'); ?>
        </div>
      </div><!--/ tab-pane  -->
    </div><!--/ tab-content  -->
  </div><!--/ col-lg-10  -->
</div><!--/ row  -->

<script>
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
</script>
<script src=" <?php echo base_url(); ?>scripts/customer_group/customer_group.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>