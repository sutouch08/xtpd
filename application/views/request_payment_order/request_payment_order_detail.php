<?php $this->load->view('include/header'); ?>
<style>
  .label-sm {
    font-size: 12px;
    margin-bottom: 0px;
    margin-top: 5px;
  }
</style>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <h4 class="title"><?php echo $this->title; ?></h4>
  </div>
</div><!-- End Row -->
<hr class="padding-5" />

<div class="row">
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4">
    <label class="label-sm">Web Order No</label>
    <input type="text" class="form-control input-sm text-center" id="payment-order-code" value="<?php echo $data->code; ?>" readonly>
  </div>
  <div class="col-lg-4-harf col-md-6 col-sm-4-harf col-xs-8">
    <label class="label-sm">Customer</label>
    <input type="text" class="form-control input-sm" id="payment-customer" value="<?php echo $data->CardCode . ' | ' . $data->CardName; ?>" readonly>
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="label-sm">DocTotal</label>
    <input type="text" class="form-control input-sm text-right" id="payment-doc-total" value="<?php echo number($data->DocTotal, 2); ?>" readonly>
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6">
    <label class="label-sm">Credit Diff.</label>
    <input type="text" class="form-control input-sm text-right" id="payment-credit-diff" value="<?php echo number($data->credit_diff, 2); ?>" readonly>
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6">
    <label class="label-sm">Overdue</label>
    <input type="text" class="form-control input-sm text-right" id="payment-overdue-total" value="<?php echo number($data->overdue_total, 2); ?>" readonly>
  </div>
  <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
    <label class="label-sm">Request by</label>
    <input type="text" class="form-control input-sm" id="payment-user" value="<?php echo $data->request_by; ?>" readonly>
  </div>
  <div class="col-lg-2 col-md-2 col-sm-3 col-xs-6">
    <label class="label-sm">Request Date</label>
    <input type="text" class="form-control input-sm text-center" id="payment-date" value="<?php echo thai_date($data->date_add, TRUE); ?>" readonly>
  </div>
  <div class="col-lg-12 col-md-7 col-sm-12 col-xs-12">
    <label class="label-sm">Message</label>
    <input type="text" class="form-control input-sm" id="payment-message" value="<?php echo $data->message; ?>" readonly>
  </div>
  <div class="divider"></div>
</div>
<div class="row">
  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-12">
    <button type="button" class="btn btn-white btn-sm btn-primary btn-block" style="height: 30px;" onclick="showImportModal()"><i class="fa fa-plus"></i>&nbsp; Add File</button>
  </div>
  <div class="col-lg-10-harf col-md-10-harf col-sm-10 col-xs-12">
    <div class="input-group">
      <input type="text" class="form-control input-sm" id="reply-message" placeholder="Reply Message : <?php echo $data->reply_message; ?>" />
      <span class="input-group-btn">
        <button type="button" class="btn btn-white btn-sm btn-success" style="height: 30px;" onclick="submitReply()"><i class="fa fa-reply"></i>&nbsp; Reply</button>
      </span>
    </div>
  </div>

  <div class="divider" style="margin-top:5px;"></div>
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="file-list">
    <table class="table table-striped tableNarrow border-1">
      <thead>
        <tr>
          <th class="fix-width-40 text-center">#</th>
          <th class="fix-width-80">Actions</th>
          <th class="min-width-250">File Name</th>
          <th class="fix-width-100 text-right">Size</th>
          <th class="fix-width-130">Date</th>
        </tr>
      </thead>
      <tbody id="file-table">
        <?php if (!empty($data->files)) : ?>
          <?php $no = 1; ?>
          <?php foreach ($data->files as $rs) : ?>
            <tr id="row-<?php echo $no; ?>">
              <td class="middle text-center no"><?php echo $no; ?></td>
              <td class="middle">
                <button type="button" class="btn btn-white btn-minier btn-info" title="View File" onclick="viewFile('<?php echo $data->code; ?>', '<?php echo $rs->name; ?>')"><i class="fa fa-eye"></i></button>
                <button type="button" class="btn btn-white btn-minier btn-danger" title="Delete File" onclick="confirmDeleteFile(<?php echo $no; ?>, '<?php echo $data->code; ?>', '<?php echo $rs->name; ?>')"><i class="fa fa-trash"></i></button>
                <button type="button" class="btn btn-white btn-minier btn-success" title="Download File" onclick="downloadFile('<?php echo $data->code; ?>', '<?php echo $rs->name; ?>')"><i class="fa fa-download"></i></button>
              </td>
              <td class="middle">
                <?php echo $rs->name; ?>
                <input type="hidden" class="attached-file" value="<?php echo $rs->name; ?>" />
              </td>
              <td class="middle text-right"><?php echo $rs->size; ?></td>
              <td class="middle"><?php echo thai_date($rs->date_modify, TRUE); ?></td>
            </tr>
            <?php $no++; ?>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php $this->load->view('request_payment_order/attach_file_modal'); ?>

<script src="<?php echo base_url(); ?>scripts/request_payment_order/request_payment_order.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>