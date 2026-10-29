window.addEventListener('load', inputInit);

function inputInit() {
	$('input[type="number"]').on('wheel', function (e) {
		e.preventDefault();
	});
}

//----  get new customer list
function changeCustomerList() {
	let type = $('#cardType').val();

	load_in();

	$.ajax({
		url:HOME + 'get_user_customer_list/'+type,
		type:"GET",
		cache:false,
		success:function(rs) {
			load_out();
			if(isJson(rs)) {
				let ds = JSON.parse(rs);
				let data = {
					'count' : ds.length,
					'data' : ds
				}

				let source = $('#customer-template').html();
				let output = $('#customer');

				render(source, data, output);

				$('#customer').select2();
				$('#VatGroup').val('');
				$('#vatRate').val('');
				$('#shipToCode option').remove();
				$('#ShipTo').val('');
				$('#billToCode option').remove();
				$('#BillTo').val('');
				$('#slpName').val('');

				checkPriceList();
			}
			else {
				swal({
					title:"Error!",
					text:rs,
					type:'error'
				});

				let prevType = type == 'V' ? 'Q' : 'V';
				$('#cardType').val(prevType);
			}
		}
	})
}

$('#customer').change(function() {
	getItemTemplate();
});

function getCustomerData() {
	let source = $('#price-list-source').val();
	getAddress();
	getCreditData();
	changePriceListType();	
}

function getCreditData() {
	let code = $('#customer').val();

	$.ajax({
		url:`${HOME}get_credit_data`,
		type:'GET',
		cache:false,
		data:{
			'CardCode' : code
		},
		success:function(rs) {			
			if(isJson(rs)) {
				let ds = JSON.parse(rs);

				if(ds.status == 'success') {
					let balance = addCommas(ds.data.CreditBalance.toFixed(2));
					$('#credit-balance').val(balance);
					$('#is-regular').val(ds.data.isRegular);
					$('#overdue').val(ds.data.overdue);

					if(ds.data.isRegular == 1) {
						$('#customer-label').html('<span class="label label-success arrowed">ลูกค้าประจำ</span>');
					}
					else {
						$('#customer-label').html('<span class="label label-danger arrowed">ลูกค้าไม่ประจำ</span>');
					}
				}
				else {
					showError(ds.message);
				}
			}
			else {
				showError(rs);
			}
		},
		error:function(rs) {
			showError(rs);
		}
	});
}

function getAddress() {
	//--- update vatcode
	let code = $('#customer').val();
	let vatCode = $('#customer option:selected').data('vat');
	let vatRate = $('#customer option:selected').data('rate');
	let currency = $('#customer option:selected').data('currency');

	if(currency == '##') {
		$('#currency').removeAttr('disabled');
	}
	else
	{
		$('#currency').attr('disabled', 'disabled');
	}

	currency = currency == '##' ? $('#default_currency').val() : currency;

	$('#VatGroup').val(vatCode);
	$('#vatRate').val(vatRate);
	$('#currency').val(currency);
	recalVat();
	get_address_ship_to_code(code);
	//---- create Address bill to
	get_address_bill_to_code(code);

	if($('#slpName').length) {
		get_sale_name_by_customer(code);
	}

	getRate();
}

function get_sale_name_by_customer(code) {
	if(code.length) {
		$.ajax({
			url:HOME + 'get_sale_name_by_customer',
			type:'GET',
			cache:false,
			data: {
				"CardCode" : code
			},
			success:function(rs) {
				$('#slpName').val(rs);
			}
		})
	}
}

function get_address_ship_to_code(code) {
	$.ajax({
		url:HOME + 'get_address_ship_to_code',
		type:'GET',
		cache:false,
		data:{
			'CardCode' : code
		},
		success:function(rs) {
			var rs = $.trim(rs);
			if(isJson(rs)) {
				var data = $.parseJSON(rs);
				var source = $('#ship-to-template').html();
				var output = $('#shipToCode');
				render(source, data, output);

				get_address_ship_to();
			}
			else {
				$('#shipToCode').html('');
			}
		}
	})
}

function get_address_ship_to() {
	var code = $('#customer').val()
	var adr_code = $('#shipToCode').val();
	$.ajax({
		url:HOME + 'get_address_ship_to',
		type:'GET',
		cache:false,
		data:{
			'CardCode' : code,
			'Address' : adr_code
		},
		success:function(rs) {
			var rs = $.trim(rs);
			if(isJson(rs)) {
				var ds = $.parseJSON(rs);

				let address = ds.address === "" ? "" : ds.address + " ";
				let street = ds.street === "" ? "" : ds.street + " ";
				let sub_district = ds.sub_district === "" ? "" : ds.sub_district + " ";
				let district = ds.district === "" ? "" : ds.district + " ";
				let province = ds.province === "" ? "" : ds.province + " ";
				let postcode = ds.postcode === "" ? "" : ds.postcode + " "
				let country = ds.country === 'TH' ? '' : ds.countryName;
				let adr = address + street + sub_district + district + province + postcode + country;

				$('#ShipTo').val(adr);
			}
		}
	})
}

function get_address_bill_to_code(code) {
	$.ajax({
		url:HOME + 'get_address_bill_to_code',
		type:'GET',
		cache:false,
		data:{
			'CardCode' : code
		},
		success:function(rs) {
			var rs = $.trim(rs);
			if(isJson(rs)) {
				var data = $.parseJSON(rs);
				var source = $('#bill-to-template').html();
				var output = $('#billToCode');
				render(source, data, output);

				get_address_bill_to();
			}
			else {
				$('#billToCode').html('');
			}
		}
	})
}

function get_address_bill_to() {
	var code = $('#customer').val();
	var adr_code = $('#billToCode').val();
	$.ajax({
		url:HOME + 'get_address_bill_to',
		type:'GET',
		cache:false,
		data:{
			'CardCode' : code,
			'Address' : adr_code
		},
		success:function(rs) {
			var rs = $.trim(rs);
			if(isJson(rs)) {
				var ds = $.parseJSON(rs);
				let address = ds.address === "" ? "" : ds.address + " ";
				let street = ds.street === "" ? "" : ds.street + " ";
				let sub_district = ds.sub_district === "" ? "" : ds.sub_district + " ";
				let district = ds.district === "" ? "" : ds.district + " ";
				let province = ds.province === "" ? "" : ds.province + " ";
				let postcode = ds.postcode === "" ? "" : ds.postcode + " "
				let country = ds.country === 'TH' ? '' : ds.countryName;
				let adr = address + street + sub_district + district + province + postcode + country;

				$('#BillTo').val(adr);
			}
		}
	})
}

function checkPriceList() {
	let count = 0;

	$('.item-code').each(function() {
		if($(this).val() != "") {
			count++;
		}
	});

	if(count > 0) {
		//--- clear item
		$('#details-template').html('');

		//--- add new value to top row
		$('#top-row').val(0);

		addRow();
	}

	getStepTemplate();
	getItemTemplate();
	getTermDropdown();
}

function getStepTemplate() {
	let priceList = $('#priceList').val();

	$.ajax({
		url:HOME + 'get_step_template',
		type:'POST',
		cache:false,
		data:{
			'PriceList' : priceList
		},
		success:function(rs) {
			if(isJson(rs)) {
				let ds = JSON.parse(rs);

				if(ds.status == 'success') {
					$('#step-template').html(ds.template);

					$('.step').each(function() {
						let no = $(this).data('no');

						updateSelectStep(no);

						updateStepQty(no);
					});
				}
				else {
					swal({
						title:'Error !',
						text:ds.message,
						type:'error',
						html:true
					})
				}
			}
			else {
				swal({
					title:'Error!',
					text:rs,
					type:'error',
					html:true
				})
			}
		},
		error:function(rs) {
			swal({
				title:'Error!',
				text:rs.responseText,
				type:'error',
				html:true
			})
		}
	})
}

function getTermDropdown() {
	let priceList = $('#priceList').val();
	let sp_id = parseDefault(parseInt($('#priceList option:selected').data('spid')), 0)

	$.ajax({
		url:HOME + 'get_payment_term',
		type:'POST',
		cache:false,
		data:{
			'PriceList' : priceList,
			'special_price_id' : sp_id
		},
		success:function(rs) {
			if(isJson(rs)) {
				let ds = JSON.parse(rs);

				if(ds.status == 'success') {
					$('#term').html(ds.options);
					$('#discPrcnt').val(0.00);
				}
				else {
					swal({
						title:'Error !',
						text:ds.message,
						type:'error',
						html:true
					})
				}
			}
			else {
				swal({
					title:'Error!',
					text:rs,
					type:'error',
					html:true
				})
			}
		},
		error:function(rs) {
			swal({
				title:'Error!',
				text:rs.responseText,
				type:'error',
				html:true
			})
		}
	})
}

function getItemTemplate() {
	let custCode = $('#customer').val();
	let isControl = $('#customer option:selected').data('control');
	let type = $('#customer option:selected').data('type');
	let priceList = $('#priceList').val();
	let spId = $('#priceList option:selected').data('spid');

	if(custCode == "") {
		$('#item-1').val("0");
		$('#item-1 option:selected').text('กรุณาเลือกลูกค้า');
		return false;
	}

	if(priceList == "") {
		$('#item-1').val("0");
		$('#item-1 option:selected').text('กรุณาเลือก Price List');
		return false;
	}

	load_in();

	$.ajax({
		url:HOME + 'get_item_template',
		type:"POST",
		cache:false,
		data:{
			'priceList' : priceList,
			'spid' : spId,
			'isControl' : isControl,
			'customer_type' : type
		},
		success:function(rs) {
			load_out();

			if(isJson(rs)) {
				let ds = JSON.parse(rs);
				if(ds.status === 'success') {
					$('#item-template').html(ds.template);

					$('.input-item-code').each(function() {
						let no = $(this).data('no');

						updateSelectItem(no);
					});
				}
				else {
					showError(ds.message);
				}
			}
			else {
				showError(rs);
			}
		},
		error:function(rs) {
			load_out();
			showError(rs);
		}
	})
}

function getItemData(no) {
	let priceList = $('#priceList').val();
	let sp_id = parseDefault(parseInt($('#priceList option:selected').data('spid')), 0);
	let code = $('#item-'+no).val();
	if(code != "" && code != "0" && code !== null && code !== undefined) {
		load_in();

		$.ajax({
			url:HOME + "get_item_data",
			type:"GET",
			cache:false,
			data:{
				'code' : code,
				'priceList' : priceList,
				'special_price_id' : sp_id,
				'no' : no
			},
			success:function(rs) {
				load_out();

				if(isJson(rs)) {
					let ds = JSON.parse(rs);
					let price = parseFloat(ds.price);

					$('#control-'+no).val(ds.isControl);
					$('#uom-'+no).val(ds.uom);
					$('#stdPrice-'+no).val(price);
					$('#item-'+no).data('price', price);
					$('#price-'+no).val('');
					$('#instock-'+no).val(ds.inStock);
					$('#commit-'+no).val(ds.commit);
					$('#available-'+no).val(ds.available);
					$('#itemVatCode-'+no).val(ds.vatCode);
					$('#itemVatRate-'+no).val(ds.vatRate);
					$('#whsCode-'+no).val(ds.whsCode);
					$('#itemCode-'+no).val(code);

					if(ds.is_sale_discount == 'Y') {
						$('#dis-'+no).prop('checked', true);
					}
					else {
						$('#dis-'+no).prop('checked', false);
					}

					if(ds.step != '' && ds.step !== null && ds.step != undefined) {
						$('#step-'+no).html(ds.step);
					}

					if(ds.stepData != '' && ds.stepData !== null && ds.stepData != undefined) {
						$('#step-data-'+no).val(JSON.stringify(ds.stepData));
					}

					$('#qty-'+no).focus();

					if(ds.isControl == 'Y') {
						$('#row-'+no).addClass('control');
					}
					else {
						$('#row-'+no).removeClass('control');
					}

					updateStepQty(no);
					//recalAmount(no);
				}
				else {
					showError(rs);
				}
			},
			error:function(rs) {
				load_out();
				showError(rs);
			}
		})
	}
	else {
		$('#control-'+no).val("");
		$('#uom-'+no).val("");
		$('#stdPrice-'+no).val("");
		$('#item-'+no).data('price', 0);
		$('#price-'+no).val('');
		$('#instock-'+no).val("");
		$('#commit-'+no).val("");
		$('#available-'+no).val("");
		$('#itemVatCode-'+no).val("");
		$('#itemVatRate-'+no).val("");
		$('#whsCode-'+no).val("");
		$('#itemCode-'+no).val("");
		$('#step-'+no).val(0);
		$('#qty-'+no).val("");
		$('#free-'+no).val("");
		$('#amount-'+no).val("");

		recalAmount(no);
	}
}

function updateSelectItem(no) {
	let template = $('#item-template').html();
	let ds = {"no" : no};
	let target = $('#item-'+no);
	render(template, ds, target);

	$('#item-'+no).select2();
}

function updateSelectStep(no) {
	let template = $('#step-template').html();
	let ds = {"no" : no};
	let target = $('#step-'+no);
	render(template, ds, target);
}

function updateStepQty(no) {
	let priceList = $('#priceList').val();
	let stepQty = parseDefault(parseFloat($('#step-'+no+' option:selected').data('stepqty')), 0);
	let freeQty = parseDefault(parseFloat($('#step-'+no+' option:selected').data('freeqty')), 0);
	let price = parseDefault(parseFloat($('#step-'+no+' option:selected').data('price')), 0);
	let force = $('#step-'+no+' option:selected').data('force');

	if(priceList == 'x') {
		$('#stdPrice-'+no).val(price);
	}


	$('#qty-'+no).val(stepQty);
	$('#free-'+no).val(freeQty);

	if(force) {
		$('#qty-'+no).attr('disabled', 'disabled');
		$('#free-'+no).attr('disabled', 'disabled');
	}
	else {
		$('#qty-'+no).removeAttr('disabled');
		$('#free-'+no).removeAttr('disabled');
		$('#qty-'+no).focus();
	}

	recalAmount(no);
}

function addRow() {
	var no = $('#top-row').val();
	no++;
	$('#top-row').val(no);
	let data = {"no" : no};
	let source = $('#row-template').html();
	let output = $('#details-template');
	render_append(source, data, output);

	let sc = $('#item-template').html();
	let opt = $('#item-'+no);
	render(sc, data, opt);
	updateSelectStep(no);
	$('#item-'+no).select2();

	inputInit();
}

function removeRow() {
	$('.chk').each(function(){
		if($(this).is(':checked')) {
			var no = $(this).val();
			$('#row-'+no).remove();
		}
	})

	recalTotal();
}

function remove_vat(amount, vat) {

	if(vat != 0) {
		vat = parseDefault(parseFloat(vat), 0);
		re_vat	= (vat + 100) / 100;
		amount = parseDefault(parseFloat(amount), 0);
		return amount/re_vat;
	}

	return amount;
}

function get_vat_amount(amount, vat) {
	vat = parseDefault(parseFloat(vat), 0);
	amount = parseDefault(parseFloat(amount), 0);
	if(vat > 0) {
		re_vat = (amount * vat) / (100 + vat);
		return re_vat;
	}

	return 0;
}

function recalVat() {
	$('.item-code').each(function(){
		let no = $(this).data('no');
		recalAmount(no);
	});
}

function recalAmount(no) {
	let priceList = $('#priceList').val();
	let stepData = null;

	let vatRate = parseDefault(parseFloat($('#vatRate').val()), 0);

	if($('#VatGroup').val() === "") {
		vatRate = parseDefault(parseFloat($('#itemVatRate-'+no).val()), 0);
	}

	let qty = parseDefault(parseFloat($('#qty-'+no).val()), 0);
	let stdPrice = parseDefault(parseFloat($('#stdPrice-'+no).val()), 0);
	let sellPrice = $('#price-'+no).val();
	let prevPrice = parseDefault(parseFloat($('#item-'+no).data('price')), 0);

	//--- ถ้า special price list ถูกเลือกให้ทำแบบนี้ด้วย
	if(priceList == 'x') {
		//--- เปลี่ยนราคาขายต่่อชิ้น และ จำนวนแถม ตาม step price qty
		let stepVal = $('#step-'+no).val();
		let stepQty = parseDefault(parseFloat($('#step-'+no+' option:selected').data('stepqty')), 0);

		if(stepQty > qty) {
			$('#qty-'+no).hasError();
			$('#err-'+no).val(1);
			showError('จำนวนต้องไม่น้อยกว่า จำนวนขั้นต่ำของ step ที่เลือก');			
		}
		else {
			$('#qty-'+no).clearError();
			$('#err-'+no).val(0);
		}
	}
	else {
		let limitQty = parseDefault(parseFloat($('#step-'+no+' option:selected').data('limit')), 0);
		let minQty = parseDefault(parseFloat($('#step-'+no+' option:selected').data('stepqty')), 0);

		if(qty < minQty) {
			$('#qty-'+no).hasError();
			$('#err-'+no).val(1);
			showError("จำนวนต้องไม่น้อยกว่า step ที่เลือก");
		}
		else {
			$('#qty-'+no).clearError();
			$('#err-'+no).val(0);
		}

		if(limitQty > 0 && qty > limitQty) {
			qty = limitQty;
			$('#qty-'+no).val(qty);
		}
	}

	if(sellPrice != "") {
		let amount = qty * sellPrice;
		let vatamount = get_vat_amount(amount, vatRate);
		$('#amount-'+no).val(amount.toFixed(2));
		$('#vatAmount-'+no).val(vatamount.toFixed(4));
	}
	else {
		let amount = qty * stdPrice;
		let vatamount = get_vat_amount(amount, vatRate);
		$('#amount-'+no).val(amount.toFixed(2));
		$('#vatAmount-'+no).val(vatamount.toFixed(4));
	}

	recalTotal();
}

function recalTotal() {
	var totalBefDi = 0.00; //--- total befor discount exclude vat
	var discPrcnt = parseDefault(parseFloat($('#discPrcnt').val()), 0); //--- discount percentage
	var totalDisc = 0.00; //--- discount amount
	var totalVat = 0.00; //--- total vat
	var totalAmount = 0; //--- total after bill discount
	var docTotal = 0.00;

	$('.input-qty').each(function(){
		let no = $(this).data('no');
		let vatRate = parseDefault(parseFloat($('#itemVatRate-'+no).val()), 0);
		let amount = parseDefault(parseFloat($('#amount-'+no).val()), 0);
		let vatAmount = parseDefault(parseFloat($('#vatAmount-'+no).val()), 0);
		let lineTotal = amount - vatAmount;
		let discAmount = lineTotal * (discPrcnt * 0.01);

		totalBefDi += amount;
		totalDisc += discAmount;
		totalVat += vatAmount * (1 - (discPrcnt * 0.01));
		totalAmount += lineTotal - discAmount;
	});

	totalBefVat = totalAmount;
	docTotal = totalAmount + totalVat;

	$('#totalBefDi').val(addCommas(totalBefDi.toFixed(2)));
	$('#discSum').val(addCommas(totalDisc.toFixed(2)));
	$('#totalAmount').val(addCommas(totalBefVat.toFixed(2)));
	$('#totalVat').val(addCommas(totalVat.toFixed(2)));
	$('#docTotal').val(addCommas(docTotal.toFixed(2)));
}

async function previewOrder() {
	clearErrorByClass('e');

	let err = 0;
	let count = 0;
	let runno = 1;
	let msg = "";
	let price_edit = 0;
	let shipToWarning = 0;
	let creditDiff = 0; 
	let creditIssue = 0; //--- ติดเครดิต limit หรือไม่ 0 = ไม่ติด, 1 = ติด  (ติดเมื่อ  CreditDiff > 0)

	//--- check valid data
	let customerCode = $('#customer').val();
	let customerName = $('#customer option:selected').data('name');
	let saleTeam = $('#customer option:selected').data('saleteam');
	let areaId = $('#customer option:selected').data('area');
	let shipToCode = $('#shipToCode').val();
	let isDefaultShipTo = $('#shipToCode option:selected').data('default');
	let shipToAddress = $('#ShipTo').val();
	let billToCode = $('#billToCode').val();
	let billToAddress = $('#BillTo').val();
	let priceList = $('#priceList').val();
	let term = $('#term').val();
	let termName = $('#term option:selected').text();
	let listName = $('#priceList option:selected').text();
	let docDate = $('#DocDate').val();
	let dueDate = $('#DocDueDate').val();
	let PoNo = $('#PoNo').val();
	let exShipTo = $('#exShipTo').val().trim();
	let currencyRate = $('#currencyRate').val();
	let currency = $('#currency').val();
	let items = [];
	let totalBefDi = 0;
	let totalVat = 0;
	let totalDisc = 0;
	let totalAmount = 0;
	let docTotal = 0;
	let DiscPrcnt = parseDefaultFloat($('#discPrcnt').val(), 0);
	let isRegular = parseDefaultInt($('#is-regular').val(), 0);
	let creditBalance = parseDefaultFloat(removeCommas($('#credit-balance').val()), 0);
	let overdue = parseDefaultInt($('#overdue').val(), 0);

	const uploadFile = document.getElementById('uploadFile');
	const file = uploadFile.files[0];

	if(isDefaultShipTo == 'N' || exShipTo.length) {
		shipToWarning = 1;
	}

	if(customerCode == "") {
		$('#customer').hasError();
		warning("กรุณาเลือกลูกค้า");
		return false;
	}

	if(priceList == "") {
		$('#priceList').hasError();
		warning("กรุณาเลือก Price List");
		return false;
	}

	if(currencyRate == 0) {
		$('#currencyRate').hasError();
		warning("อัตราแลกเปลี่ยนไม่ถูกต้อง");
		return false;
	}

	if(term == "") {
		$('#term').hasError();
		warning("กรุณาเลือก Payment Term");
		return false;
	}

	if(file && PoNo.length == 0) {
		$('#PoNo').hasError();
		warning("กรุณากรอกเลขที่ใบสั่งซื้อ");
		return false;
	}


	$('.item-code').each(function() {
		let no = $(this).data('no');
		let er = 0;

		if($(this).val() != "") {
			count++;
			let qty = parseDefault(parseFloat($('#qty-'+no).val()), 0);
			let minQty = parseDefault(parseFloat($('#step-'+no+' option:selected').data('stepqty')), 0);
			let limitQty = parseDefault(parseFloat($('#step-'+no+' option:selected').data('limit')), 0);
			let freeQty = parseDefault(parseFloat($('#step-'+no+' option:selected').data('freeqty')), 0);
			let free = parseDefault(parseFloat($('#free-'+no).val()), 0);
			let stdPrice = parseDefault(parseFloat($('#stdPrice-'+no).val()), 0);
			let price = parseDefault(parseFloat($('#price-'+no).val()), 0);

			if(qty == 0 || qty < minQty || (limitQty > 0 && qty > limitQty)) {
				$('#qty-'+no).hasError();
				msg = "จำนวนไม่ถูกต้อง";
				err++;
				er++;
			}

			if(freeQty > 0 && free != freeQty) {
				$('#free-'+no).hasError();
				msg = "จำนวนแถมไม่ถูกต้อง";
				err++;
				er++;
			}

			if(stdPrice == 0 && price == 0) {
				$('#stdPrice-'+no).hasError();
				msg = "ราคาไม่ถูกต้อง";
				err++;
				er++;
			}

			if(er == 0) {
				let stdPrice = parseDefault(parseFloat($('#stdPrice-'+no).val()), 0);
				let sellPrice = parseDefault(parseFloat($('#price-'+no).val()), 0);

				if($('#price-'+no).val() != "") {
					if(sellPrice < stdPrice) {
						price_edit++;
					}
				}

				let amount = parseDefault(parseFloat($('#amount-'+no).val()), 0);
				let vatAmount = parseDefault(parseFloat($('#vatAmount-'+no).val()), 0);
				let lineTotal = amount - vatAmount;
				let discAmount = lineTotal * (DiscPrcnt * 0.01);

				let item = {
					"no" : runno,
					"ItemCode" : $(this).val(),
					"itemName" : $('#item-'+no+' option:selected').data('name'),
					"qty" : addCommas($('#qty-'+no).val()),
					"free" : addCommas($('#free-'+no).val()),
					"uom" : $('#uom-'+no).val(),
					"stdPrice" : addCommas(stdPrice.toFixed(2)),
					"sellPrice" : addCommas(sellPrice.toFixed(2)),
					"amount" : addCommas(amount.toFixed(2)),
					"remark" : $('#remark-'+no).val(),
					"dis" : $('#dis-'+no).is(':checked') ? '<i class="fa fa-check blue"></i>' : ''
				}

				items.push(item);

				totalBefDi += amount;
				totalDisc += discAmount;
				totalVat += vatAmount * (1 - (DiscPrcnt * 0.01));
				totalAmount += lineTotal - discAmount;

				runno++;
			}
		}
	});

	totalBefVat = totalAmount;
	docTotal = totalAmount + totalVat;

	creditDiff = (creditBalance - docTotal) * (-1);
	creditDiff = creditDiff > 0 ? (creditDiff > docTotal ? docTotal : creditDiff) : 0;
	creditIssue = creditDiff > 0 ? 1 : 0;

	creditBalanceNumber = addCommas((creditBalance - docTotal).toFixed(2));
	//creditDiffNumber = creditDiff > 0 ? addCommas(creditDiff.toFixed(2)) : 0;
	creditMessage = creditIssue == 1 ? `<span class="red">Credit Limit คงเหลือ ${creditBalanceNumber} บาท</span>` : `<span class="green">Credit Limit คงเหลือ ${creditBalanceNumber} บาท</span>`;

	let subTotal = {
		"totalBefDi" : addCommas(totalBefDi.toFixed(2)),
		"DiscPrcnt" : DiscPrcnt.toFixed(2),
		"DiscSum" : addCommas(totalDisc.toFixed(2)),
		"totalBefVat" : addCommas(totalBefVat.toFixed(2)),
		"totalVat" : addCommas(totalVat.toFixed(2)),
		"docTotal" : addCommas(docTotal.toFixed(2))
	}

	//items.push(subTotal);

	if(count == 0) {
		warning("ไม่พบรายการสินค้า");
		return false;
	}

	if(err > 0) {
		warning(msg);
		return false;
	}

	const flow = await getOrderFlow(1, creditIssue, overdue);

	let data = {
		"orderCode" : $('#code').val(),
		"customerCode" : customerCode,
		"customerName" : customerName,
		"shipToCode" : shipToCode,
		"isDefaultShipTo" : isDefaultShipTo,
		"shipToAddress" : shipToAddress,
		"billToCode" : billToCode,
		"billToAddress" : billToAddress,
		"currency" : currency,
		"currencyRate" : currencyRate,
		"docDate" : docDate,
		"dueDate" : dueDate,
		"PoNo" : PoNo,
		"filename" : file ? ' &nbsp;&nbsp; <span class="label label-info label-white middle"><i class="fa fa-file-o"></i>&nbsp; Attached file : ' + file.name + '</span>' : '',
		"priceList" : priceList,
		"termName" : termName,
		"listName" : listName,
		"exShipTo" : exShipTo,
		"billOption" : $("input[name=billoption]:checked").val(),
		"requiredSQ" : $('#require-sq').is(':checked') ? 'Y' : 'N',
		"isExport" : $('#is-export').is(':checked') ? 'Y' : 'N',
		"remark" : $('#remark').val(),
		"items" : items,
		"subTotal" : subTotal,
		"docTotal" : docTotal,
		"priceEdit" : price_edit,
		"saleTeam" : saleTeam,
		"areaId" : areaId,
		"creditBalance" : creditBalance,
		"creditDiff" : creditDiff,
		"creditIssue" : creditIssue,
		"creditMessage" : creditMessage,
		"isRegular" : isRegular,
		"flow" : flow
	}

	if(shipToWarning == 1) {
		swal({
			title:'โปรดทราบ',
			text:'มีการเปลี่ยนแปลงสถานที่จัดส่ง โปรดรออนุมัติ',
			type:'info',
			html:true,
			confirmButtonText:'รับทราบ',
			closeOnConfirm:true
		}, function() {
			checkApprove(data);
		})
	}
	else {
		checkApprove(data);
	}	
}


function checkApprove(data) {
	console.log(data);
	$.ajax({
		url:HOME + 'check_approve',
		type:'GET',
		cache:false,
		data:{
			'docTotal' : data.docTotal,
			'priceEdit' : data.price_edit,
			'customerCode' : data.customerCode,
			'saleTeam' : data.saleTeam,
			'areaId' : data.areaId
		},
		success:function(rs) {
			if(rs == 'pass') {
				var source = $('#preview-template').html();
				var output = $('#result');

				render(source, data, output);

				$('#previewModal').modal('show');
			}
			else {
				swal({
			    title:'',
			    text:rs,
			    type:'warning',
			    showCancelButton: true,
					confirmButtonText: 'ดำเนินการต่อ',
					cancelButtonText: 'กลับไปแก้ไข',
					closeOnConfirm: true
			  },function(){
					setTimeout(() => {
						var source = $('#preview-template').html();
						var output = $('#result');
						render(source, data, output);
						$('#previewModal').modal('show');
					}, 100);
			  })
			}
		}
	})
}

async function getOrderFlow(currentStep, creditIssue, overdue) {
	const url = `${HOME}get_order_flow`;
	const data = {
		currentStep: currentStep,		
		creditIssue: creditIssue,
		overdue: overdue
	};

	try {
		const response = await fetch(url, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json'
			},
			body: JSON.stringify(data)
		});

		const res = await response.text();
		return res;
	}
	catch (error) {
		console.error('Error fetching order flow:', error);
		return null;
	}
}

function warning(msg) {
	swal({
		title:"",
		text:msg,
		type:"warning",
		html:true
	})
}


function toggleSubmit() {
	let uncheck = 0;
	$('.check-list').each(function() {
			if(! $(this).is(":checked")) {
				uncheck++;
				$('#' + $(this).data('id')).removeClass('checked');
			}
			else {
				$('#' + $(this).data('id')).addClass('checked');
			}
	})

	if(uncheck == 0) {
		$('#btn-submit').removeAttr('disabled');
	}
	else {
		$('#btn-submit').attr('disabled', 'disabled');
	}
}


function toggleDiscount() {
	let term_id = $('#term').val();
	let discPrcnt = parseDefault(parseFloat($('#term option:selected').data('disc')), 0);

	if(term_id != '-10' && term_id != '') {
		swal({
			title:'โปรดทราบ',
			text:'คุณได้แจ้งและทำความเข้าใจกับลูกค้าแล้ว<br/>ถึงเงื่อนไขของส่วนลดที่สัมพันธ์กับ Payment term ที่เลือก',
			type:'warning',
			html:true,
			showCancelButton:true,
			confirmButtonText:'แจ้งและทำความเข้าใจแล้ว',
			cancelButtonText:'ยกเลิก',
			closeOnConfirm:true
		}, function(isConfirm) {
			if(isConfirm) {
				let canChange = $('#term option:selected').data('change');

				$('#discPrcnt').val(discPrcnt);

				if(canChange == 1) {
					$('#discPrcnt').removeAttr('disabled');
					$('#discPrcnt').focus();
				}
				else {
					$('#discPrcnt').attr('disabled', 'disabled');
				}

				recalTotal();
			}
			else {
				$('#term').val("");
				$('#discPrcnt').attr('disabled', 'disabled');
				$('#discPrcnt').val(0);
				recalTotal();
			}
		})
	}
	else {
		let canChange = $('#term option:selected').data('change');

		$('#discPrcnt').val(discPrcnt);

		if(canChange == 1) {
			$('#discPrcnt').removeAttr('disabled');
			$('#discPrcnt').focus();
		}
		else {
			$('#discPrcnt').attr('disabled', 'disabled');
		}

		recalTotal();
	}
}


function getRate() {
	var code = $('#currency').val();
	if(code === 'THB') {
		$('#currencyRate').val('1.000000');
	}
	else {
		$.ajax({
			url:HOME + 'get_currency_rate/'+code,
			type:"GET",
			cache:false,
			success:function(rs) {
				if(rs === "notfound") {
					$('#currencyRate').val(0);
					swal({
						title:'Error!',
						text:"ไม่พบอัตราแลกเปลี่ยนของวันนี้ กรุณาติดต่อผู้ดูแลระบบ",
						type:"error"
					});
				}
				else {
					$('#currencyRate').val(rs);
				}
			}
		})
	}
}


function saveAdd() {

	$('#previewModal').modal('hide');

	var ds = {
		//---- left column
		'CardCode' : $('#customer').val().trim(),  //****** required
		'CardName' : $('#customer option:selected').data('name'),
		'CustCode' : $('#customer option:selected').data('hcode'),
		'CustomerGroupNum' : $('#customer option:selected').data('groupnum'),
		'isControl' : $('#customer option:selected').data('control') == 'Y' ? 'Y' : 'N',
		'saleTeam' : $('#customer option:selected').data('saleteam'),
		'areaId' : $('#customer option:selected').data('area'),
		'SlpCode' : $('#customer option:selected').data('sale'),
		'CardGroup' : $('#customer option:selected').data('department'), //--- department
		'PriceList' : $('#priceList').val(),
		'SpecialPriceList' : $('#priceList option:selected').data('spid'),
		'PayToCode' : $('#billToCode').val(),
		'BillTo' : $('#BillTo').val(),
		'ShipToCode' : $('#shipToCode').val(),
		'isDefaultShipTo' : $('#shipToCode option:selected').data('default'),
		'ShipTo' : $('#ShipTo').val(),
		'exShipTo' : $('#exShipTo').val().trim(),
		'VatGroup' : $('#VatGroup').val(),
		'VatRate' : $('#vatRate').val(),
		'CardType' : $('#cardType').val(),
		//--- right Column
		'DocDate' : $('#DocDate').val(), //****** required
		'DocDueDate' : $('#DocDueDate').val(), //****** required
		'NumAtCard' : $('#PoNo').val(),
		'DocCur' : $('#currency').val(),
		'DocRate' : $('#currencyRate').val(),
		//---- footer
		'TotalBefDi' : parseDefault(parseFloat(removeCommas($('#totalBefDi').val())), 0),
		'term' : $('#term').val(),
		'DiscType' : $('#term option:selected').data('change') == '1' ? 'M' : 'A',
		'GroupNum' : $('#term option:selected').data('groupnum'),
		'DiscPrcnt' : parseDefault(parseFloat($('#discPrcnt').val()), 0),
		'DiscSum' : parseDefault(parseFloat(removeCommas($('#discSum').val())), 0),
		'billOption' : $("input[name=billoption]:checked").val(),
		'requireSQ' : $('#require-sq').is(':checked') ? 'Y' : 'N',
		'isExport' : $('#is-export').is(':checked') ? 'Y' : 'N',
		'comments' : $.trim($('#remark').val()),
		'TotalAfDisc' : parseDefault(parseFloat(removeCommas($('#totalAmount').val())), 0),
		'totalVat' : parseDefault(parseFloat(removeCommas($('#totalVat').val())), 0),
		'docTotal' : parseDefault(parseFloat(removeCommas($('#docTotal').val())), 0),
		'is_discount_sales' : 0,
		'isRegular' : parseDefaultInt($('#is-regular').val(), 0),
		'creditIssue' : 0,
		'creditDiff' : 0
	}

	creditBalance = parseDefaultFloat(removeCommas($('#credit-balance').val()), 0);
	creditDiff = (creditBalance - ds.docTotal) * (-1);
	creditDiff = creditDiff > 0 ? (creditDiff > ds.docTotal ? ds.docTotal : creditDiff) : 0;
	ds.creditDiff = creditDiff;
	ds.creditIssue = creditDiff > 0 ? 1 : 0;

	var details = [];

	var vatGroup = $('#VatGroup').val(); //---- Vat code ตาม customer ถ้าไม่มีใช้ตาม item
	var docRate = $('#vatRate').val(); //--- vat rate ตาม customer ถ้าไม่มีใช้ตาม item

	$('.item-code').each(function() {
		if($(this).val() != "") {
			let no = $(this).data('no');
			let vatCode = $('#itemVatCode-'+no).val(); ///---- vat code at item
			let vatRate = $('#itemVatRate-'+no).val(); ///---- vat rate at item
			let sellPrice = $('#price-'+no).val();
			let stdPrice = parseDefault(parseFloat($('#stdPrice-'+no).val()), 0);
			let dis = $('#dis-'+no).is(':checked') ? 1 : 0;
			let step_id = $('#step-'+no).val();

			let item = {
				"ItemCode" : $(this).val(),
				"ItemName" : $("#item-"+no).val(),
				"isControl" : $('#control-'+no).val(),
				"Qty" : parseDefault(parseFloat($('#qty-'+no).val()), 1),
				"freeQty" : parseDefault(parseFloat($('#free-'+no).val()), 0),
				"UomCode" : $('#uom-'+no).val(),
				"stdPrice" : stdPrice,
				"SellPrice" : (sellPrice == "" ? stdPrice : sellPrice),
				"VatGroup" : (vatGroup == "" ? vatCode : vatGroup), //---- Vat code ตาม customer ถ้าไม่มีใช้ตาม item
				"VatRate" : (vatGroup == "" ? vatRate : docRate), //--- vat rate ตาม customer ถ้าไม่มีใช้ตาม item
				"VatAmount" : $('#vatAmount-'+no).val(),
				"lineTotal" : $('#amount-'+no).val(),
				"lineText" : $('#remark-'+no).val(),
				"WhsCode" : $('#whsCode-'+no).val(),
				"discount_sales" : dis,
				"step_id" : step_id
			}

			if($('#freeTxt-'+no).length) {
				item.freeTxt = $('#freeTxt-'+no).val();
			}

			if(dis == 1) {
				ds.is_discount_sales = 1;
			}

			details.push(item);
		}
	}); //--- end each function

	// let header = JSON.stringify(ds);
	// let line = JSON.stringify(details);

	let showWarning = $('#show-warning').val();

	if(showWarning == 1) {
		setTimeout(() => {
			swal({
				title: 'คำเตือน',
				text: $('#warning-message').val(),
				type: 'info',
				html: true,
				showCancelButton: true,
				confirmButtonText: 'รับทราบ',
				cancelButtonText: 'กลับไปแก้ไข',
				closeOnConfirm: true
			}, function (isConfirm) {
				if (isConfirm) {
					add(ds, details);
				}
				else {
					$('#previewModal').modal('show');
				}
			});
		}, 200);
		
	}
	else {
		add(ds, details);
	}
}

function add(ds, details) {
	const uploadFile = document.getElementById('uploadFile');
	const file = uploadFile.files[0];
	const fd = new FormData();

	fd.append('header', JSON.stringify(ds));
	fd.append('details', JSON.stringify(details));

	if(file) {
		fd.append('uploadFile', file);		
	}

	load_in();

	$.ajax({
		url: `${HOME}add`,
		type:'POST',		
		data:fd,
		processData: false,
		contentType: false,
		success:function(rs) {
			load_out();
			
			if(isJson(rs)) {
				var ds = JSON.parse(rs);

				if(ds.status == 'success') {
					swal({
						title:'Success',
						type:'success',
						timer:1000
					});

					setTimeout(() => {
						goBack();
					}, 1200);
				}
				else {
					if(ds.upload == 0) {
						swal({
							title:'Upload Failed',
							text:'Create order success but upload file failed',
							type:'info',
							html:true
						},function() {
							goBack();
						});
					}
					else {
						showError(rs);
					}
				}
			}
			else {
				showError(rs);
			}
		},
		error:function(rs) {
			showError(rs);
		}
	});
}


function clearText(no) {
	let priceList = $('#priceList').val();
	$('#item-'+no).val('0').trigger('change');
	$('#itemCode-'+no).val('');

	if(priceList == 'x') {
		$('#step-'+no).html('<option value="0">Choose Step</option>');
	}

	$('#step-'+no).val('0');
	$('#step-data-'+no).val('');
	$('#uom-'+no).val('');
	$('#qty-'+no).val('');
	$('#free-'+no).val('');
	$('#amount-'+no).val('');
	$('#stdPrice-'+no).val('');
	$('#price-'+no).val('');
	$('#instock-'+no).val('');
	$('#commit-'+no).val('');
	$('#available-'+no).val('');
	$('#itemVatCode-'+no).val('');
	$('#itemVatRate-'+no).val('');
	$('#whsCode-'+no).val('');

	recalAmount(no);
	$('#item-'+no).focus();
}


$('.autosize').autosize({append: "\n"});

function wordCount(el, no) {
	let count = el.val().length;
	$('#word-count-'+no).text(count);
}


$('#discPrcnt').keyup(function() {
	recalTotal();
})


function getCreditBalanceDetails() {
	let code = $('#customer').val();

	if(code != "") {
		load_in();
		$.ajax({
			url:`${HOME}get_credit_data`,
			type:'GET',
			cache:false,
			data:{
				'CardCode' : code
			},
			success:function(rs) {
				load_out();
				
				if(isJson(rs)) {
					let ds = JSON.parse(rs);
					let data = ds.data;
					data.CreditBalance = addCommas(parseDefault(parseFloat(data.CreditBalance), 0).toFixed(2));
					data.CreditLine = addCommas(parseDefault(parseFloat(data.CreditLine), 0).toFixed(2));
					data.OrderUsed = addCommas(parseDefault(parseFloat(data.OrderUsed), 0).toFixed(2));
					data.Balance = addCommas(parseDefault(parseFloat(data.Balance), 0).toFixed(2));
					data.DNotesBal = addCommas(parseDefault(parseFloat(data.DNotesBal), 0).toFixed(2));
					data.OrdersBal = addCommas(parseDefault(parseFloat(data.OrdersBal), 0).toFixed(2));

					let source = $('#credit-detail-template').html();
					let output = $('#credit-detail-table');
					render(source, data, output);
					$('#creditDetailModal').modal('show');
				}
				else {
					showError(rs);
				}				
			},
			error:function(rs) {				
				showError(rs);
			}
		});
	}
}

function getCustomerInvoiceDetails() {
	let code = $('#customer').val();

	if(code != "") {
		load_in();

		$.ajax({
			url:`${HOME}get_customer_invoice`,
			type:'GET',
			cache:false,
			data:{
				'CardCode' : code
			},
			success:function(rs) {
				load_out();
				
				if(isJson(rs)) {
					let ds = JSON.parse(rs);
					let data = ds.data;

					let source = $('#invoice-detail-template').html();
					let output = $('#invoice-detail-table');
					render(source, data, output);
					$('#invoiceDetailModal').modal('show');
				}
				else {
					showError(rs);
				}				
			},
			error:function(rs) {				
				showError(rs);
			}
		});
	}
}

function changePriceListType() {
	let pType = $('#price-list-type').val();
	let customer = $('#customer').val();
			
	load_in();

	$.ajax({
		url:`${HOME}get_price_list_by_type`,
		type:'POST',
		cache:false,
		data:{
			'CardCode' : customer,
			'type' : pType
		},
		success:function(rs) {
			load_out();
			if(isJson(rs)) {
				let ds = JSON.parse(rs);
				$('#priceList').html(ds.priceList);		
				$('#priceList').val('').trigger('change');
				checkPriceList();
			}
			else {
				showError(rs);
			}
		},
		error:function(rs) {
			showError(rs);
		}
	})
}