<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <button type="button" class="btn btn-xs btn-info btn-100" onclick="addRow()">Add Row</button>
    <button type="button" class="btn btn-xs btn-danger btn-100" onclick="removeRow()">Delete Row</button>
  </div>

  <div class="divider-hidden"></div>

  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-0 border-1 table-responsive" style="margin-left:5px; height:350px; overflow:auto;">
    <table class="table table-striped tableNarrow" style="min-width:660px;">
      <thead>
        <tr>
          <th class="fix-width-40 text-center">
            <label>
              <input type="checkbox" class="ace" id="check-all" onchange="checkAll()">
              <span class="lbl"></span>
            </label>
          </th>
          <th class="fix-width-40 text-center">#</th>
          <th class="min-width-150">Label Text</th>
          <th class="fix-width-80">Step Qty</th>
          <th class="fix-width-80">Limit Qty</th>
          <th class="fix-width-80">Free Qty</th>
          <th class="fix-width-50 text-center">Active</th>
          <th class="fix-width-50 text-center">Force</th>
          <th class="fix-width-50 text-center">Highlight</th>
          <th class="fix-width-80 text-center">Position</th>          
        </tr>
      </thead>
      <tbody id="detail-table">
        <?php $no = 1; ?>
        <?php if (!empty($details)) : ?>
          <?php foreach ($details as $rs) : ?>
            <tr id="row-<?php echo $no; ?>">
              <td class="middle text-center">
                <input type="checkbox"
                  class="ace chk"
                  id="chk-<?php echo $no; ?>"
                  data-id="<?php echo $rs->id; ?>"
                  data-no="<?php echo $no; ?>"
                  value="<?php echo $no; ?>" />
                <span class="lbl"></span>
              </td>
              <td class="middle text-center no"><?php echo $no; ?></td>
              <td class="middle">
                <input type="text"
                  class="form-control input-sm e"
                  id="label-<?php echo $no; ?>"
                  data-no="<?php echo $no; ?>"
                  data-id="<?php echo $rs->id; ?>"
                  data-value="<?php echo $rs->labelText; ?>"
                  value="<?php echo $rs->labelText; ?>"
                  placeholder="Label text" />
              </td>
              <td class="middle">
                <input type="number"
                  class="form-control input-sm text-right step-qty e"
                  id="step-qty-<?php echo $no; ?>"
                  data-no="<?php echo $no; ?>"
                  data-id="<?php echo $rs->id; ?>"
                  data-value="<?php echo $rs->stepQty; ?>"
                  value="<?php echo $rs->stepQty; ?>"
                  placeholder="Step qty" />
              </td>
              <td class="middle">
                <input type="number"
                  class="form-control input-sm text-right limit-qty e"
                  id="limit-qty-<?php echo $no; ?>"
                  data-no="<?php echo $no; ?>"
                  data-id="<?php echo $rs->id; ?>"
                  data-value="<?php echo $rs->limitQty; ?>"
                  value="<?php echo $rs->limitQty; ?>"
                  placeholder="Limit qty" />
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
              <td class="middle text-center">
                <label>
                  <input type="checkbox"
                    class="ace"
                    id="active-<?php echo $no; ?>"
                    data-no="<?php echo $no; ?>"
                    data-id="<?php echo $rs->id; ?>"
                    data-value="<?php echo $rs->active; ?>"
                    value="<?php echo $rs->active; ?>" <?php echo is_checked('1', $rs->active); ?> />
                  <span class="lbl"></span>
                </label>
              </td>
              <td class="middle text-center">
                <label>
                  <input type="checkbox"
                    class="ace"
                    id="force-<?php echo $no; ?>"
                    data-no="<?php echo $no; ?>"
                    data-id="<?php echo $rs->id; ?>"
                    data-value="<?php echo $rs->is_force; ?>"
                    value="<?php echo $rs->is_force; ?>" <?php echo is_checked('1', $rs->is_force); ?> />
                  <span class="lbl"></span>
                </label>
              </td>
              <td class="middle text-center">
                <label>
                  <input type="checkbox"
                    class="ace"
                    id="highlight-<?php echo $no; ?>"
                    data-no="<?php echo $no; ?>"
                    data-id="<?php echo $rs->id; ?>"
                    data-value="<?php echo $rs->highlight; ?>"
                    value="<?php echo $rs->highlight; ?>" <?php echo is_checked('1', $rs->highlight); ?> />
                  <span class="lbl"></span>
                </label>
              </td>
              <td class="middle">
                <input type="number"
                  class="form-control input-sm text-center position e"
                  id="pos-<?php echo $no; ?>"
                  data-no="<?php echo $no; ?>"
                  data-id="<?php echo $rs->id; ?>"
                  data-value="<?php echo $rs->position; ?>"
                  value="<?php echo $rs->position; ?>" />
              </td>              
            </tr>
            <?php $no++; ?>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <div class="divider-hidden"></div>
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <button type="button" class="btn btn-white btn-success btn-100" onclick="update_details()">&nbsp; Save</button>
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
        class="form-control input-sm e"
        id="label-{{no}}"
        data-no="{{no}}"
        data-id="0"
        data-value=""
        value=""
        placeholder="Label text"/>
    </td>
    <td class="middle">
      <input type="number"
        class="form-control input-sm text-right step-qty e"
        id="step-qty-{{no}}"
        data-no="{{no}}"
        data-id="0"
        data-value=""
        value=""
        placeholder="Step qty" />
    </td>
    <td class="middle">
      <input type="number"
        class="form-control input-sm text-right limit-qty e"
        id="limit-qty-{{no}}"
        data-no="{{no}}"
        data-id="0"
        data-value=""
        value=""
        placeholder="Limit qty" />
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
      <label>
        <input type="checkbox"
          class="ace"
          id="active-{{no}}"
          data-no="{{no}}"
          data-id="0"
          data-value="0"
          value="1" checked/>
        <span class="lbl"></span>
      </label>
    </td>
    <td class="middle text-center">
      <label>
        <input type="checkbox"
          class="ace"
          id="force-{{no}}"
          data-no="{{no}}"
          data-id="0"
          data-value="0"
          value="1" checked/>
        <span class="lbl"></span>
      </label>
    </td>
    <td class="middle text-center">
      <label>
        <input type="checkbox"
          class="ace"
          id="highlight-{{no}}"
          data-no="{{no}}"
          data-id="0"
          data-value="0"
          value="1" />
        <span class="lbl"></span>
      </label>
    </td>
    <td class="middle">
      <input type="number"
        class="form-control input-sm text-center position e"
        id="pos-{{no}}"
        data-no="{{no}}"
        data-id="0"
        data-value="0"
        value="" />
    </td>    
  </tr>
</script>