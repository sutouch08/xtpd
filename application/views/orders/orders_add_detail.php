<style>
  .tableFixHead tr>td {
    padding: 3px !important;
  }

  .highlight {
    font-style: italic;
    font-weight: bold;
    color: red;
  }

  span.clear {
    padding: 6px 12px;
    font-size: 14px;
    font-weight: normal;
    line-height: 1;
    color: #555555;
    text-align: center;
    background-color: #eeeeee;
    border: solid 1px #cccccc;
  }

  @media (min-width: 768px) {

    .fix-no {
      left: -1px;
      position: sticky;
    }

    .fix-item {
      left: 39px;
      position: sticky;
    }

    .fix-clear {
      left: 289px;
      position: sticky;
    }

    .fix-step {
      left: 329px;
      position: sticky;
    }

    td[scope=row] {
      background-color: #f8f8f8;
      border: 0 !important;
      outline: solid 1px #dddddd;
    }
  }
</style>
<?php $width = 1441; ?>
<?php $hide = $this->disSale ? "" : 'hide'; ?>
<?php $width += $this->disSale ? 80 : 0; ?>
<?php $width += $this->isAdmin ? 100 : 0; ?>

<div class="row">
  <div class="col-sm-12 col-xs-12 padding-5">
    <button type="button" class="btn btn-sm btn-info" onclick="addRow()">Add Row</button>
    <button type="button" class="btn btn-sm btn-warning" onclick="removeRow()">Delete Row</button>
  </div>
  <div class="divider-hidden"></div>

  <div class="col-sm-12 col-xs-12 padding-5 border-1 table-responsive" style="max-height:400px; padding-left:0px; padding-right:0px; padding-bottom:5px; margin-left:5px; margin-right:5px;">
    <table class="table table-bordered tableFixHead" style="margin-left: -1px; margin-top: -1px; width:<?php echo $width; ?>px; min-width:100% !important;">
      <thead>
        <tr>
          <th class="fix-width-40 middle text-center fix-no fix-header">#</th>
          <th class="fix-width-250 middle text-center fix-item fix-header">รายการสินค้า</th>
          <th class="fix-width-40 middle text-center fix-clear fix-header"></th>
          <th class="fix-width-150 middle text-center fix-step fix-header">Step</th>
          <th class="fix-width-80 middle text-center hide">Controlled</th>
          <th class="fix-width-80 middle text-center">In Stock</th>
          <th class="fix-width-80 middle text-center">Committed</th>
          <th class="fix-width-80 middle text-center">Available</th>
          <th class="fix-width-80 middle text-center">จำนวน</th>
          <th class="fix-width-80 middle text-center">แถม</th>
          <th class="fix-width-80 middle text-center">หน่วย</th>
          <th class="fix-width-100 middle text-center">ราคา/หน่วย (Term)</th>
          <th class="fix-width-100 middle text-center">ราคา(พิเศษ) /หน่วย</th>
          <th class="fix-width-80 middle text-center <?php echo $hide; ?>">Discount Sales</th>
          <th class="fix-width-100 middle text-center">มูลค่า</th>
          <th class="fix-width-100 middle text-center">หมายเหตุ</th>
          <?php if ($this->isAdmin) : ?>
            <th class="fix-width-100 middle text-center">Free text</th>
          <?php endif; ?>
        </tr>
      </thead>
      <tbody id="details-template">
        <tr id="row-1">
          <td class="middle text-center fix-no" scope="row"><input type="checkbox" class="ace chk" id="chk-1" value="1" /><span class="lbl"></span></td>
          <td class="middle fix-item" scope="row">
            <input type="hidden" class="item-code" id="itemCode-1" data-no="1">
            <input type="hidden" class="item-vat-code" id="itemVatCode-1" data-no="1">
            <input type="hidden" class="item-vat-rate" id="itemVatRate-1" dta-no="1">
            <input type="hidden" class="whs" id="whsCode-1" data-no="1">
            <input type="hidden" id="step-data-1" data-no="1">
            <div class="input-group width-100">
              <select class="width-100 input-item-code" id="item-1" data-no="1" onchange="getItemData(1)">
                <option value="0" data-no="1">กรุณาเลือกลูกค้า</option>
              </select>
            </div>
          </td>
          <td class="middle text-center fix-clear" scope="row">
            <span class="clear pointer" onclick="clearText(1)">x</span>
          </td>
          <td class="middle fix-step" scope="row">
            <select class="form-control input-sm step" id="step-1" data-no="1" onchange="updateStepQty(1)">
              <option value="0" data-stepqty="0" data-limit="0" data-freeqty="0" data-force="1">Choose Step</option>
            </select>
          </td>
          <td class="middle hide"><input type="text" class="form-control input-sm text-center is-control" id="control-1" data-no="1" value="" disabled /></td>
          <td class="middle"><input type="text" class="form-control input-sm text-right" id="instock-1" value="" disabled /></td>
          <td class="middle"><input type="text" class="form-control input-sm text-right" id="commit-1" value="" disabled /></td>
          <td class="middle"><input type="text" class="form-control input-sm text-right" id="available-1" value="" disabled /></td>
          <td class="middle"><input type="number" class="form-control input-sm text-right input-qty e" id="qty-1" data-no="1" value="" onchange="recalAmount(1)" disabled /></td>
          <td class="middle"><input type="number" class="form-control input-sm text-right e" id="free-1" value="" disabled /></td>
          <td class="middle"><input type="text" class="form-control input-sm text-center" id="uom-1" value="" disabled /></td>
          <td class="middle">
            <input type="number" class="form-control input-sm text-right e" id="stdPrice-1" value="" disabled />
            <input type="hidden" id="vatAmount-1" value="0.00">
          </td>
          <td class="middle"><input type="number" class="form-control input-sm text-right e" id="price-1" value="" onkeyup="recalAmount(1)" /></td>
          <td class="middle text-center <?php echo $hide; ?>"><label><input type="checkbox" class="ace dis" id="dis-1" value="1" /><span class="lbl"></span></lable>
          </td>
          <td class="middle"><input type="number" class="form-control input-sm text-right" id="amount-1" value="" disabled /></td>
          <td class="middle"><input type="text" class="form-control input-sm" id="remark-1" maxlength="100" value="" /></td>
          <?php if ($this->isAdmin) : ?>
            <td class="middle"><input type="text" class="form-control input-sm" id="freeTxt-1" maxlength="100" value="" /></td>
          <?php endif; ?>
        </tr>
      </tbody>
    </table>
  </div>
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <span class="red">*กรณีราคาพิเศษ/หน่วย ราคาต่ำกว่า ราคา/หน่วย ระบบจะส่งอนุมัติก่อนเสมอ</span>
  </div>
</div>
<input type="hidden" id="top-row" value="1">
<hr class="padding-5 margin-top-15 margin-bottom-15" />

<script id="row-template" type="text/x-handlebarsTemplate">
  <tr id="row-{{no}}">
    <td class="middle text-center fix-no" scope="row"><input type="checkbox" class="ace chk" id="chk-{{no}}" value="{{no}}"/><span class="lbl"></span></td>
    <td class="middle fix-item" scope="row">
      <input type="hidden" class="item-code" id="itemCode-{{no}}" data-no="{{no}}">
      <input type="hidden" class="item-vat-code" id="itemVatCode-{{no}}" data-no="{{no}}">
      <input type="hidden" class="item-vat-rate" id="itemVatRate-{{no}}" dta-no="{{no}}">
      <input type="hidden" class="whs" id="whsCode-{{no}}" data-no="{{no}}">
      <input type="hidden" id="step-data-{{no}}" data-no="{{no}}">
      <div class="input-group width-100">
      <select class="width-100 input-item-code e" id="item-{{no}}" data-no="{{no}}" onchange="getItemData({{no}})">
      </select>
      </div>
    </td>
    <td class="middle text-center fix-clear" scope="row">
      <span class="clear pointer" onclick="clearText({{no}})">x</span>
    </td>
    <td class="middle fix-step" scope="row">
      <select class="form-control input-sm step" id="step-{{no}}" data-no="{{no}}" onchange="updateStepQty({{no}})">
        <option value="0" data-stepqty="0" data-limit="0" data-freeqty="0" data-force="1">No Step</option>
      </select>
    </td>
    <td class="middle hide"><input type="text" class="form-control input-sm text-center is-control" id="control-{{no}}" data-no="{{no}}" value="" disabled /></td>
    <td class="middle"><input type="text" class="form-control input-sm text-right" id="instock-{{no}}" value="" disabled /></td>
    <td class="middle"><input type="text" class="form-control input-sm text-right" id="commit-{{no}}" value="" disabled/></td>
    <td class="middle"><input type="text" class="form-control input-sm text-right" id="available-{{no}}" value="" disabled/></td>
    <td class="middle"><input type="number" class="form-control input-sm text-right input-qty e" id="qty-{{no}}" data-no="{{no}}" value="" onchange="recalAmount({{no}})" disabled/></td>
    <td class="middle"><input type="number" class="form-control input-sm text-right e" id="free-{{no}}" value="" disabled/></td>
    <td class="middle"><input type="text" class="form-control input-sm text-center" id="uom-{{no}}" value="" disabled /></td>
    <td class="middle">
    <input type="number" class="form-control input-sm text-right e" id="stdPrice-{{no}}" value="" disabled/>
    <input type="hidden" id="vatAmount-{{no}}" value="0.00">
    </td>
    <td class="middle"><input type="number" class="form-control input-sm text-right e" id="price-{{no}}" value="" onkeyup="recalAmount({{no}})"/></td>
    <td class="middle text-center <?php echo $hide; ?>"><label><input type="checkbox" class="ace dis" id="dis-{{no}}" value="1" /><span class="lbl"></span></lable></td>
    <td class="middle"><input type="number" class="form-control input-sm text-right" id="amount-{{no}}" value="" disabled/></td>
    <td class="middle"><input type="text" class="form-control input-sm" id="remark-{{no}}" maxlength="100" value="" /></td>
    <?php if ($this->isAdmin) : ?>
      <td class="middle"><input type="text" class="form-control input-sm" id="freeTxt-{{no}}" maxlength="100" value="" /></td>
    <?php endif; ?>
  </tr>
</script>

<script id="step-template" type="text/x-handlebarsTemplate">
  <option value="0" data-stepqty="0" data-limit="0" data-freeqty="0" data-force="1">No Step</option>
</script>

<script id="item-template" type="text/x-handlebarsTemplate">
  <option value="0">Select Item</option>
</script>