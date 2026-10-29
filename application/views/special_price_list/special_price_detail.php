<style>
  .tableFixHead {
    margin-top:-1px;
    margin-left:-1px;    
  }
  .tableFixHead thead th {
    outline: 0;
  }

  .fix-header {
    outline:0;
  }
</style>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <button type="button" class="btn btn-xs btn-info btn-50" onclick="addRow()">Add Row</button>
    <button type="button" class="btn btn-xs btn-danger btn-50" onclick="removeRow()">Delete Row</button>
  </div>

  <div class="divider-hidden"></div>

  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-0 table-responsive border-1" style="height:300px; overflow-y:scroll;">
    <table class="table table-striped tableFixHead tableNarrow border-1" style="min-width:830px;">
      <thead>
        <tr>
          <th class="fix-width-40 text-center fix-header">
            <label>
              <input type="checkbox" class="ace" id="check-all" onchange="checkAll()">
              <span class="lbl"></span>
            </label>
          </th>
					<th class="fix-width-40 text-center fix-header">#</th>
          <th class="min-width-150 fix-header">Description</th>
					<th class="fix-width-80 text-right fix-header">Min. Qty</th>
          <th class="fix-width-80 text-right fix-header">Sell Price</th>
					<th class="fix-width-80 text-right fix-header">Free Qty</th>
          <th class="fix-width-80 text-right fix-header">Position</th>
          <th class="fix-width-150 fix-header">Update by</th>
          <th class="fix-width-130 fix-header">Update at</th>
        </tr>
      </thead>
      <tbody id="detail-table">
				<?php $no = 1; ?>
				<?php if(!empty($details)) : ?>
					<?php foreach($details as $rs) : ?>
        <tr id="row-<?php echo $no; ?>">
					<td class="middle text-center">
						<input type="checkbox"
              class="ace chk"
              id="chk-<?php echo $no; ?>"
              data-id="<?php echo $rs->id; ?>"
              data-no="<?php echo $no; ?>"
              value="<?php echo $no; ?>"/>
            <span class="lbl"></span>
					</td>
					<td class="middle text-center no"><?php echo $no; ?></td>
          <td class="middle">
						<input type="text"
              class="form-control input-sm step-name e"
              id="step-name-<?php echo $no; ?>"
              data-no="<?php echo $no; ?>"
              data-id="<?php echo $rs->id; ?>"
              data-value="<?php echo $rs->name; ?>"
              value="<?php echo $rs->name; ?>"
              placeholder="Description" />
					</td>
					<td class="middle">
						<input type="number"
              class="form-control input-sm text-right step-qty e"
              id="step-qty-<?php echo $no; ?>"
              data-no="<?php echo $no; ?>"
              data-id="<?php echo $rs->id; ?>"
              data-value="<?php echo $rs->Qty; ?>"
              value="<?php echo $rs->Qty; ?>"
              placeholder="Min. Qty" />
					</td>
          <td class="middle">
            <input type="number"
              class="form-control input-sm text-right sell-price e"
              id="sell-price-<?php echo $no; ?>"
              data-no="<?php echo $no; ?>"
              data-id="<?php echo $rs->id; ?>"
              data-value="<?php echo $rs->SellPrice; ?>"
              value="<?php echo $rs->SellPrice; ?>"
              placeholder="Sell Price" />
					</td>
					<td class="middle">
            <input type="number"
              class="form-control input-sm text-right free-qty e"
              id="free-qty-<?php echo $no; ?>"
              data-no="<?php echo $no; ?>"
              data-id="<?php echo $rs->id; ?>"
              data-value="<?php echo $rs->freeQty; ?>"
              value="<?php echo $rs->freeQty; ?>"
              placeholder="Free qty" />
					</td>
          <td class="middle">
            <input type="number"
              class="form-control input-sm text-center position e"
              id="pos-<?php echo $no; ?>"
              data-no="<?php echo $no; ?>"
              data-id="<?php echo $rs->id; ?>"
              data-value="<?php echo $rs->position; ?>"
              value="<?php echo $rs->position; ?>"
               />
					</td>
          <td class="middle">
            <?php echo $this->user_model->get_uname($rs->update_by); ?>
          </td>
          <td class="middle">
            <?php echo thai_date($rs->date_upd, TRUE); ?>
          </td>
        </tr>
				<?php $no++; ?>
			<?php endforeach; ?>
		<?php endif; ?>
      </tbody>
    </table>
  </div>

	<input type="hidden" id="top-row" value="<?php echo $no; ?>" />
</div>
<div class="hide" id="deleted-table">
  <!-- รายการที่ถูกลบจะถูกเพิ่มเข้ามาไว้ในนี้ -->
  <!-- <input type="hidden" class="del-id" value="{{row - id}}"> -->
</div>

<script id="delete-template" type="text/x-handlebarsTemplate">
  <input type="hidden" class="delete-row" value="{{row_id}}" />
</script>

<script id="row-template" type="text/x-handlebarsTemplate">
  <tr id="row-{{no}}">
    <td class="middle text-center">
      <input type="checkbox"
        class="ace chk"
        id="chk-{{no}}"
        data-id="0"
        data-no="{{no}}"
        value="{{no}}" />
      <span class="lbl"></span>
    </td>
    <td class="middle text-center no">{{no}}</td>
    <td class="middle">
      <input type="text"
        class="form-control input-sm step-name e"
        id="step-name-{{no}}"
        data-no="{{no}}"
        data-id="0"
        data-value=""
        value=""
        placeholder="Description" />
    </td>
    <td class="middle">
      <input type="number"
        class="form-control input-sm text-right step-qty e"
        id="step-qty-{{no}}"
        data-no="{{no}}"
        data-id="0"
        data-value=""
        value=""
        placeholder="Min. Qty" />
    </td>
    <td class="middle">
      <input type="number"
        class="form-control input-sm text-right sell-price e"
        id="sell-price-{{no}}"
        data-no="{{no}}"
        data-id="0"
        data-value=">"
        value=""
        placeholder="Sell Price" />
    </td>
    <td class="middle">
      <input type="number"
        class="form-control input-sm text-right free-qty e"
        id="free-qty-{{no}}"
        data-no="{{no}}"
        data-id="0"
        data-value=">"
        value=""
        placeholder="Free qty" />
    </td>

    <td class="middle text-center">
      <input type="number"
        class="form-control input-sm text-center position e"
        id="pos-{{no}}"
        data-no="{{no}}"
        data-id="0"
        data-value=">"
        value="" />
    </td>
    <td class="middle text-center"></td>
    <td class="middle text-center"></td>
  </tr>
</script>
