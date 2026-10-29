var HOME = BASE_URL + 'user_group/';

function goBack(){
  window.location.href = HOME;
}


function groupViewCheck(el, id)
{
	if(el.is(":checked")){
		$(".view-"+id).each(function(index, element) {
			$(this).prop("checked",true);
		});
	}else{
		$(".view-"+id).each(function(index, element) {
			$(this).prop("checked",false);
		});
	}
}


function groupAddCheck(el, id)
{
	if(el.is(":checked")){
		$(".add-"+id).each(function(index, element) {
			$(this).prop("checked",true);
		});
	}else{
		$(".add-"+id).each(function(index, element) {
			$(this).prop("checked",false);
		});
	}
}

function groupEditCheck(el, id)
{
	if(el.is(":checked")){
		$(".edit-"+id).each(function(index, element) {
			$(this).prop("checked",true);
		});
	}else{
		$(".edit-"+id).each(function(index, element) {
			$(this).prop("checked",false);
		});
	}
}

function groupDeleteCheck(el, id)
{
	if(el.is(":checked")){
		$(".delete-"+id).each(function(index, element) {
			$(this).prop("checked",true);
		});
	}else{
		$(".delete-"+id).each(function(index, element) {
			$(this).prop("checked",false);
		});
	}
}



function groupAllCheck(el, id)
{
  var view = $("#view-group-"+id);
  var add = $("#add-group-"+id);
  var edit = $("#edit-group-"+id);
  var del  = $("#delete-group-"+id);

	if(el.is(":checked")){
		view.prop("checked", true);
		groupViewCheck(view, id);
		add.prop("checked", true);
		groupAddCheck(add, id);
		edit.prop("checked", true);
		groupEditCheck(edit, id);
		del.prop("checked", true);
		groupDeleteCheck(del, id);

	}else{
    view.prop("checked", false);
		groupViewCheck(view, id);
		add.prop("checked", false);
		groupAddCheck(add, id);
		edit.prop("checked", false);
		groupEditCheck(edit, id);
		del.prop("checked", false);
		groupDeleteCheck(del, id);

	}
}


function allCheck(el, id_tab){
	if(el.is(":checked")){
		$("."+id_tab).each(function(index, element) {
            $(this).prop("checked", true);
        });
	}else{
		$("."+id_tab).each(function(index, element) {
            $(this).prop("checked", false);
        });
	}
}



function savePermission(){
  var form = $('#permissionForm');

  load_in();

  $.ajax({
    url:HOME + 'update_permission',
    type:'POST',
    data:form.serialize(),
    success:function(rs) {
      load_out();
      if(rs === 'success') {
        swal({
          title:"Success",
          type:"success",
          timer:1500
        });
      }
      else {
        swal({
          title:'Error!',
          type:'error',
          text:rs
        });
      }
    },
    error:function(xhr) {
      load_out();
      swal({
        title:"Error!!",
        text:"Error : "+xhr.responseText,
        type:"error",
        html:true
      })
    }
  })
}
