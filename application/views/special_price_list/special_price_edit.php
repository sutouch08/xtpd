<?php $this->load->view('include/header'); ?>
<style>
	.tableFixHead thead th {
		outline:0;
	}

	.fix-header {
		outline: 0;
	}
</style>
<?php
$tab = isset($tab) ? $tab : 'info';
$info = $tab === 'info' ? 'active in' : '';
$items = $tab === 'items' ? 'active in' : '';
?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<h4 class="title"><?php echo $this->title; ?></h4>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-white btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
			<button type="button" class="btn btn-white btn-info" onclick="refresh()"><i class="fa fa-refresh"></i> &nbsp;Reload</button>
			<button type="button" class="btn btn-white btn-purple" onclick="getImportTemplate()"><i class="fa fa-download"></i> Template</button>
		</p>
	</div>
</div><!-- End Row -->

<hr class="padding-5" />

<div class="row">
	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12 padding-5 padding-right-0" style="padding-top:5px;">
		<ul id="myTab1" class="setting-tabs" style="margin-left:0px;">
			<li class="li-block <?php echo $info; ?>" onclick="changeURL(<?php echo $doc->id; ?>, 'info')"><a href="#info" data-toggle="tab">Price List</a></li>
			<li class="li-block <?php echo $items; ?>" onclick="changeURL(<?php echo $doc->id; ?>, 'items')"><a href="#items" data-toggle="tab">Items</a></li>			
		</ul>
	</div>
	<div class="col-lg-10 col-md-10 col-sm-10 col-xs-12 padding-5" style="padding-top:5px; border-left:solid 1px #ccc; min-height:600px; max-height:1500px;">
		<div class="tab-content" style="border:0px;">
			<div class="tab-pane fade <?php echo $info; ?>" id="info">
				<div class="form-horizontal margin-top-30">
					<div class="form-group">
						<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Price List Name</label>
						<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
							<input type="text" class="form-control input-sm e" id="name" maxlength="100" value="<?php echo $doc->name; ?>" />
						</div>
					</div>

					<div class="form-group">
						<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Type</label>
						<div class="col-lg-2-harf col-md-2-harf col-sm-3 col-xs-12">
							<select class="form-control input-sm e" id="type">
								<option value="">Select type</option>
								<?php echo select_price_list_type($doc->type_id); ?>
							</select>
						</div>
					</div>

					<div class="form-group">
						<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Start Date</label>
						<div class="col-lg-1 col-md-2 col-sm-2 col-xs-12">
							<input type="datetime-local" class="form-control input-sm min-width-150 e" id="start-date"
								value="<?php echo empty($doc->start_date) ? "" : date('Y-m-d\TH:i', strtotime($doc->start_date)); ?>"
								max="<?php echo empty($doc->end_date) ? "" : date('Y-m-d\TH:i', strtotime($doc->end_date)); ?>" />
						</div>
					</div>

					<div class="form-group">
						<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">End Date</label>
						<div class="col-lg-1 col-md-2 col-sm-2 col-xs-12">
							<input type="datetime-local" class="form-control input-sm min-width-150 e" id="end-date"
								value="<?php echo empty($doc->end_date) ? "" : date('Y-m-d\TH:i', strtotime($doc->end_date)); ?>"
								min="<?php echo empty($doc->start_date) ? "" : date('Y-m-d\TH:i', strtotime($doc->start_date)); ?>" />
						</div>
					</div>

					<div class="form-group">
						<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Status</label>
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-top-7">
							<label class="fix-width-100">
								<input type="radio" class="ace e" name="status" value="1" <?php echo is_checked($doc->active, 1); ?> />
								<span class="lbl"> Active</span>
							</label>
							<label class="fix-width-100">
								<input type="radio" class="ace e" name="status" value="0" <?php echo is_checked($doc->active, 0); ?> />
								<span class="lbl"> Inactive</span>
						</div>
					</div>

					<div class="form-group">
						<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label">Apply to</label>
						<div class="col-lg-9 col-md-9 col-sm-9 col-xs-12 padding-top-7">
							<label class="fix-width-200">
								<input type="radio" class="ace e" name="all_customer" value="1" <?php echo is_checked($doc->all_customer, 1); ?> onchange="toggleCustomerSelection(1)" />
								<span class="lbl"> All customer groups</span>
							</label>
							<label class="fix-width-200">
								<input type="radio" class="ace e" name="all_customer" value="0" <?php echo is_checked($doc->all_customer, 0); ?> onchange="toggleCustomerSelection(0)" />
								<span class="lbl"> Specific customer groups</span>
							</label>

							<div class="row <?php echo $doc->all_customer == 0 ? '' : 'hidden'; ?>" id="customer-selection" style="margin-left:0px; margin-top:10px;">
								<div class="col-lg-8 col-md-12 col-sm-12 col-xs-12 padding-0 table-responsive border-1" style="height:250px; overflow-x:hidden; overflow-y:auto;">
									<table class="table table-striped tableNarrow tableFixHead">
										<thead>
											<tr>
												<th class="fix-width-40 middle text-center fix-header">
													<label>
														<input type="checkbox" class="ace" onchange="checkAllCustomerGroups($(this))" />
														<span class="lbl"></span>
													</label>
												</th>												
												<th class="fix-width-100 middle fix-header">Code</th>
												<th class="min-width-200 middle fix-header">Description</th>
											</tr>
										</thead>
										<tbody>
											<?php if (!empty($customerGroupList)) : ?>											
												<?php foreach ($customerGroupList as $rs) : ?>
													<tr>
														<td class="middle text-center">
															<label>
																<input type="checkbox" class="ace cus-chk" value="<?php echo $rs->id; ?>" <?php echo isset($customerGroups[$rs->id]) ? 'checked' : ''; ?> />
																<span class="lbl"></span>
															</label>
														</td>														
														<td class="middle"><?php echo $rs->code; ?></td>
														<td class="middle"><?php echo $rs->name; ?></td>
													</tr>													
												<?php endforeach; ?>
												<?php else : ?>
													<tr>
														<td colspan="3" class="middle text-center">No customer groups found</td>
													</tr>
											<?php endif; ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>

					</div>

					<div class="divider-hidden"></div>
					<div class="divider-hidden"></div>
					<div class="divider-hidden"></div>

					<div class="form-group">
						<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">&nbsp;</div>
						<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
							<button type="button" class="btn btn-sm btn-success btn-100 btn-xs-block" onclick="update()"><i class="fa fa-save"></i> &nbsp;Update</button>
						</div>
					</div>
				</div><!-- End Form Horizontal -->
			</div><!-- tab-pane -->
			<div class="tab-pane fade <?php echo $items; ?>" id="items">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
					<div class="row">
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
							<label>Items</label>
							<select class="width-100 item-row" id="item" onchange="update_uom($(this))">
								<option value="" data-uom="">Select Item</option>
								<?php if (!empty($itemList)) : ?>
									<?php foreach ($itemList as $rs) : ?>
										<option value="<?php echo $rs->code; ?>" data-uom="<?php echo $rs->UoM; ?>"><?php echo $rs->name; ?></option>
									<?php endforeach; ?>
								<?php endif; ?>
							</select>
						</div>
						<div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-6 padding-5">
							<label>Uom</label>
							<input type="text" class="form-control input-sm text-center" id="uom" value="" readonly />
						</div>
						<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
							<label class="display-block not-show">btn</label>
							<button type="button" class="btn btn-xs btn-primary btn-block" onclick="addItem()"><i class="fa fa-plus"></i> เพิ่ม</button>
						</div>
						<div class="col-lg-1 col-md-1 col-sm-1 col-xs-2 padding-5 text-center">
							<label class="display-block not-show">buton</label>
							<span style="height:30px; line-height:30px;">-- OR --</span>
						</div>
						<div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-6 padding-5">
							<label class="display-block not-show">Import</label>
							<button type="button" class="btn btn-sm btn-white btn-primary btn-block" style="height:30px;" onclick="showImportModal()"><i class="fa fa-upload"></i> Import</button>
						</div>
					</div>
				</div>
				<div class="divider"></div>
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
						<table class="table table-striped tableNarrow border-1" style="min-width:950px;">
							<thead>
								<tr>
									<th class="fix-width-90 middle"></th>
									<th class="fix-width-40 middle text-center">#</th>
									<th class="fix-width-70 middle text-center">Status</th>
									<th class="fix-width-200 middle">Item Code</th>
									<th class="min-width-200 middle">Item Name</th>
									<th class="fix-width-80 middle text-center">Step</th>
								</tr>
							</thead>
							<tbody id="item-table">
								<?php if (! empty($details)) : ?>
									<?php $no = 1; ?>
									<?php foreach ($details as $rs) : ?>
										<tr id="item-row-<?php echo $rs->id; ?>">
											<td class="middle">
												<button type="button" class="btn btn-minier btn-info" onclick="viewItem(<?php echo $rs->id; ?>, true)"><i class="fa fa-eye"></i></button>
												<button type="button" class="btn btn-minier btn-warning" onclick="editItem(<?php echo $rs->id; ?>, true)">
													<i class="fa fa-pencil"></i>
												</button>
												<button type="button" class="btn btn-minier btn-danger" onclick="deleteItem(<?php echo $rs->id; ?>, '<?php echo $rs->ItemCode; ?>')">
													<i class="fa fa-trash"></i>
												</button>
											</td>
											<td class="middle text-center no"><?php echo $no; ?></td>
											<td class="middle text-center">
												<label style="height:22px;">
													<input class="ace ace-switch ace-switch-6" type="checkbox" value="1" <?php echo is_checked($rs->active, 1); ?> onchange="toggleActiveItem(<?php echo $rs->id; ?>, this)" />
													<span class="lbl"></span>
												</label>
											</td>
											<td class="middle"><?php echo $rs->ItemCode; ?></td>
											<td class="middle"><?php echo $rs->ItemName; ?></td>
											<td class="middle text-center"><?php echo $this->special_price_list_model->count_step($rs->id); ?></td>
										</tr>
										<?php $no++; ?>
									<?php endforeach; ?>
								<?php endif; ?>
							</tbody>
						</table>
					</div>
				</div>

			</div><!-- tab-pane -->			
		</div><!-- End Tab Content -->
	</div><!-- End Col-10 -->
</div><!-- End Row -->

<input type="hidden" id="id" value="<?php echo $doc->id; ?>" />

<?php $this->load->view('special_price_list/import_items'); ?>

<script id="item-row-template" type="text/x-handlebarsTemplate">
	<tr id="item-row-{{id}}">
		<td class="middle">
			<button type="button" class="btn btn-minier btn-info" onclick="viewItem({{id}}, true)"><i class="fa fa-eye"></i></button>		
				<button type="button" class="btn btn-minier btn-warning" onclick="editItem({{id}}, true)">
					<i class="fa fa-pencil"></i>
				</button>						
				<button type="button" class="btn btn-minier btn-danger" onclick="deleteItem({{id}}, '{{code}}')">
					<i class="fa fa-trash"></i>
				</button>			
		</td>
		<td class="middle text-center no">{{no}}</td>
		<td class="middle text-center">
			<label style="height:22px;">
				<input class="ace ace-switch ace-switch-6" type="checkbox" value="1" checked onchange="toggleActiveItem({{id}}, this)" />
				<span class="lbl"></span>
			</label>
		</td>
		<td class="middle">{{code}}</td>
		<td class="middle">{{name}}</td>
		<td class="middle text-center">0</td>	
	</tr>
</script>

<script>
	window.addEventListener('load', function() {
		bindDateTimeRange('#start-date', '#end-date');
	});

	$('#item').select2();
</script>

<script src="<?php echo base_url(); ?>scripts/special_price_list/special_price_list.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>