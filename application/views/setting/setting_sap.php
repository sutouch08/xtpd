<form id="sapForm" method="post" action="<?php echo $this->home; ?>/update_config">
	<div class="row">
		<div class="col-lg-3 col-md-4 col-sm-5 col-xs-8 padding-5">
			<span class="form-control left-label">Default Currency</span>
		</div>
		<div class="col-lg-9 col-md-8 col-sm-7 col-xs-4 padding-5">
			<input type="text" class="form-control input-sm input-small" name="CURRENCY" value="<?php echo $CURRENCY; ?>" />
		</div>
		<div class="divider-hidden"></div>

		<div class="col-lg-3 col-md-4 col-sm-5 col-xs-8 padding-5">
			<span class="form-control left-label">รหัสภาษีซื้อ</span>
		</div>
		<div class="col-lg-9 col-md-8 col-sm-7 col-xs-4 padding-5">
			<input type="text" class="form-control input-sm input-small" name="PURCHASE_VAT_CODE" value="<?php echo $PURCHASE_VAT_CODE; ?>" />
		</div>
		<div class="divider-hidden"></div>

		<div class="col-lg-3 col-md-4 col-sm-5 col-xs-8 padding-5">
			<span class="form-control left-label">อัตราภาษีซื้อ</span>
		</div>
		<div class="col-lg-9 col-md-8 col-sm-7 col-xs-4 padding-5">
			<input type="text" class="form-control input-sm input-small" name="PURCHASE_VAT_RATE" value="<?php echo $PURCHASE_VAT_RATE; ?>" />
		</div>
		<div class="divider-hidden"></div>

		<div class="col-lg-3 col-md-4 col-sm-5 col-xs-8 padding-5">
			<span class="form-control left-label">รหัสภาษีขาย</span>
		</div>
		<div class="col-lg-9 col-md-8 col-sm-7 col-xs-4 padding-5">
			<input type="text" class="form-control input-sm input-small" name="SALE_VAT_CODE" value="<?php echo $SALE_VAT_CODE; ?>" />
		</div>
		<div class="divider-hidden"></div>

		<div class="col-lg-3 col-md-4 col-sm-5 col-xs-8 padding-5">
			<span class="form-control left-label">อัตราภาษีขาย</span>
		</div>
		<div class="col-lg-9 col-md-8 col-sm-7 col-xs-4 padding-5">
			<input type="text" class="form-control input-sm input-small" name="SALE_VAT_RATE" value="<?php echo $SALE_VAT_RATE; ?>" />
		</div>
		<div class="divider-hidden"></div>

		<div class="col-lg-3 col-md-4 col-sm-5 col-xs-8 padding-5">
			<span class="form-control left-label">Customer Group List</span>
		</div>
		<div class="col-lg-5 col-md-5 col-sm-5 col-xs-6 padding-5">
			<input type="text" class="form-control input-sm" name="CUSTOMER_GROUP_LIST" value="<?php echo $CUSTOMER_GROUP_LIST; ?>" />
		</div>
		<div class="divider-hidden"></div>

		<div class="col-lg-3 col-md-4 col-sm-5 col-xs-8 padding-5">
			<span class="form-control left-label">Must Approve List</span>
		</div>

		<?php $pl_name = []; ?>
		<div class="col-lg-4 col-md-5 col-sm-5 col-xs-6 padding-5">
			<select class="width-100" id="price-list">
				<option value="">Select Price List</option>
				<?php if (! empty($priceList)) : ?>
					<?php foreach ($priceList as $p) : ?>
						<option value="<?php echo $p->id; ?>" data-name="<?php echo $p->name; ?>"><?php echo $p->id . " | " . $p->name; ?></option>
						<?php $pl_name[$p->id] = $p->name; ?>
					<?php endforeach; ?>
				<?php endif; ?>
			</select>
			<div class="col-xs-12 col-sm-12 padding-left-0 padding-right-0 margin-top-10" id="price-list-table">
				<?php if (! empty($MUST_APPROVE_PRICE_LIST)) : ?>
					<?php $mapl =  explode(',', $MUST_APPROVE_PRICE_LIST); ?>
					<?php if (count($mapl) > 0) : ?>
						<?php foreach ($mapl as $p_id) : ?>
							<?php $p_id = trim($p_id); ?>
							<label class="btn-block pl-tag" id="pl-tag-<?php echo $p_id; ?>" style="padding:5px; border:solid 1px #81a87b;">
								<?php echo $pl_name[$p_id]; ?>
								<a class="pointer bold pull-right red" onclick="removePriceList('<?php echo $p_id; ?>')" style="margin-left:15px;">
									<i class="fa fa-times"></i>
								</a>
								<input type="hidden" class="pl" id="pl-<?php echo $p_id; ?>" data-id="<?php echo $p_id; ?>" value="<?php echo $p_id; ?>" />
							</label>
						<?php endforeach; ?>
					<?php endif; ?>
				<?php endif; ?>
			</div>
			<input type="hidden" id="must-approve-price-list" name="MUST_APPROVE_PRICE_LIST" value="<?php echo $MUST_APPROVE_PRICE_LIST; ?>" />
		</div>
		<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-4 padding-5">
			<button type="button" class="btn btn-xs btn-primary btn-block" onclick="addPriceList()"><i class="fa fa-plus"></i> Add</button>
		</div>
		<div class="divider-hidden"></div>

		<div class="col-lg-3 col-md-4 col-sm-5 col-xs-8 padding-5">
			<span class="form-control left-label">Default Warehouse</span>
		</div>
		<div class="col-lg-5 col-md-5 col-sm-5 col-xs-6 padding-5">
			<select class="width-100" id="dfWhsCode" name="DEFAULT_WAREHOUSE">
				<option value="">Please select</option>
				<?php echo select_warehouse($DEFAULT_WAREHOUSE); ?>
			</select>
		</div>
		<div class="divider-hidden"></div>

		<div class="divider-hidden"></div>



		<div class="col-lg-9 col-lg-offset-3 col-md-8 col-md-offset-4 col-sm-7 col-sm-offset-5 col-xs-6 col-xs-offset-6">
			<?php if ($this->pm->can_edit or $this->_SuperAdmin) : ?>
				<button type="button" class="btn btn-sm btn-success input-small" onClick="updateConfig('sapForm')">
					<i class="fa fa-save"></i> บันทึก
				</button>
			<?php endif; ?>
		</div>
		<div class="divider-hidden"></div>

	</div><!--/ row -->
</form>

<script id="price-list-template" type="text/x-handlebarsTemplate">
	<label class="btn-block pl-tag" id="pl-tag-{{id}}" style="padding:5px; border:solid 1px #81a87b;">
			{{pname}}
			<a class="pointer bold pull-right red" onclick="removePriceList('{{id}}')" style="margin-left:15px;">
				<i class="fa fa-times"></i>
			</a>
			<input type="hidden" class="pl" id="pl-{{id}}" data-id="{{id}}" value="{{id}}" />
		</label>
	</script>

<script>
	$('#dfWhsCode').select2();

	function removePriceList(id) {
		$('#pl-tag-' + id).remove();
		updateMustApprovePriceList();
	}

	function addPriceList() {
		let pid = $('#price-list').val();

		if (pid == "") {
			return false;
		}

		if ($('#pl-' + pid).length > 0) {
			return false;
		}

		let pname = $('#price-list option:selected').data('name');
		let ds = {
			'id': pid,
			'pname': pname
		};

		let source = $('#price-list-template').html();
		let output = $('#price-list-table');

		render_append(source, ds, output);

		updateMustApprovePriceList();
	}


	function updateMustApprovePriceList() {
		let pls = "";
		let i = 1;
		$('.pl').each(function() {
			let p_id = $(this).val();

			if (i > 1) {
				pls = pls + ", " + p_id;
			} else {
				pls = pls + p_id;
			}

			i++;
		});

		$('#must-approve-price-list').val(pls);
	}
</script>