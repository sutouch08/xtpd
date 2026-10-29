<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-4 col-sm-4 col-xs-12 padding-5 hidden-xs">
  	<h4 class="title margin-top-20"><?php echo $this->title; ?></h4>
  </div>
	<div class="col-lg-6 col-md-8 col-sm-8 col-xs-12 padding-5">
		<p class="pull-right top-p">
			<?php if($this->pm->can_add OR $this->pm->can_edit OR $this->_SuperAdmin) : ?>
			<button type="button" class="btn btn-sm btn-primary margin-top-5" onclick="syncData('SO')">Sync SO</button>
			<button type="button" class="btn btn-sm btn-primary margin-top-5" onclick="syncData('DO')">Sync DO</button>
			<button type="button" class="btn btn-sm btn-primary margin-top-5" onclick="syncData('INV')">Sync INV</button>
			<?php endif; ?>
			<?php if($this->pm->can_delete OR $this->_SuperAdmin) : ?>
				<button type="button" class="btn btn-sm btn-warning margin-top-5" onclick="clearLogs()">Clear Logs</button>
				<button type="button" class="btn btn-sm btn-danger margin-top-5" onclick="clearAllLogs()">Clear All Logs</button>
			<?php endif; ?>
		</p>
	</div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-2 col-md-2 col-sm-1-harf col-xs-6 padding-5">
    <label>Order No.</label>
    <input type="text" class="form-control input-sm search" name="code"  value="<?php echo $code; ?>" />
  </div>

	<div class="col-lg-2 col-md-2 col-sm-1-harf col-xs-6 padding-5">
    <label>Doc No.</label>
    <input type="text" class="form-control input-sm search" name="docNum"  value="<?php echo $docNum; ?>" />
  </div>

	<div class="col-lg-2 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <label>Doc Type</label>
    <select class="width-100" name="docType" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="SO" <?php echo is_selected('SO', $docType); ?>>SO</option>
			<option value="DO" <?php echo is_selected('DO', $docType); ?>>DO</option>
			<option value="INV" <?php echo is_selected('INV', $docType); ?>>INV</option>
			<option value="Logs" <?php echo is_selected('Logs', $docType); ?>>Logs</option>
		</select>
  </div>



  <div class="col-lg-2 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <label>สถานะ</label>
    <select class="width-100" name="status" onchange="getSearch()">
      <option value="all">ทั้งหมด</option>
      <option value="1" <?php echo is_selected('1', $status); ?>>Success</option>
      <option value="0" <?php echo is_selected('0', $status); ?>>Not found</option>
      <option value="3" <?php echo is_selected('3', $status); ?>>Error</option>
    </select>
  </div>

	<div class="col-lg-2 col-md-2 col-sm-3 col-xs-6 padding-5">
    <label>Sync Date</label>
    <div class="input-daterange input-group">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="from_date" value="<?php echo $from_date; ?>">
      <input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="to_date" value="<?php echo $to_date; ?>">
    </div>
  </div>

  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>
</div>
<hr class="padding-5">
</form>

<?php echo $this->pagination->create_links(); ?>

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped border-1">
      <thead>
        <tr>
          <th class="width-5 text-center">ลำดับ</th>
						<th class="width-15">Sync Date</th>
					<th class="width-15">Order Code</th>
          <th class="width-10 text-center">Doc Type </th>
					<th class="width-15">Doc Num.</th>
          <th class="width-10 text-center">Status</th>
					<th class="">Message</th>
        </tr>
      </thead>
      <tbody>
<?php if(!empty($data))  : ?>
<?php $no = $this->uri->segment(5) + 1; ?>
<?php   foreach($data as $rs)  : ?>

        <tr class="font-size-12">
          <td class="middle text-center"><?php echo $no; ?></td>
					<td class="middle"><?php echo thai_date($rs->date_upd, TRUE); ?></td>
          <td class="middle"><?php echo $rs->code; ?></td>
					<td class="middle text-center"><?php echo $rs->sync_code; ?></td>
          <td class="middle"><?php echo $rs->get_code; ?></td>
					<td class="middle text-center" id="status-label-<?php echo $rs->id; ?>">
            <?php if($rs->status == 0) : ?>
              <span class="blue">Not Found</span>
            <?php elseif($rs->status == 3) : ?>
              <span class="red">Error</span>
						<?php elseif($rs->status == 1) : ?>
							<span class="green">Success</span>
            <?php endif; ?>
          </td>
          <td class="middle"><?php echo $rs->message;?> </td>
        </tr>
<?php  $no++; ?>
<?php endforeach; ?>
<?php else : ?>
      <tr>
        <td colspan="7" class="text-center"><h4>--- ไม่พบรายการ ---</h4></td>
      </tr>
<?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
	var HOME = BASE_URL + 'sync_data/';

	function goBack() {
		window.location.href = HOME;
	}

	function syncData(docType) {
		load_in();

		$.ajax({
			url: HOME + 'syncData',
			type:'POST',
			cache:false,
			data:{
				'docType' : docType
			},
			success:function(rs) {
				load_out();
				var rs = $.trim(rs);
				if(rs === 'success') {
					swal({
						title:"Success",
						type:'success',
						timer:1000
					});

					setTimeout(function() {
						window.location.href = HOME;
					}, 1200);
				}
				else {
					swal({
						title:'Error!',
						text:rs,
						type:'error'
					})
				}
			},
			error:function(xhr) {
				load_out();
				swal({
					title:'Error!!',
					text:'Error : '+xhr.responseText(),
					type:'error',
					html:true
				});
			}
		})
	}



	function clearLogs() {
		load_in();
		$.ajax({
			url:HOME + 'clear_logs',
			type:'GET',
			cache:false,
			success:function(rs) {
				load_out();
				if(rs == 'success') {
					swal({
						title:'Success',
						type:'success',
						timer:1000
					});

					setTimeout(function() {
						goBack();
					}, 1200);
				}
				else {
					swal({
						title:'Error!',
						text:rs,
						type:'error'
					});
				}
			},
			error:function(xhr) {
				load_out();
				swal({
					title:'Error!',
					text:'Error : '+ xhr.responseText,
					type:'error',
					html:true
				});
			}
		})
	}



	function clearAllLogs() {
		swal({
	    title:'Are sure ?',
	    text:'ต้องการลบ Sync logs ทั้งหมดหรือไม่ ?',
	    type:'warning',
	    showCancelButton: true,
			confirmButtonColor: '#FA5858',
			confirmButtonText: 'ใช่, ฉันต้องการลบ',
			cancelButtonText: 'ยกเลิก',
			closeOnConfirm: true
	  }, function() {
				load_in();
				$.ajax({
					url:HOME + 'clear_all_logs',
					type:'GET',
					cache:false,
					success:function(rs) {
						load_out();
						if(rs == 'success') {
							swal({
								title:'Success',
								type:'success',
								timer:1000
							});

							setTimeout(function() {
								goBack();
							}, 1200);
						}
						else {
							swal({
								title:'Error!',
								text:rs,
								type:'error'
							});
						}
					},
					error:function(xhr) {
						load_out();
						swal({
							title:'Error!',
							text:'Error : '+ xhr.responseText,
							type:'error',
							html:true
						});
					}
				});
		})
	}


	$("#from_date").datepicker({
		dateFormat:'dd-mm-yy',
		onClose:function(sd){
			$("#to_date").datepicker('option', 'minDate', sd);
		}
	});


	$("#to_date").datepicker({
		dateFormat:'dd-mm-yy',
		onClose:function(sd){
			$("#from_date").datepicker('option', 'maxDate', sd);
		}
	});

</script>

<?php $this->load->view('include/footer'); ?>
