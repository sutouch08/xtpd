var HOME = `${BASE_URL}setting/`;

function updateConfig(formName)
{
	load_in();
	var formData = $("#"+formName).serialize();
	$.ajax({
		url: `${HOME}update_config`,
		type:"POST",
    cache:false,
    data: formData,
		success: function(rs){
			load_out();
      rs = $.trim(rs);
      if(rs == 'success'){
        swal({
          title:'Updated',
          type:'success',
          timer:1000
        });
      }else{
        swal('Error!', rs, 'error');
      }
		}
	});
}

function toggleOption(el) {
	let name = el.data('name');
	let option = el.is(':checked') ? 1 : 0;
	$("input[name='" + name + "']").val(option);
	console.log(name + ' : ' + $("input[name='" + name + "']").val());
}

function changeURL(tab) {
	var url = HOME + 'index/' + tab;
	var stObj = { stage: 'stage' };
	window.history.pushState(stObj, 'setting', url);
}