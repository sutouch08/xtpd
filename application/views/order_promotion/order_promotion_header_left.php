<div class="col-lg-7 col-md-7 col-sm-7 col-xs-12 padding-5">
  <div class="form-horizontal">
    <div class="form-group">
      <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">V, Q</label>
      <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
        <select class="form-control input-sm" id="cardType" onchange="changeCustomerList()">
          <option value="V">V</option>
          <option value="Q">Q</option>
        </select>
      </div>
    </div>


    <div class="form-group">
      <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">รหัสลูกค้า</label>
      <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
        <select name="customer" id="customer" class="width-100" onchange="getAddress()">
          <option value="">Select Customer</option>
          <?php if(!empty($customer)) : ?>
            <?php foreach($customer as $cs) : ?>
              <option value="<?php echo $cs->CardCode; ?>" data-currency="<?php echo $cs->Currency; ?>" data-sale="<?php echo $cs->SlpCode; ?>" data-vat="<?php echo $cs->ECVatGroup; ?>" data-rate="<?php echo $cs->Rate; ?>"><?php echo $cs->CardCode . " ".$cs->CardName; ?></option>
            <?php endforeach; ?>
          <?php endif; ?>
        </select>
      </div>
    </div>



    <div class="form-group">
      <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">ที่อยู่ตามใบกำกับ</label>
      <div class="col-lg-2 col-md-2 col-sm-2 col-xs-4">
        <select class="form-control input-sm" id="billToCode" onchange="get_address_bill_to()"></select>
      </div>
      <div class="col-lg-7 col-md-7 col-sm-7 col-xs-8 padding-left-0">
        <textarea id="BillTo" class="autosize autosize-transition form-control" disabled></textarea>
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">สถานที่จัดส่ง</label>
      <div class="col-lg-2 col-md-2 col-sm-2 col-xs-4">
        <select class="form-control input-sm" id="shipToCode" onchange="get_address_ship_to()"></select>
      </div>
      <div class="col-lg-7 col-md-7 col-sm-7 col-xs-8 padding-left-0">
        <textarea id="ShipTo" class="autosize autosize-transition form-control" disabled></textarea>
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">สถานที่จัดส่งเพิ่มเติม</label>
      <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
        <textarea id="exShipTo" class="autosize autosize-transition form-control" maxlength="254" onkeyup="wordCount($(this), 2)"></textarea>
        <span class="pull-right grey"><span id="word-count-2">0</span>/254</span>
      </div>
    </div>

    
    <div class="form-group">
      <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Vat Group</label>
      <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
        <input type="text" class="form-control input-sm" id="VatGroup" value="" disabled>
        <input type="hidden" id="vatRate" value="">
      </div>
      <label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label no-padding-right">Currency</label>
      <div class="col-lg-2-harf col-md-2-harf col-sm-2-harf col-xs-6">
        <select class="form-control input-sm width-100" id="currency" onchange="getRate()" disabled>
          <?php echo select_currency(); ?>
        </select>
      </div>
      <div class="col-lg-2-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-left-0">
        <input type="number" class="form-control input-sm" id="currencyRate" value="1.00" disabled>
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Promotion</label>
      <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
        <select name="promotion" id="promotion" class="width-100" onchange="checkPromotionList()">
          <option value="">Select Promotion</option>
          <?php echo select_promotion(); ?>
        </select>
      </div>
    </div>

  </div>
</div>

<script>
  $('#customer').select2();
  $('#promotion').select2();
</script>
