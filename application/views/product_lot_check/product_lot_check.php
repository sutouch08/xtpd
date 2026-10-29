<?php $this->load->view('include/header'); ?>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <h4 class="title text-center">
      <span class="pull-left">
        <?php echo $this->title; ?>
      </span>
      <span class="font-size-20 red" style="padding-left:10px; padding-right:10px;">ข้อมูลใช้สำหรับประกอบการขายเท่านั้น บริษัทขอสงวนสิทธิ์ในการเลือก Lot.</span>
    </h4>
  </div>
</div><!-- End Row -->
<hr class="padding-5" />
<div class="row">
  <div class="col-lg-2 col-md-3 col-sm-3 col-xs-12 padding-5">
    <label>Price List</label>
    <select class="form-control input-sm r" name="priceList" id="priceList" onchange="getItemTemplate()">
      <option value="">Select Price List</option>
      <?php echo $priceList; ?>
    </select>
  </div>

  <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12 padding-5">
    <label>Items</label>
    <select class="form-control input-sm r" id="item">
      <option value="0">Select</option>
    </select>
  </div>

  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-primary btn-block" onclick="getData()">Search</button>
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearData()">Clear</button>
  </div>
</div>
<hr class="margin-top-15 padding-5">

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped table-bordered tableNarrow border-1" style="min-width:750px;">
      <thead>
        <tr>
          <th class="fix-width-50 middle text-center">#</th>
          <th class="min-width-200 middle">Description</th>
          <th class="fix-width-100 middle text-center">Whs. Code</th>
          <th class="fix-width-100 middle text-center">Lot No.</th>
          <th class="fix-width-100 middle text-center">Mfd. Date</th>
          <th class="fix-width-100 middle text-center">Exp. Date</th>
          <th class="fix-width-100 middle text-center">Qty.</th>
        </tr>
      </thead>
      <tbody id="item-table"> </tbody>
    </table>
  </div>
</div>

<script type="text/x-handlebarTemplate" id="item-template">
  {{#each this}}
    {{#if nodata}}
      <tr>
        <td colspan="7" class="text-center">---{{nodata}} ----</td>
      </tr>
    {{else}}
      <tr>
        <td class="text-center">{{no}}</td>
        <td class="">{{description}}</td>
        <td class="text-center">{{whsCode}}</td>
        <td class="text-center">{{lotNo}}</td>
        <td class="text-center">{{mfdDate}}</td>
        <td class="text-center">{{expDate}}</td>
        <td class="text-center">{{qty}}</td>
      </tr>
    {{/if}}
  {{/each}}
</script>

<script src="<?php echo base_url(); ?>scripts/product_lot_check/product_lot_check.js?v=<?php echo date('Ymd'); ?>"></script>
<script>
  $('#item').select2();
</script>

<?php $this->load->view('include/footer'); ?>