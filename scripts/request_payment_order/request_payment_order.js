var HOME = `${BASE_URL}request_payment_order/`;
var ORDER  = `${BASE_URL}orders/`;

function goBack() {
	window.location.href = HOME;
}

$('#fromDate').datepicker({
	format:'dd-mm-yyyy',
	onClose:function(sd) {
		$('#toDate').datepicker('option', 'minDate', sd);
	}
});

$('#toDate').datepicker({
	format:'dd-mm-yyyy',
	onClose:function(sd) {
		$('#fromDate').datepicker('option', 'maxDate', sd);
	}
});


// function viewDetail(code) {
// 	let width = 1000;
// 	let height = 600;
// 	let left = (screen.width - width) / 2;
// 	let top = (screen.height - height) / 2;
// 	let target = `${HOME}view_detail/${code}?nomenu&nonavbar`;
// 	window.open(target, "_blank", `width=${width}, height=${height}, left=${left}, top=${top}`);
// }

function viewDetail(code) {
	load_in();

	$.ajax({
		url: `${HOME}get_detail`,
		type: 'GET',
		cache: false,
		data: {
			'code': code
		},
		success: function (rs) {
			load_out();

			if (isJson(rs)) {
				let ds = JSON.parse(rs);
				if(ds.status === 'success') {
					let data = ds.data;
					$('#payment-order-code').val(data.code);
					$('#payment-customer').val(data.customer);
					$('#payment-doc-total').val(data.doc_total);
					$('#payment-credit-diff').val(data.diff);
					$('#payment-overdue-total').val(data.overdue);
					$('#payment-date').val(data.date);
					$('#payment-user').val(data.request_by);
					$('#reply-message').val('').attr('placeholder', 'Last reply: '+data.reply_message);
					$('#payment-message').val(data.message);

					let files = ds.data.files;					
					let source = $('#file-template').html();
					let output = $('#file-table');
					render(source, files, output);

					reIndex('fno');
					$('#reply-modal').modal('show');
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


function viewFile(code, fileName) {
	let width = 1000;
	let height = 600;
	let left = (screen.width - width) / 2;
	let top = (screen.height - height) / 2;
	let target = `${HOME}open_file/${code}/${fileName}?nomenu&nonavbar`;
	window.open(target, "_blank", `width=${width}, height=${height}, left=${left}, top=${top}`);
}


function downloadFile(code, fileName) {
	let target = `${HOME}download_file/${code}/${fileName}`;
	window.location.href = target;
}


function confirmDeleteFile(no, code, fileName) {
	swal({
		title: "Are you sure ?",
		text: "Do you want to delete this file ?",
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#d9534f',
		confirmButtonText: 'Delete',
		cancelButtonText: 'Cancel',
		closeOnConfirm: true
	}, function() {
		setTimeout(() => {
			deleteFile(no, code, fileName);
		}, 300);
	});
}

function deleteFile(no, code, fileName) {
	load_in();

	$.ajax({
		url: `${HOME}delete_file`,
		type: 'POST',
		cache: false,
		data: {
			'code': code,
			'fileName': fileName
		},
		success: function (rs) {
			load_out();

			if(isJson(rs)) {
				let ds = JSON.parse(rs);
				if(ds.status === 'success') {
					swal({
						title: "Success",
						type: 'success',
						timer: 1000
					});

					$(`#row-${no}`).remove();

					updateFileList(code);				
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

function submitReply() {
	let code = $('#payment-order-code').val().trim();
	let message = $('#reply-message').val().trim();
	let files = $('.attached-file').length;

	if(files === 0) {
		swal("กรุณาแนบไฟล์ก่อนส่งข้อความ");
		return false;
	}

	if(message.length === 0) {
		swal("กรุณากรอกข้อความก่อนส่ง");
		return false;
	}

	$.ajax({
		url: `${HOME}submit_reply`,
		type: 'POST',
		cache: false,
		data: {
			'code': code,
			'message': message
		},
		success: function (rs) {
			if(rs.trim() === 'success') {
				$('#reply-modal').modal('hide');
				$('#reply-message').val('');
				$('#file-table').html('');				
				$('#uploadFile').val('');

				swal({
					title: "Success",
					type: 'success',
					timer: 1000
				});
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

				if(ds.status === 'success') {
					let data = ds.data;					
					let source = $('#preview-template').html();
					let output = $('#result');

					render(source, data, output);

					if(data.can_approve) {
						
						$('#btn-reject').removeClass('hide');
						
						if(data.is_overdue && data.case_id == null) {
							$('#btn-request').removeClass('hide');
						}
						else {
							$('#btn-approve').removeClass('hide');
						}						
					}

					$('#preview-modal').modal('show');
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
	})
}


function showAuthorize(code) {
	let diff = parseDefaultFloat(removeCommas($(`#credit-diff-${code}`).val()), 0);

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


function approve() {
	$('#preview-modal').modal('hide');

	let code = $('#OrderCode').val();

	swal({
		title:'Are you sure ?',
		text: `Do you want to approve this order : ${code} ?`,
		type: 'info',
		showCancelButton: true,
		confirmButtonColor: '#5cb85c',
		confirmButtonText: 'Approve',
		cancelButtonText: 'Cancel',
		closeOnConfirm: true
	}, function() {
		setTimeout(() => {
			doApprove();
		}, 300);
	});
}


function doApprove() {
	let code = $('#OrderCode').val();	

	load_in();

	$.ajax({
		url:`${HOME}do_approve`,
		type:'POST',
		cache:'false',
		data:{
			'code' : code
		},
		success:function(rs) {
			load_out();
			
			if(rs.trim() === 'success') {
				swal({
					title: "Success",
					type: 'success',
					timer: 1000
				});

				setTimeout(function() {
					window.location.reload();
				}, 1200);
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


function reject() {
	let code = $('#OrderCode').val();
	swal({
		title:'Are you sure ?',
		text: `Do you want to reject this order : ${code} ?`,
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#d9534f',
		confirmButtonText: 'Reject',
		cancelButtonText: 'Cancel',
		closeOnConfirm: true
	}, function() {
		setTimeout(() => {
			doReject();
		}, 300);
	});
}


function doReject() {
	let code = $('#OrderCode').val();	
	load_in();

	$.ajax({
		url:`${HOME}do_reject`,
		type:'POST',
		cache:'false',
		data:{
			'code' : code
		},
		success:function(rs) {
			load_out();
			
			if(rs.trim() === 'success') {
				swal({
					title: "Success",
					type: 'success',
					timer: 1000
				});

				setTimeout(function() {
					window.location.reload();
				}, 1200);
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


function addRequestPayment(code) {
	load_in();

	$.ajax({
		url:`${HOME}get_order_data`,
		type:'POST',
		cache:'false',
		data:{
			'code' : code
		},
		success:function(rs) {
			load_out();

			if(isJson(rs)) {
				let ds = JSON.parse(rs);

				if(ds.status === 'success') {
					let data = ds.data;
					$('#payment-order-code').val(data.code);					
					$('#payment-user').val(`${data.user} | ${data.emp_name}`);
					$('#payment-customer').val(data.customer);
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
		error:function(rs) {
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
	$('#payment-overdue-total').val(overdue);
	$('#payment-credit-diff').val(diff);
	$('#payment-message').val('').removeAttr('readonly').focus();
	$('#btn-send-request').removeClass('hide');

	$('#request-payment-modal').modal('show');	
}


function sendRequest() {
	let code = $('#payment-order-code').val().trim();
	let message = $('#payment-message').val();		

	$('#request-payment-modal').modal('hide');

	$.ajax({
		url:`${HOME}send_request_payment`,
		type:'POST',
		cache:'false',
		data:{
			'code' : code,
			'message' : message
		},
		success:function(rs) {
			if(rs.trim() === 'success') {
				swal({
					title: "Success",
					type: 'success',
					timer: 1000
				});

				setTimeout(function() {
					window.location.reload();
				}, 1200);
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


function viewRequestPayment(code) {
	load_in();
	$.ajax({
		url:`${HOME}get_request_payment_data`,
		type:'POST',
		cache:'false',
		data:{
			'code' : code
		},
		success:function(rs) {
			load_out();

			if(isJson(rs)) {
				let ds = JSON.parse(rs);

				if(ds.status === 'success') {
					let data = ds.data;
					$('#payment-order-code').val(data.code);					
					$('#payment-user').val(`${data.user} | ${data.emp_name}`);
					$('#payment-customer').val(data.customer);
					$('#payment-overdue-total').val(data.overdue);
					$('#payment-credit-diff').val(data.diff);
					$('#payment-message').val(data.message).attr('readonly', true);
					$('#btn-send-request').addClass('hide');

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
		error:function(rs) {
			showError(rs);
		}
	});
}

function getFile() {
	$('#uploadFile').click();
}

const uploadFile = document.getElementById('uploadFile');

uploadFile.addEventListener('change', function () {
	const files = this.files;

	if (files.length === 0) {
		return;
	}

	if (files.length > 5) {
		swal("เลือกไฟล์มากเกินไป", "สามารถนำเข้าได้สูงสุดครั้งละ 5 ไฟล์", "warning");
		this.value = '';
		return;
	}

	// ตรวจขนาดไฟล์แต่ละไฟล์
	for (let i = 0; i < files.length; i++) {
		if (files[i].size > 5000000) {
			swal("ไฟล์ใหญ่เกินไป", "ไฟล์ต้องมีขนาดไม่เกิน 5 MB ต่อไฟล์", "error");
			this.value = '';
			return;
		}
	}

	uploadfile();
});

async function uploadfile() {
	const code = $('#payment-order-code').val().trim();
	const files = uploadFile.files;

	if (files.length === 0) {
		swal("กรุณาเลือกไฟล์ก่อน", "", "warning");
		return false;
	}

	const fd = new FormData();

	for (let i = 0; i < files.length; i++) {
		fd.append('uploadFile[]', files[i]);
	}

	load_in();

	const url = `${HOME}upload_file/${code}`;
	const xhr = new XMLHttpRequest();

	xhr.addEventListener('load', function () {
		load_out();		
		const result = xhr.responseText;

		if (isJson(result)) {
			const res = JSON.parse(result);
			if (res.status === 'success') {
				updateFileList(code);
			} else {
				showError(res.message);
			}
		} else {
			showError(result);
		}
	});

	xhr.addEventListener('error', function () {
		load_out();
		swal("เกิดข้อผิดพลาด", "ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้", "error");
	});

	xhr.open('POST', url);
	xhr.send(fd);
}


async function updateFileList(code) {
	const url = `${HOME}get_file_list_json/${code}`;
	const response = await fetch(url);
	const data = await response.json();
	const source = $('#file-template').html();
	const output = $('#file-table');
	render(source, data, output);
}
