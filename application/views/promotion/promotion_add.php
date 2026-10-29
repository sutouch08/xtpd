<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
    <h4 class="title"><i class="fa fa-bolt"></i>&nbsp; <?php echo $this->title; ?></h4>
  </div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
			<?php if($this->pm->can_add) : ?>
				<button type="button" class="btn btn-sm btn-success" onclick="saveAdd()"><i class="fa fa-save"></i> &nbsp;Save</button>
			<?php endif; ?>
		</p>
	</div>
</div><!-- End Row -->

<hr class="padding-5"/>

<div class="row">
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>Code</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $code; ?>" disabled />
	</div>

	<div class="col-lg-6 col-md-5 col-sm-5 col-xs-6 padding-5">
		<label>Name</label>
			<input type="text" class="form-control input-sm" id="name" name="name" placeholder="Promotion name" autofocus required/>
	</div>
	<div class="divider-hidden visible-xs">

	</div>
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4-harf padding-5">
		<label>Start Date</label>
		<div class="input-group width-100">
			<input type="text" class="form-control input-sm text-center" name="start_date" id="start_date" value="<?php echo date('d-m-Y'); ?>">
			<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
		</div>
	</div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4-harf padding-5">
		<label>End Date</label>
		<div class="input-group width-100">
			<input type="text" class="form-control input-sm text-center" name="end_date" id="end_date" value="<?php echo date('d-m-Y'); ?>">
			<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
		</div>
	</div>


	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-3 middle padding-left-15">
		<label class="display-block not-show">Status</label>
		<label>
			<input type="checkbox" class="ace" name="status" id="status" checked/>
			<span class="lbl">&nbsp; Active</span>
		</label>
	</div>
</div>

<hr class="padding-5 margin-top-15 margin-bottom-15">
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <button type="button" class="btn btn-sm btn-info" onclick="addRow()">Add Row</button>
    <button type="button" class="btn btn-sm btn-danger" onclick="removeRow()">Delete Row</button>
  </div>
  <div class="divider-hidden"></div>

  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive" style="over-flow:auto;">
    <table class="table table-bordered border-1" style="min-width:600px;">
      <thead>
        <tr class="font-size-10">
          <th class="width-5 text-center"></th>
					<th class="width-5 text-center">#</th>
					<th class="width-50">Item</th>
					<th class="width-15 text-right">Min. Qty</th>
					<th class="width-15 text-right">Sell Price</th>
					<th class="width-10 text-center">Uom</th>
        </tr>
      </thead>
      <tbody id="detail-table">
        <tr id="row-1">
					<td class="middle text-center">
						<input type="checkbox" class="ace chk" id="chk-1" value="1"/>
            <span class="lbl"></span>
					</td>
					<td class="middle text-center no">1</td>
          <td class="middle">
						<select class="width-100 item-row" id="item-1" data-no="1" onchange="update_uom($(this))">
							<option value="" data-uom=""></option>
							<?php if(!empty($items)) : ?>
								<?php foreach($items as $rs) : ?>
									<option value="<?php echo $rs->code; ?>" data-uom="<?php echo $rs->UoM; ?>"><?php echo $rs->name; ?></option>
								<?php endforeach; ?>
							<?php endif; ?>
						</select>
          </td>
					<td class="middle">
						<input type="number" class="form-control input-sm text-right input-qty" data-no="1" id="input-qty-1" value="" />
					</td>
					<td class="middle">
						<input type="number" class="form-control input-sm text-right" id="input-price-1" value="" />
					</td>
					<td class="middle">
						<input type="text" class="form-control input-sm text-center" id="uom-1" value="" disabled />
					</td>
        </tr>
      </tbody>
    </table>
  </div>

	<input type="hidden" id="top-row" value="2" />

</div>

<script id="row-template" type="text/x-handlebarsTemplate">
<tr id="row-{{no}}">
	<td class="middle text-center">
		<input type="checkbox" class="ace chk" id="chk-{{no}}" value="{{no}}"/>
		<span class="lbl"></span>
	</td>
	<td class="middle text-center no">{{no}}</td>
	<td class="middle">
		<select class="width-100 item-row" id="item-{{no}}" data-no="{{no}}" onchange="update_uom($(this))">
			<option value="" data-uom=""></option>
			<?php if(!empty($items)) : ?>
				<?php foreach($items as $rs) : ?>
					<option value="<?php echo $rs->code; ?>" data-uom="<?php echo $rs->UoM; ?>"><?php echo $rs->name; ?></option>
				<?php endforeach; ?>
			<?php endif; ?>
		</select>
	</td>
	<td class="middle">
		<input type="number" class="form-control input-sm text-right input-qty" data-no="{{no}}"  id="input-qty-{{no}}" value="" />
	</td>
	<td class="middle">
		<input type="number" class="form-control input-sm text-right" id="input-price-{{no}}" value="" />
	</td>
	<td class="middle">
		<input type="text" class="form-control input-sm text-center" id="uom-{{no}}" value="" disabled />
	</td>
</tr>
</script>

<script src="<?php echo base_url(); ?>scripts/promotion/promotion.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
