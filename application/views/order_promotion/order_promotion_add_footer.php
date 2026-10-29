<div class="row">
  <!--- left column -->
  <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
    <div class="row">
      <div class="col-lg-4 col-md-3-harf col-sm-5 col-xs-6 margin-bottom-10">
        <label>
          <input type="radio" name="billoption" class="ace input-lg" value="Y" checked>
          <span class="lbl bigger-100">&nbsp;&nbsp;บิลลงวันที่</span>
        </label>
      </div>
      <div class="col-lg-4 col-md-3-harf col-sm-5 col-xs-6 margin-bottom-10">
        <label>
          <input type="radio" name="billoption" class="ace input-lg" value="N">
          <span class="lbl bigger-100">&nbsp;&nbsp;บิลไม่ลงวันที่</span>
        </label>
      </div>
      <div class="col-lg-4 col-md-5 col-sm-6 col-xs-6 margin-bottom-10">
        <label>
          <input type="checkbox" id="require-sq" class="ace input-lg" value="Y">
          <span class="lbl bigger-100">&nbsp;&nbsp;ต้องการใบเสนอราคา</span>
        </label>
      </div>
    </div>
    <div class="divider-hidden"> </div>
    <div class="divider-hidden"> </div>
    <div class="divider-hidden"> </div>
    <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
        <label class="bigger-100">Remark สำหรับสื่อสารกับ Admin</label>
        <textarea id="remark" class="autosize autosize-transition form-control" maxlength="254" onkeyup="wordCount($(this), 1)"></textarea>
        <span class="pull-right grey"><span id="word-count-1">0</span>/254</span>
      </div>
    </div>
  </div>

  <!--- right column -->
  <div class="divider visible-xs"> </div>
  <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
    <div class="form-horizontal">
      <div class="form-group">
        <label class="col-lg-6 col-md-6 col-sm-6 col-xs-6 control-label no-padding-right">รวมจำนวนเงิน</label>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
          <input type="text" class="form-control input-sm text-right" id="totalAmount" value="0.00" disabled>
        </div>
      </div>

      <div class="form-group">
        <label class="col-lg-6 col-md-6 col-sm-6 col-xs-6 control-label no-padding-right">ภาษีมูลค่าเพิ่ม</label>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
          <input type="text" id="totalVat" class="form-control input-sm text-right" value="0.00" disabled />
        </div>
      </div>

      <div class="form-group">
        <label class="col-lg-6 col-md-6 col-sm-6 col-xs-6 control-label no-padding-right">จำนวนเงินรวมทั้งสิ้น</label>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
          <input type="text" id="docTotal" class="form-control input-sm text-right" value="0.00" disabled/>
        </div>
      </div>

    </div>
  </div>

  <div class="divider-hidden"></div>
  <div class="divider-hidden"></div>
  <div class="divider-hidden"></div>

  <div class="col-sm-12 col-xs-12 padding-5">
    <p class="pull-right">
      <button type="button" class="btn btn-lg btn-success" onclick="previewOrder()">ตรวจสอบข้อมูล</button>
    </p>
  </div>
</div>
