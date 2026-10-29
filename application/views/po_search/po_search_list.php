<?php $this->load->view('include/header'); ?>
<style>
  .form-group {
    margin-bottom: 5px;
  }
</style>

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <h4 class="title"><?php echo $this->title; ?></h4>
  </div>
</div><!-- End Row -->
<hr class="padding-5" />
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
  <div class="row">
    <div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
      <label>Invoice No.</label>
      <input type="text" class="form-control input-sm text-center search-box" name="inv_code" value="<?php echo $inv_code; ?>" placeholder="Invoice No." />
    </div>

    <div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
      <label>PO No.</label>
      <input type="text" class="form-control input-sm text-center search-box" name="po" value="<?php echo $po; ?>" placeholder="PO Number" />
    </div>
    <div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
      <label>SO No.</label>
      <input type="text" class="form-control input-sm text-center search-box" name="so_code" value="<?php echo $so_code; ?>" placeholder="SO No." />
    </div>

    <div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
      <label>Customer</label>
      <input type="text" class="form-control input-sm text-center search-box" name="customer" value="<?php echo $customer; ?>" placeholder="Code OR Name" />
    </div>

    <div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
      <label>Invoice Date</label>
      <div class="input-daterange input-group width-100">
        <input type="text" class="form-control input-sm width-50 from-date text-center" id="fromDate" name="fromDate" value="<?php echo $fromDate; ?>" placeholder="From" readonly />
        <input type="text" class="form-control input-sm width-50 to-date text-center" id="toDate" name="toDate" value="<?php echo $toDate; ?>" placeholder="To" readonly />
      </div>
    </div>

    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
      <label>Attachment</label>
      <select class="form-control input-sm filter" name="has_file">
        <option value="all" <?php echo is_selected('all', $has_file); ?>>All</option>
        <option value="1" <?php echo is_selected('1', $has_file); ?>>Yes</option>
        <option value="0" <?php echo is_selected('0', $has_file); ?>>No</option>
      </select>
    </div>

    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
      <label class="display-block not-show">buton</label>
      <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
    </div>
    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
      <label class="display-block not-show">buton</label>
      <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
    </div>
  </div>

  <input type="hidden" name="search" value="1" />
</form>

<hr class="margin-top-15 padding-5">
<?php echo $this->pagination->create_links(); ?>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped tableNarrow border-1" style="min-width:1000px;">
      <thead>
        <tr>
          <th class="fix-width-40 text-center">#</th>
          <th class="fix-width-40 text-center">Files</th>
          <th class="fix-width-100">SO No.</th>
          <th class="fix-width-80">Invoice Date</th>
          <th class="fix-width-100">Invoice No.</th>
          <th class="fix-width-150">PO No.</th>
          <th class="min-width-200">Customer</th>
          <th class="fix-width-100">User</th>
          <th class="fix-width-100">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($data)) : ?>
          <?php $no = $this->uri->segment($this->segment) + 1; ?>
          <?php foreach ($data as $rs) : ?>
            <tr>
              <td class="middle text-center"><?php echo $no; ?></td>
              <td class="middle text-center"><?php echo $rs->has_file ? 'Y' : 'N'; ?></td>
              <td class="middle"><?php echo $rs->soCode; ?></td>
              <td class="middle"><?php echo thai_date($rs->InvoiceDate, FALSE); ?></td>
              <td class="middle"><?php echo $rs->InvoiceNo; ?></td>
              <td class="middle"><?php echo $rs->poCode; ?></td>
              <td class="middle"><?php echo $rs->CardCode; ?> | <?php echo $rs->CardName; ?></td>
              <td class="middle"><?php echo $rs->uname; ?></td>
              <td class="middle">
                <?php if ($rs->has_file) : ?>
                  <button type="button" class="btn btn-minier btn-primary" title="Preview" onclick="openFile('<?php echo $rs->file_name; ?>')"><i class="fa fa-eye"></i></button>
                  <button type="button" class="btn btn-minier btn-success" title="Download" onclick="downloadFile('<?php echo $rs->file_name; ?>')"><i class="fa fa-download"></i></button>
                  <button type="button" class="btn btn-minier btn-info" title="แจ้งผู้แทน" onclick="printFile('<?php echo $rs->file_name; ?>')"><i class="fa fa-print"></i></button>
                <?php endif; ?>
              </td>
            </tr>
            <?php $no++; ?>
          <?php endforeach; ?>
        <?php else : ?>
          <tr>
            <td colspan="8" class="middle text-center">No records found</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
  $('#user-id').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/po_search/po_search.js?v=<?php echo date('YmdH'); ?>"></script>

<?php $this->load->view('include/footer'); ?>