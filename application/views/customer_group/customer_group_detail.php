<?php $this->load->view('include/header'); ?>
<style>
  .tableNarrow>tbody>tr>td {
    font-size: 12px;
    padding: 2px 5px !important;
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
    </p>
  </div>
</div><!-- End Row -->
<hr class="padding-5 margin-bottom-0" />
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <div class="form-horizontal">
      <div class="form-group margin-top-30">
        <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Code</label>
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
          <input type="text" id="code" class="width-100 e" maxlength="20" value="<?php echo $data->code; ?>" disabled />
          <input type="hidden" id="id" value="<?php echo $data->id; ?>" />
        </div>
        <div class="help-block col-xs-12 col-sm-reset inline red" id="code-error"></div>
      </div>
      <div class="form-group">
        <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Description</label>
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
          <input type="text" id="name" class="width-100 e" maxlength="50" value="<?php echo $data->name; ?>" autocomplete="off" disabled />
        </div>
        <div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
      </div>

      <div class="form-group">
        <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Status</label>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-top-7">
          <?php if ($data->active == 1) : ?>
            <span class="green"><i class="fa fa-check"></i> Active</span>
          <?php else : ?>
            <span class="red"><i class="fa fa-times"></i> Inactive</span>
          <?php endif; ?>
        </div>          
        <div class="help-block col-xs-12 col-sm-reset inline red" id="status-error"></div>
      </div>
      
      <div class="divider"></div>
      <div class="divider-hidden"></div>

      <div class="form-group">
        <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Members</label>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
            <div class="col-lg-10 col-md-9 col-sm-9 col-xs-8 padding-5" style="padding-left: 0px;">
              <input type="text" class="form-control input-sm" id="group-search-box" placeholder="Search..." />
            </div>
            <div class="col-lg-2 col-md-3 col-sm-3 col-xs-4 padding-0 padding-left-5">
              <button type="button" class="btn btn-xs btn-primary btn-block" onclick="clearSearchText('group-search-box')"><i class="fa fa-refresh"></i> Clear</button>
            </div>
            <div class="divider-hidden"></div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
              Results: <span id="group-result"><?php echo empty($details) ? 0 : count($details); ?></span> records of <?php echo empty($details) ? 0 : number_format(count($details)); ?> records
            </div>
            <div class="divider-hidden"></div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-0 border-1" style="height:400px; overflow-x:hidden; overflow-y:scroll;">
              <table class="table table-bordered table-striped tableNarrow tableFixHead" id="group-list-table" style="margin-left:-1px; margin-top:-1px;">
                <thead>
                  <tr>
                    <th class="fix-width-40 text-center fix-header">#</th>
                    <th class="fix-width-120 fix-header">Code</th>
                    <th class="fix-header">Name</th>
                  </tr>
                </thead>
                <tbody id="group-list-body">
                  <?php if (!empty($details)) : ?>
                    <?php $no = 1; ?>
                    <?php foreach ($details as $rs) : ?>
                      <tr id="group-<?php echo $rs->id; ?>">
                        <td class="middle text-center no"><?php echo number($no); ?></td>
                        <td class="middle"><?php echo $rs->CardCode; ?></td>
                        <td class="middle"><?php echo $rs->CardName; ?></td>
                      </tr>
                      <?php $no++; ?>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div><!--/ col-lg-6  -->
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
    updateRowCount(tableSelector, "#group-result");
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
  const groupTable = "#group-list-table";
  const columnsToSearch = [1, 2]; // configurable   
  let groupTextSearch = "";  
  let groupCache = cacheTableData(groupTable);

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

  function applyGroupFilter() {
    const filtered = filterTable(groupCache, groupTextSearch, columnsToSearch);
    renderRows(groupTable, filtered);
    reIndex();
  }


  function clearSearchText(inputId) {
    const input = document.getElementById(inputId);
    if (input) {
      input.value = "";      
      groupTextSearch = "";
      applyGroupFilter();
    }
  }

  function updateRowCount(tableSelector, labelSelector) {
    const visibleRows = document.querySelectorAll(`${tableSelector} tbody tr`);
    document.querySelector(labelSelector).innerText = visibleRows.length;
  }
</script>
<script src=" <?php echo base_url(); ?>scripts/customer_group/customer_group.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>