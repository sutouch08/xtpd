<?php $this->load->view('include/header'); ?>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <h4 class="title"><?php echo $this->title; ?></h4>
  </div>
</div><!-- End Row -->
<hr class="padding-5" />
<div class="row">
  <div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-6 padding-right-0">
    <label>Price List Type</label>
    <select class="form-control input-sm" id="price-list-type" onchange="changePriceListType()">
      <option value="all">All</option>
      <?php echo select_price_list_type(); ?>
    </select>
  </div>
  <div class="col-lg-2 col-md-3 col-sm-3 col-xs-12 padding-5">
    <label>Special Price List</label>
    <select class="form-control input-sm r" name="priceList" id="priceList" onchange="getItemTemplate()">
      <option value="">Select</option>
      <?php if (!empty($priceList)) : ?>
        <?php foreach ($priceList as $label => $list) : ?>
          <optgroup label="<?php echo $label; ?>">
            <?php foreach ($list as $pl) : ?>
              <option value="<?php echo $pl->id; ?>" data-spid="<?php echo $pl->spid; ?>"><?php echo $pl->name; ?></option>
            <?php endforeach; ?>
          </optgroup>
        <?php endforeach; ?>
      <?php endif; ?>
    </select>
  </div>

  <div class="col-lg-3-harf col-md-3 col-sm-4 col-xs-12 padding-5">
    <label>Customers</label>
    <select id="customer" class="form-control input-sm r" onchange="updatePriceList()">
      <option value="" id="count-cust">Select Customer (<?php echo empty($customer) ? 0 : count($customer); ?>)</option>
      <?php if (! empty($customer)) : ?>
        <?php foreach ($customer as $cs) : ?>
          <option value="<?php echo $cs->CardCode; ?>"
            data-code="<?php echo $cs->CardCode; ?>"
            data-name="<?php echo $cs->CardName; ?>"
            data-hcode="<?php echo $cs->CustCode; ?>"
            data-groupnum="<?php echo $cs->GroupNum; ?>"
            data-currency="<?php echo $cs->Currency; ?>"
            data-sale="<?php echo $cs->SlpCode; ?>"
            data-vat="<?php echo $cs->ECVatGroup; ?>"
            data-rate="<?php echo $cs->Rate; ?>"
            data-type="<?php echo $cs->customer_type; ?>"
            data-control="<?php echo $cs->isControl == '1' ? 'Y' : 'N'; ?>"
            data-saleteam="<?php echo $cs->saleTeam; ?>"
            data-saleperson="<?php echo $cs->salePerson; ?>"
            data-department="<?php echo $cs->department; ?>"
            data-area="<?php echo $cs->areaId; ?>"
            data-regular="<?php echo $cs->isRegular; ?>">
            <?php echo $cs->CardCode; ?> <?php echo $cs->CardName; ?>
          </option>
        <?php endforeach; ?>
      <?php endif; ?>
    </select>
  </div>

  <div class="col-lg-3 col-md-5 col-sm-5 col-xs-12 padding-5">
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
    <table class="table table-striped table-bordered tableNarrow border-1" style="min-width:550px;">
      <thead>
        <tr>
          <th class="fix-width-50 middle text-center">#</th>
          <th class="min-width-200 middle">Description</th>
          <th class="fix-width-100 middle text-center">Min. Qty</th>
          <th class="fix-width-100 middle text-center">Price</th>
          <th class="fix-width-100 middle text-center">Free</th>          
        </tr>
      </thead>
      <tbody id="step-table"> </tbody>
    </table>
  </div>
</div>

<script type="text/x-handlebarTemplate" id="step-template">
  {{#each this}}
    {{#if nodata}}
      <tr>
        <td colspan="5" class="text-center">---{{nodata}} ----</td>
      </tr>
    {{else}}
      <tr>
        <td class="text-center">{{no}}</td>
        <td class="">{{description}}</td>
        <td class="text-center">{{Qty}}</td>
        <td class="text-center">{{Price}}</td>
        <td class="text-center">{{freeQty}}</td>
      </tr>
    {{/if}}
  {{/each}}
</script>

<script id="item-template" type="text/x-handlebarsTemplate">
  <option value="0">Select Item</option>
</script>


<script src="<?php echo base_url(); ?>scripts/price_list_item_check/price_list_item_check.js?v=<?php echo date('Ymd'); ?>"></script>
<script>
  $('#customer').select2();
  $('#item').select2();
</script>

<?php $this->load->view('include/footer'); ?>