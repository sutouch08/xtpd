<form id="companyForm" method="post" action="<?php echo $this->home; ?>/update_config">
  <div class="row">
    <div class="col-sm-3">
      <span class="form-control left-label">แบรนด์สินค้า</span>
    </div>
    <div class="col-sm-9">
      <input type="text" class="form-control input-sm input-medium" name="COMPANY_NAME" id="brand" value="<?php echo $COMPANY_NAME; ?>" />
    </div>
    <div class="divider-hidden"></div>

    <div class="col-sm-3">
      <span class="form-control left-label">ชื่อบริษัท</span>
    </div>
    <div class="col-sm-9">
      <input type="text" class="form-control input-sm input-xlarge" name="COMPANY_FULL_NAME" id="cName" value="<?php echo $COMPANY_FULL_NAME; ?>" />
    </div>
    <div class="divider-hidden"></div>

    <div class="col-sm-3">
      <span class="form-control left-label">ที่อยู่บรรทัด 1</span>
    </div>
    <div class="col-sm-5">
      <input type="text" class="form-control input-sm" name="COMPANY_ADDRESS1" id="cAddress1" placeholder="เลขที่ หมู่ ถนน ตำบล" value="<?php echo $COMPANY_ADDRESS1; ?>" />
    </div>
    <div class="divider-hidden"></div>

    <div class="col-sm-3">
      <span class="form-control left-label">ที่อยู่บรรทัด 2</span>
    </div>
    <div class="col-sm-5">
      <input type="text" class="form-control input-sm" name="COMPANY_ADDRESS2" id="cAddress2" placeholder="อำเภอ จังหวัด" value="<?php echo $COMPANY_ADDRESS2; ?>" />
    </div>
    <div class="divider-hidden"></div>

    <div class="col-sm-3">
      <span class="form-control left-label">รหัสไปรษณีย์</span>
    </div>
    <div class="col-sm-9">
      <input type="text" class="form-control input-sm input-medium" name="COMPANY_POST_CODE" id="postCode" value="<?php echo $COMPANY_POST_CODE; ?>" />
    </div>
    <div class="divider-hidden"></div>

    <div class="col-sm-3">
      <span class="form-control left-label">รหัสประเทศ</span>
    </div>
    <div class="col-sm-9">
      <input type="text" class="form-control input-sm input-mini" name="COUNTRY" id="country" value="<?php echo $COUNTRY; ?>" />
    </div>
    <div class="divider-hidden"></div>

    <div class="col-sm-3">
      <span class="form-control left-label">โทรศัพท์</span>
    </div>
    <div class="col-sm-9">
      <input type="text" class="form-control input-sm input-large" name="COMPANY_PHONE" id="phone" value="<?php echo $COMPANY_PHONE; ?>" />
    </div>
    <div class="divider-hidden"></div>

    <div class="col-sm-3">
      <span class="form-control left-label">แฟกซ์</span>
    </div>
    <div class="col-sm-9">
      <input type="text" class="form-control input-sm input-large" name="COMPANY_FAX" id="fax" value="<?php echo $COMPANY_FAX; ?>" />
    </div>
    <div class="divider-hidden"></div>

    <div class="col-sm-3">
      <span class="form-control left-label">อีเมล์</span>
    </div>
    <div class="col-sm-9">
      <input type="text" class="form-control input-sm input-large" name="COMPANY_EMAIL" id="email" value="<?php echo $COMPANY_EMAIL; ?>" />
    </div>
    <div class="divider-hidden"></div>

    <div class="col-sm-3">
      <span class="form-control left-label">เลขประจำตัวผู้เสียภาษี</span>
    </div>
    <div class="col-sm-9">
      <input type="text" class="form-control input-sm input-large" name="COMPANY_TAX_ID" id="taxID" value="<?php echo $COMPANY_TAX_ID; ?>" />
    </div>
    <div class="divider-hidden"></div>

    <div class="col-sm-3">
      <span class="form-control left-label">Line</span>
    </div>
    <div class="col-sm-9">
      <input type="text" class="form-control input-sm input-large" name="COMPANY_LINE" id="line" value="<?php echo $COMPANY_LINE; ?>" />
    </div>
    <div class="divider-hidden"></div>

    <div class="col-sm-3">
      <span class="form-control left-label">Facebook</span>
    </div>
    <div class="col-sm-9">
      <input type="text" class="form-control input-sm input-large" name="COMPANY_FACEBOOK" id="facebook" value="<?php echo $COMPANY_FACEBOOK; ?>" />
    </div>
    <div class="divider-hidden"></div>

    <div class="col-sm-3">
      <span class="form-control left-label">Website</span>
    </div>
    <div class="col-sm-9">
      <input type="text" class="form-control input-sm input-large" name="COMPANY_WEBSITE" id="website" value="<?php echo $COMPANY_WEBSITE; ?>" />
    </div>
    <div class="divider-hidden"></div>

    <div class="col-sm-9 col-sm-offset-3">
      <?php if ($this->pm->can_edit or $this->_SuperAdmin) : ?>
        <button type="button" class="btn btn-sm btn-success" onClick="updateConfig('companyForm')">
          <i class="fa fa-save"></i> บันทึก
        </button>
      <?php endif; ?>
    </div>
    <div class="divider-hidden"></div>

  </div><!--/ row -->
</form>