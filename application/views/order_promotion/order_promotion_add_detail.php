<style>
  .table > tr > td {
    padding:3px;
  }
</style>
<?php $hide = $this->disSale ? "" : 'hide'; ?>
<div class="row">
  <div class="col-sm-12 col-xs-12 padding-5">
    <button type="button" class="btn btn-sm btn-info" onclick="addRow()">Add Row</button>
    <button type="button" class="btn btn-sm btn-warning" onclick="removeRow()">Delete Row</button>
  </div>
  <div class="divider-hidden">

  </div>
  <div class="col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-bordered" style="table-layout: fixed; min-width:100%; width:1310px; margin-bottom:10px;">
      <thead>
        <tr>
          <th class="middle text-center" style="width:50px;">#</th>
          <th class="middle text-center" style="width:200px;">รายการสินค้า</th>
          <th class="middle text-center" style="width:80px;">In Stock</th>
          <th class="middle text-center" style="width:80px;">Committed</th>
          <th class="middle text-center" style="width:80px;">Available</th>
          <th class="middle text-center" style="width:80px;">จำนวน</th>
          <th class="middle text-center" style="width:80px;">แถม</th>
          <th class="middle text-center" style="width:80px;">หน่วย</th>
          <th class="middle text-center" style="width:100px;">ราคา(พิเศษ) /หน่วย</th>
          <th class="middle text-center <?php echo $hide; ?>" style="width:80px;">Discount Sales</th>
          <th class="middle text-center" style="width:100px;">มูลค่า</th>
          <th class="middle text-center" style="width:100px;">หมายเหตุ</th>
          <?php if($this->isAdmin) : ?>
            <th class="middle text-center" style="width:100px;">Free text</th>
          <?php endif; ?>
        </tr>
      </thead>
      <tbody id="details-template">
        <tr id="row-1">
          <td class="middle text-center"><input type="checkbox" class="ace chk" id="chk-1" value="1"/><span class="lbl"></span></td>
          <td class="middle">
            <input type="hidden" class="item-code" id="itemCode-1" data-no="1">
            <input type="hidden" class="item-vat-code" id="itemVatCode-1" data-no="1">
            <input type="hidden" class="item-vat-rate" id="itemVatRate-1" dta-no="1">
            <input type="hidden" class="whs" id="whsCode-1" data-no="1">
            <select class="width-100 input-item-code" id="item-1" data-no="1" onchange="getItemData(1)">
              <option value="">Select Promotion</option>
            </select>
          </td>
          <td class="middle"><input type="text" class="form-control input-sm text-right" id="instock-1" value="" disabled /></td>
          <td class="middle"><input type="text" class="form-control input-sm text-right" id="commit-1" value="" disabled/></td>
          <td class="middle"><input type="text" class="form-control input-sm text-right" id="available-1" value="" disabled/></td>
          <td class="middle">
            <input type="hidden" id="minQty-1" value="0">
            <input type="number" class="form-control input-sm text-right input-qty" id="qty-1" data-no="1" value="" onkeyup="recalAmount(1)" />
          </td>
          <td class="middle"><input type="number" class="form-control input-sm text-right" id="free-1" value="" disabled /></td>
          <td class="middle"><input type="text" class="form-control input-sm text-center" id="uom-1" value="" disabled /></td>
          <td class="middle">
            <input type="hidden" id="stdPrice-1" value="0.00"/>
            <input type="hidden" id="vatAmount-1" value="0.00">
            <input type="number" class="form-control input-sm text-right" id="price-1" value="" disabled onkeyup="recalAmount(1)"/>
          </td>
          <td class="middle text-center <?php echo $hide; ?>"><label><input type="checkbox" class="ace dis" id="dis-1" value="1" /><span class="lbl"></span></lable></td>
          <td class="middle"><input type="number" class="form-control input-sm text-right" id="amount-1" value="" disabled/></td>
          <td class="middle"><input type="text" class="form-control input-sm" id="remark-1" maxlength="100" value="" /></td>
          <?php if($this->isAdmin) : ?>
            <td class="middle"><input type="text" class="form-control input-sm" id="freeTxt-1" maxlength="100" value="" /></td>
          <?php endif; ?>
        </tr>
      </tbody>
    </table>
  </div>
  <div class="divider-hidden">  </div>
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <span class="red">*กรณีราคาพิเศษ/หน่วย ราคาต่ำกว่า ราคา/หน่วย ระบบจะส่งอนุมัติก่อนเสมอ</span>
  </div>
</div>
<input type="hidden" id="top-row" value="1">
<hr class="padding-5 margin-top-15 margin-bottom-15"/>
<script id="row-template" type="text/x-handlebarsTemplate">
<tr id="row-{{no}}">
  <td class="middle text-center"><input type="checkbox" class="ace chk" id="chk-{{no}}" value="{{no}}"/><span class="lbl"></span></td>
  <td class="middle">
    <input type="hidden" class="item-code" id="itemCode-{{no}}" data-no="{{no}}">
    <input type="hidden" class="item-vat-code" id="itemVatCode-{{no}}" data-no="{{no}}">
    <input type="hidden" class="item-vat-rate" id="itemVatRate-{{no}}" dta-no="{{no}}">
    <input type="hidden" class="whs" id="whsCode-{{no}}" data-no="{{no}}">
    <select class="width-100 input-item-code" id="item-{{no}}" data-no="{{no}}" onchange="getItemData({{no}})">
      <option value="">Select Products</option>
      {{#if promotion_items}}
        {{#each promotion_items}}
          <option value="{{code}}">{{name}}</option>
        {{/each}}
        {{/if}}
    </select>
  </td>
  <td class="middle"><input type="text" class="form-control input-sm text-right" id="instock-{{no}}" value="" disabled /></td>
  <td class="middle"><input type="text" class="form-control input-sm text-right" id="commit-{{no}}" value="" disabled/></td>
  <td class="middle"><input type="text" class="form-control input-sm text-right" id="available-{{no}}" value="" disabled/></td>
  <td class="middle">
    <input type="hidden" id="minQty-{{no}}" value="0">
    <input type="number" class="form-control input-sm text-right input-qty" id="qty-{{no}}" data-no="{{no}}" value="" onkeyup="recalAmount({{no}})"/>
  </td>
  <td class="middle"><input type="number" class="form-control input-sm text-right" id="free-{{no}}" value="" disabled/></td>
  <td class="middle"><input type="text" class="form-control input-sm text-center" id="uom-{{no}}" value="" disabled /></td>
  <td class="middle">
    <input type="hidden" id="stdPrice-{{no}}" value="0.00">
    <input type="hidden" id="vatAmount-{{no}}" value="0.00">
    <input type="number" class="form-control input-sm text-right" id="price-{{no}}" value="" disabled onkeyup="recalAmount({{no}})"/>
  </td>
  <td class="middle text-center <?php echo $hide; ?>"><label><input type="checkbox" class="ace dis" id="dis-{{no}}" value="1" /><span class="lbl"></span></lable></td>
  <td class="middle"><input type="number" class="form-control input-sm text-right" id="amount-{{no}}" value="" disabled/></td>
  <td class="middle"><input type="text" class="form-control input-sm text-right" id="remark-{{no}}" maxlength="100" value="" /></td>
  <?php if($this->isAdmin) : ?>
    <td class="middle"><input type="text" class="form-control input-sm" id="freeTxt-{{no}}" maxlength="100" value="" /></td>
  <?php endif; ?>
</tr>
</script>
<!--
<div class="input-group width-100">
  <input type="text" class="form-control input-sm input-item-code" id="item-{{no}}" data-no="{{no}}" />
  <span class="input-group-addon" onclick="clearText({{no}})">x</span>
</div>
-->
