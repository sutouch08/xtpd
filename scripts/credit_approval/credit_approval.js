var HOME = `${BASE_URL}credit_approval/`;
var ORDER = `${BASE_URL}orders/`;
var REQUEST_PAYMENT = `${BASE_URL}request_payment_order/`;

function goBack() {
	window.location.href = HOME;
}

$('#fromDate').datepicker({
	format: 'dd-mm-yyyy',
	onClose: function (sd) {
		$('#toDate').datepicker('option', 'minDate', sd);
	}
});

$('#toDate').datepicker({
	format: 'dd-mm-yyyy',
	onClose: function (sd) {
		$('#fromDate').datepicker('option', 'maxDate', sd);
	}
});

function preview(code) {
	load_in();

	$('#OrderCode').val(code);
	$('.a-btn').addClass('hide'); //-- hide all btn and will show only the btn that user can access

	$.ajax({
		url: `${HOME}get_order_detail`,
		type: 'GET',
		cache: false,
		data: {
			'code': code
		},
		success: function (rs) {
			load_out();
			if (isJson(rs)) {
				let ds = JSON.parse(rs);

				if (ds.status === 'success') {
					let data = ds.data;
					let source = $('#preview-template').html();
					let output = $('#result');
					let modal = $('#preview-modal');

					if (data.can_review && data.is_overdue && data.case_id == null && data.approval_status != 'A') {
						if(data.approval_status != 'R') {
							$('#btn-request').removeClass('hide');
							$('#btn-reject').removeClass('hide');						
						}

						console.log('a');
					}

					if (data.can_review && ((data.is_overdue && data.case_id != null) || (!data.is_overdue && data.case_id == null)) && !data.credit_review && data.approval_status != 'A') {
						if(data.approval_status != 'R') {
							$('#btn-accept').removeClass('hide');
							$('#btn-reject').removeClass('hide');						
						}

						console.log('b');
					}

					if (data.can_approve && data.credit_review && data.approval_status != 'A') {
						if(data.approval_status != 'R') {
							$('#btn-ap-approve').removeClass('hide');
							$('#btn-ap-reject').removeClass('hide');
						}

						modal = $('#approve-modal');
						source = $('#approve-template').html();
						output = $('#result-table');
						console.log('c');
					}

					render(source, data, output);
					modal.modal('show');
				}
				else {
					showError(ds.message);
				}
			}
			else {
				showError(rs);
			}
		},
		error: function (rs) {
			showError(rs);
		}
	})
}

function toggleApprove() {
	let check = 0;

	$('.check-item').each(function () {
		if ($(this).is(':checked')) {
			check++;
		}
	});

	if (check == 0) {
		$('#btn-ap-approve').attr('disabled', 'disabled');
		$('#btn-ap-reject').attr('disabled', 'disabled');
	}
	else {
		if (check > 0) {
			$('#btn-ap-approve').removeAttr('disabled');
			$('#btn-ap-reject').removeAttr('disabled');
		}
	}
}

function viewFile(code, fileName) {
	let width = 1000;
	let height = 600;
	let left = (screen.width - width) / 2;
	let top = (screen.height - height) / 2;
	let target = `${REQUEST_PAYMENT}open_file/${code}/${fileName}?nomenu&nonavbar`;
	window.open(target, "_blank", `width=${width}, height=${height}, left=${left}, top=${top}`);
}

function downloadFile(code, fileName) {
	let target = `${REQUEST_PAYMENT}download_file/${code}/${fileName}`;
	window.location.href = target;
}

function showAuthorize(code) {
	let diff = parseDefaultFloat(removeCommas($(`#credit-diff-${code}`).text()), 0);

	$.ajax({
		url: `${HOME}get_approver`,
		type: 'POST',
		cache: false,
		data: {
			'diff': diff
		},
		success: function (rs) {
			if (isJson(rs)) {
				let source = $('#authorizer-template').html();
				let data = $.parseJSON(rs);
				let output = $('#authorizer-table');

				render(source, data, output);

				$('#authorizer-modal').modal('show');
			}
			else {
				swal({
					title: "Error!",
					text: rs,
					type: 'error'
				})
			}
		}
	})
}

function accept() {	
	let code = $('#OrderCode').val().trim();

	swal({
		title: 'Are you sure ?',
		text: `Do you want to accept this document : ${code} ?`,
		type: 'info',
		showCancelButton: true,
		confirmButtonColor: '#5cb85c',
		confirmButtonText: 'Accept',
		cancelButtonText: 'Cancel',
		closeOnConfirm: true
	}, function () {
		setTimeout(() => {
			doAccept();
		}, 300);
	});
}

function doAccept() {
	$('#preview-payment-modal').modal('hide');
	let code = $('#OrderCode').val().trim();

	load_in();

	$.ajax({
		url: `${HOME}do_accept`,
		type: 'POST',
		cache: 'false',
		data: {
			'code': code
		},
		success: function (rs) {
			load_out();

			if (rs.trim() === 'success') {
				swal({
					title: "Success",
					type: 'success',
					timer: 1000
				});

				setTimeout(function () {
					window.location.reload();
				}, 1200);
			}
			else {
				showError(rs);
			}
		},
		error: function (rs) {
			showError(rs);
		}
	});
}

function approve() {
	let code = $('#OrderCode').val();
	let check = 0;
	let err = 0;
	let ds = {
		'code': code,
		'items': []
	};

	$('.check-item').each(function () {
		let id = $(this).val();
		if ($(this).is(':checked')) {
			let item = { "id": id, "status": "A" }
			ds.items.push(item);
			$('#reject-item-' + id).removeClass('has-error');
			check++;
		}
		else {
			let reject_text = $('#reject-item-' + id).val();
			if (reject_text.length) {
				let item = { "id": id, "status": "R", "reject_text": reject_text }
				ds.items.push(item);
			}
			else {
				$('#reject-item-' + id).addClass('has-error');
				err++;
			}
		}
	});

	if (err > 0) {
		swal({
			title: 'Required',
			text: 'กรุณาระบุเหตุผลในการ Reject ทุกรายการที่ไม่อนุมัติ',
			type: 'warning'
		});

		return false;
	}

	if(check == 0) {
		swal({
			title: 'Required',
			text: 'กรุณาเลือกอย่างน้อย 1 รายการ',
			type: 'warning'
		});
		return false;
	}

	swal({
		title: 'Are you sure ?',
		text: `Do you want to approve this order : ${code} ?`,
		type: 'info',
		showCancelButton: true,
		confirmButtonColor: '#5cb85c',
		confirmButtonText: 'Approve',
		cancelButtonText: 'Cancel',
		closeOnConfirm: true
	}, function () {
		setTimeout(() => {
			doApprove(ds);
		}, 300);
	});
}

function doApprove(ds) {	
	$('#approve-modal').modal('hide');
	load_in();
	
	$.ajax({
		url: `${HOME}do_approve`,
		type: 'POST',
		cache: 'false',
		data: {
			'data': JSON.stringify(ds)
		},
		success: function (rs) {
			load_out();

			if (rs.trim() === 'success') {
				swal({
					title: "Success",
					type: 'success',
					timer: 1000
				});

				setTimeout(function () {
					window.location.reload();
				}, 1200);
			}
			else {
				showError(rs);
			}
		},
		error: function (rs) {
			showError(rs);
		}
	});
}

function reject() {
	let code = $('#OrderCode').val();
	let check = 0;
	let err = 0;
	let count = 0;
	let ds = {
		'code': code,
		'items': []
	};

	$('.check-item').each(function () {
		if ($(this).is(':checked')) {
			let id = $(this).val();
			let reject_text = $("#reject-item-" + id).val();
			if (reject_text.length) {
				ds.items.push({ "id": id, "reject_text": reject_text });
				$('#reject-item-' + id).removeClass('has-error');
			}
			else {
				$('#reject-item-' + id).addClass('has-error');
				err++;
			}

			check++;
		}

		count++;
	});

	if (check == count) {
		if (err > 0) {
			swal({
				title: 'Required',
				text: 'กรุณาระบุเหตุผลในการ Reject ทุกรายการ',
				type: 'warning'
			});

			return false;
		}
	}
	else {
		swal({
			title: "",
			text: "กรุณาเลือกรายการทั้งหมด",
			type: "warning"
		});

		return false;
	}
	
	swal({
		title: 'Are you sure ?',
		text: `Do you want to reject this order : ${code} ?`,
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#d9534f',
		confirmButtonText: 'Reject',
		cancelButtonText: 'Cancel',
		closeOnConfirm: true
	}, function () {
		setTimeout(() => {
			doReject(ds);
		}, 300);
	});
}


function doReject(ds) {
	$('#approve-modal').modal('hide');
	$('#preview-payment-modal').modal('hide');
	let code = $('#OrderCode').val();
	load_in();

	$.ajax({
		url: `${HOME}do_reject`,
		type: 'POST',
		cache: 'false',
		data: {
			'data': JSON.stringify(ds)
		},
		success: function (rs) {
			load_out();

			if (rs.trim() === 'success') {
				swal({
					title: "Success",
					type: 'success',
					timer: 1000
				});

				setTimeout(function () {
					window.location.reload();
				}, 1200);
			}
			else {
				showError(rs);
			}
		},
		error: function (rs) {
			showError(rs);
		}
	});
}

function addRequestPayment(code) {
	load_in();

	$.ajax({
		url: `${HOME}get_order_data`,
		type: 'POST',
		cache: 'false',
		data: {
			'code': code
		},
		success: function (rs) {
			load_out();

			if (isJson(rs)) {
				let ds = JSON.parse(rs);

				if (ds.status === 'success') {
					let data = ds.data;
					$('#payment-order-date').val(data.date);
					$('#payment-order-code').val(data.code);
					$('#payment-user').val(`${data.emp_name}`);
					$('#payment-customer').val(data.customer);
					$('#payment-doc-total').val(data.doc_total);
					$('#payment-overdue-total').val(data.overdue);
					$('#payment-credit-diff').val(data.diff);
					$('#payment-message').val('').removeAttr('readonly').focus();
					$('#btn-send-request').removeClass('hide');

					$('#request-payment-modal').modal('show');
				}
				else {
					showError(ds.message);
				}
			}
			else {
				showError(rs);
			}
		},
		error: function (rs) {
			showError(rs);
		}
	})
}

function requestPayments() {
	$('#preview-modal').modal('hide');

	let code = $('#OrderCode').val();
	let user = $('#user').val();
	let emp_name = $('#emp-name').val();
	let customer = $('#customer-name').val();
	let overdue = $('#overdue-amount').val();
	let diff = $('#diff-amount').val();

	$('#payment-order-code').val(code);
	$('#payment-user').val(`${user} | ${emp_name}`);
	$('#payment-customer').val(customer);
	$('#payment-credit-diff').val(diff);
	$('#payment-overdue-total').val(overdue);
	$('#payment-message').val('').removeAttr('readonly').focus();
	$('#btn-send-request').removeClass('hide');

	$('#request-payment-modal').modal('show');
}

function sendRequest() {
	let code = $('#payment-order-code').val().trim();
	let message = $('#payment-message').val();

	$('#request-payment-modal').modal('hide');

	$.ajax({
		url: `${HOME}send_request_payment`,
		type: 'POST',
		cache: 'false',
		data: {
			'code': code,
			'message': message
		},
		success: function (rs) {
			if (rs.trim() === 'success') {
				swal({
					title: "Success",
					type: 'success',
					timer: 1000
				});

				setTimeout(function () {
					window.location.reload();
				}, 1200);
			}
			else {
				showError(rs);
			}
		},
		error: function (rs) {
			showError(rs);
		}
	});
}

function viewRequestPayment(code) {
	$('#OrderCode').val(code);
	$('.p-btn').addClass('hide'); //-- hide all btn and will show only the btn that user can access
	load_in();
	$.ajax({
		url: `${HOME}get_request_payment_data`,
		type: 'POST',
		cache: 'false',
		data: {
			'code': code
		},
		success: function (rs) {
			load_out();

			if (isJson(rs)) {
				let ds = JSON.parse(rs);

				if (ds.status === 'success') {
					let data = ds.data;
					let source = $('#preview-payment-detail-template').html();
					let output = $('#preview-payment-detail');
					render(source, data, output);

					if (data.has_document) {
						let source = $('#preview-files-template').html();
						let output = $('#preview-payment-file-list');
						render(source, data.files, output);
					}

					if (data.logs) {
						let source = $('#preview-logs-template').html();
						let output = $('#preview-payment-logs');
						render(source, data.logs, output);
					}

					if (data.can_review && data.status == 'O') {
						$('#btn-p-reject').removeClass('hide');
						$('#btn-p-approve').removeClass('hide');
					}

					$('#preview-payment-modal').modal('show');
				}
				else {
					showError(ds.message);
				}
			}
			else {
				showError(rs);
			}
		},
		error: function (rs) {
			showError(rs);
		}
	});
}

