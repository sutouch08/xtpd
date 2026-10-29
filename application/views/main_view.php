<?php $this->load->view('include/header'); ?>
<?php $od = get_permission('ORDERS'); ?>
<?php $op = get_permission('ORDERPRO'); ?>

<style>
.icon-box {
	display: inline-block;
	width:30%;
	height: 60px;
	font-size: 22px;
	padding-top:15px;
	padding-bottom: 15px;
	text-align: center;
	margin: 0px;
	color:#fff;
}

.sub-icon {
	display: inline-block;
	width:30%;
	height: 50px;
	font-size: 18px;
	padding-top:15px;
	padding-bottom: 15px;
	text-align: center;
	margin: 0px;
	color:#fff;
}

.info-box {
	display: inline-block;
	width:70%;
	height: 60px;
	font-size:22px;
	padding:15px;
	margin-left:-4px;
	text-align: right;
	color:#FFF;
}

.sub-info {
	display: inline-block;
	width:70%;
	height: 50px;
	font-size:18px;
	padding:15px;
	margin-left:-4px;
	text-align: right;
	color:#FFF;
}

.i-blue {  background-color: #4A89DC; }
.c-blue {  background-color: #5D9CEC; }
.i-green {  background-color:  #8CC152;}
.c-green {  background-color:  #A0D468;}
.i-yellow { background-color: #F6BB42;}
.c-yellow { background-color: #FFCE54;}
.i-orange { background-color: #E9573F;}
.c-orange { background-color: #FC6E51;}
.i-red { background-color: #DA4453;}
.c-red { background-color: #ED5565;}
</style>

<?php if(!empty($this->_user->sale_id) && !$this->isGM) : ?>
<div class="row">
  <div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
    <label>วันที่</label>
		<div class="input-daterange input-group">
			<input type="text" class="form-control input-sm width-50 from-date text-center" id="fromDate" name="fromDate" value="<?php echo date('01-m-Y'); ?>" placeholder="เริ่มต้น" readonly/>
			<input type="text" class="form-control input-sm width-50 to-date text-center" id="toDate" name="toDate" value="<?php echo date('t-m-Y'); ?>" placeholder="สิ้นสุด" readonly />
		</div>
  </div>
  <div class="col-lg-2 col-md-2-harf col-sm-2 col-xs-6 padding-5">
    <label>การอนุมัติ</label>
    <select class="form-control input-sm" name="approval_status" id="approval_status">
			<option value="all">ทั้งหมด</option>
			<option value="P">รออนุมัติ</option>
			<option value="A">อนุมัติ</option>
			<option value="AP">อนุมัติบางส่วน</option>
			<option value="R">ไม่อนุมัติ</option>
		</select>
  </div>

  <div class="col-lg-2 col-md-2-harf col-sm-2 col-xs-6 padding-5">
    <label class="display-block not-show">btn</label>
    <button type="button" class="btn btn-xs btn-primary btn-block" onclick="getSummary()">Summary</button>
  </div>
  <div class="col-lg-2 col-md-2-harf col-sm-2 col-xs-6 padding-5">
    <label class="display-block not-show">btn</label>
    <button type="button" class="btn btn-xs btn-info btn-block" onclick="getDetails()">Details</button>
  </div>
	<?php if($this->_user->bi_link == 1) : ?>
		<?php if(!empty($this->_user->bi_link)) : ?>
	<div class="col-lg-2 col-md-2-harf col-sm-2 col-xs-6 padding-5">
		<label class="display-block not-show">btn</label>
		<button type="button" class="btn btn-xs btn-success btn-block" onclick="goPowerBi()"><i class="fa fa-barchart"></i>&nbsp; Power BI</button>
	</div>
		<?php endif; ?>
	<?php endif; ?>
</div>
<hr class="margin-top-30 margin-bottom-30 padding-5"/>

<div class="row" id="result">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="row">
			<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 padding-5 margin-bottom-15">
				<div class="icon-box i-blue width-40">Ordered</div>
				<div class="info-box c-blue width-60"><?php echo number($sumOrdered, 2); ?></div>
			</div>
			<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 padding-5 margin-bottom-15">
				<div class="icon-box i-yellow width-40">Pending</div>
				<div class="info-box c-yellow width-60"><?php echo number($sumPending, 2); ?></div>
			</div>
			<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 padding-5 margin-bottom-15">
				<div class="icon-box i-green width-40">Approved</div>
				<div class="info-box c-green width-60"><?php echo number($sumApproved, 2); ?></div>
			</div>
			<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 padding-5 margin-bottom-15">
				<div class="icon-box i-red width-40">Rejected</div>
				<div class="info-box c-red width-60"><?php echo number($sumRejected, 2); ?></div>
			</div>
		</div>
  </div>
</div>

<script id="summary-template" type="text/x-handlebarsTemplate">
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
	<div class="row">
		<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 padding-5 margin-bottom-15">
			<div class="icon-box i-blue width-40">Ordered</div>
			<div class="info-box c-blue width-60">{{sumOrdered}}</div>
		</div>
		<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 padding-5 margin-bottom-15">
			<div class="icon-box i-yellow width-40">Pending</div>
			<div class="info-box c-yellow width-60">{{sumPending}}</div>
		</div>
		<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 padding-5 margin-bottom-15">
			<div class="icon-box i-green width-40">Approved</div>
			<div class="info-box c-green width-60">{{sumApproved}}</div>
		</div>
		<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 padding-5 margin-bottom-15">
			<div class="icon-box i-red width-40">Rejected</div>
			<div class="info-box c-red width-60">{{sumRejected}}</div>
		</div>
	</div>
</div>
</script>


<script id="details-template" type="text/x-handlebarsTemplate">

	<div class="col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped table-bordered" style="min-width:1900px;">
			<thead>
				<tr>
					<th class="middle text-center" style="width:20px;">#</th>
					<th class="middle" style="width:100px;">วันที่</th>
					<th class="middle" style="width:110px;">เลขที่ออเดอร์</th>
					<th class="middle" style="width:100px;">Payment Term</th>
					<th class="middle" style="width:110px;">รหัสลูกค้า</th>
					<th class="middle" style="width:250px;">ชื่อลูกค้า</th>
					<th class="middle" style="width:250px;">รายการสินค้า</th>
					<th class="middle text-right" style="width:100px;">ราคา/หน่วย</th>
					<th class="middle text-right" style="width:100px;">ราคาพิเศษ/หน่วย</th>
					<th class="middle text-right" style="width:100px;">ส่วนลด</th>
					<th class="middle text-right" style="width:100px;">จำนวน</th>
					<th class="middle text-right" style="width:100px;">มูลค่า</th>
					<th class="middle" style="width:100px;">ผู้อนุมัติ</th>
					<th class="middle text-center" style="width:80px;">สถานะ</th>
					<th class="middle" style="width:100px;">เลขที่ PO</th>
					<th class="middle" style="width:100px;">เลขที่ SO</th>
					<th class="middle" style="width:100px;">เลขที่ DO</th>
					<th class="middle" style="width:100px;">เลขที่ Invoice</th>
					<th class="middle" style="width:100px;">วันที่ Invoice</th>
				</tr>
			</thead>
			<tbody>
				{{#each this}}
					{{#if nodata}}
						<tr><td colspan="19" class="text-center">--- ไม่พบข้อมูลตามเงื่อนไขที่กำหนด ---</td></tr>
						{{else}}
							{{#if @last}}
								<tr>
									<td colspan="10" class="middle text-right">รวม</td>
									<td class="middle text-right">{{totalQty}}</td>
									<td class="middle text-right">{{totalAmount}}</td>
									<td colspan="7"></td>
								</tr>
							{{else}}
								<tr>
									<td class="middle text-center">{{no}}</td>
									<td class="middle">{{DocDate}}</td>
									<td class="middle">{{code}}</td>
									<td class="middle">{{paymentTerm}}</td>
									<td class="middle">{{CardCode}}</td>
									<td class="middle">{{CardName}}</td>
									<td class="middle">{{ItemName}}</td>
									<td class="middle text-right">{{stdPrice}}</td>
									<td class="middle text-right">{{SellPrice}}</td>
									<td class="middle text-right">{{DiscPrcnt}}</td>
									<td class="middle text-right">{{Qty}}</td>
									<td class="middle text-right">{{Amount}}</td>
									<td class="middle">{{Approver}}</td>
									<td class="middle">{{Approval_status}}</td>
									<td class="middle">{{PoNo}}</td>
									<td class="middle">{{SONo}}</td>
									<td class="middle">{{{DoNo}}}</td>
									<td class="middle">{{{InvNo}}}</td>
									<td class="middle">{{InvoiceDate}}</td>
								</tr>
							{{/if}}
						{{/if}}
				{{/each}}
			</tbody>
		</table>
  </div>

</script>

<script>

	var HOME = BASE_URL + 'main/';

	$('#fromDate').datepicker({
		dateFormat:'dd-mm-yy',
		onClose:function(sd) {
			$('#toDate').datepicker("option", "minDate", sd);
		}
	});


	$('#toDate').datepicker({
		dateFormat:'dd-mm-yy',
		onClose:function(sd) {
			$('#fromDate').datepicker("option", "maxDate", sd);
		}
	});


	function getSummary() {
		let fromDate = $('#fromDate').val();
		let toDate = $('#toDate').val();

		if(!isDate(fromDate) || !isDate(toDate)) {
			swal("วันที่ไม่ถูกต้อง");
			return false;
		}

		load_in();

		$.ajax({
			url:HOME + 'get_summary',
			type:'GET',
			cache:false,
			data:{
				"fromDate" : fromDate,
				"toDate" : toDate
			},
			success:function(rs) {
				load_out();
				if(isJson(rs)) {
					let source = $('#summary-template').html();
					let data = $.parseJSON(rs);
					let output = $('#result');

					render(source, data, output);
				}
				else {
					swal({
						title:'Error!',
						text:rs,
						type:'error'
					})
				}
			}
		})
	}



	function getDetails() {
		let fromDate = $('#fromDate').val();
		let toDate = $('#toDate').val();
		let status = $('#approval_status').val();

		if(!isDate(fromDate) || !isDate(toDate)) {
			swal("วันที่ไม่ถูกต้อง");
			return false;
		}

		load_in();

		$.ajax({
			url:HOME + 'get_details',
			type:'GET',
			cache:false,
			data:{
				"fromDate" : fromDate,
				"toDate" : toDate,
				"status" : status
			},
			success:function(rs) {
				load_out();
				if(isJson(rs)) {
					let source = $('#details-template').html();
					let data = $.parseJSON(rs);
					let output = $('#result');

					render(source, data, output);
				}
				else {
					swal({
						title:'Error!',
						text:rs,
						type:'error'
					})
				}
			}
		})
	}

</script>
<?php else : ?>

	<div class="row">
	  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 text-center">
	    <h1>Hello!</h1>
	    <h5>Good to see you here</h5>
	  </div>
	  <div class="divider-hidden"></div>
		<div class="divider"></div>
	  <div class="col-lg-4 col-lg-offset-4 col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2 col-xs-12 padding-5 text-center">
	    <?php if($this->_user->bi_link == 1) : ?>
				<?php if(!empty($bi_link)) : ?>
	      <button type="button" class="btn btn-white btn-success btn-lg margin-bottom-15" onclick="goPowerBi()"><i class="fa fa-barchart"></i>&nbsp; Power BI</button>
				<?php endif; ?>
	    <?php endif; ?>
	  </div>

	</div>

<?php endif; ?>




<script>
  function newOrder() {
    window.location.href = BASE_URL + 'orders/add_new';
  }


  function newOrderPromotion() {
    window.location.href = BASE_URL + 'order_promotion/add_new';
  }

	function goPowerBi() {
		$.ajax({
			url:BASE_URL + 'bi/get_link',
			type:'GET',
			cache:false,
			success:function(rs) {
				if(rs != "not found") {
					var target = rs;
					var center    = ($(document).width() - 800)/2;
			    var prop = "width=800, height=900, left="+center+", scrollbars=yes";
			    window.open(target, '_blank', prop);
				}
				else {
					swal("Power Bi Link not found");
				}
			}
		});
  }

  // function goPowerBi() {
  //   var target = window.location.href = BASE_URL + 'bi';
  //   var prop = "width=800, height=900, left="+center+", scrollbars=yes";
  //   window.open(target, '_blank', prop);
  // }

</script>

<?php $this->load->view('include/footer'); ?>
