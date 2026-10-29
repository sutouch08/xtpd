var HOME = BASE_URL + 'user_pwd/';

function changePassword() {
  var use_strong_pwd = $('#use_strong_pwd').val();
  var cpwd = $('#cpwd').val(); //--- current pwd
  var pwd = $('#pwd').val(); //--- new pwd
  var cfpwd = $('#cfpwd').val();

  if(cpwd.length === 0) {
    set_error($('#cpwd'), $('#cpwd-error'), "Required");
    return false;
  }
  else {
    clear_error($('#cpwd'), $('#cpwd-error'));
  }

  if(pwd.length === 0) {
    set_error($('#pwd'), $('#pwd-error'), "Required");
    return false;
  }
  else {
    clear_error($('#pwd'), $('#pwd-error'));
  }

  if(use_strong_pwd == 1) {
    //--- check pwd is strong or not
    if(!validatePassword(pwd)) {
			$('#pwd-error').text('The password must have at least 8 characters, at least 1 digit(s), at least 1 lower case letter(s), at least 1 upper case letter(s), at least 1 non-alphanumeric character(s) ');
      $('#pwd').addClass('has-error');
      return false;
		}
		else {
			$('#pwd-error').text('');
			$('#pwd').removeClass('has-error');
		}
  }
  else {
    if(pwd.length < 6) {
      $('#pwd-error').text('The password must have at least 6 characters');
      $('#pwd').addClass('has-error');
      return false;
    }
    else {
      $('#pwd-error').text('');
			$('#pwd').removeClass('has-error');
    }
  }

  if(cfpwd !== pwd) {
    set_error($('#cfpwd'), $('#cfpwd-error'), "Password Mismatch");
    return false;
  }
  else {
    clear_error($('#cfpwd'), $('#cfpwd-error'));
  }

  $.ajax({
    url:HOME + 'verify_password',
    type:'POST',
    cache:false,
    data:{
      'pwd' : cpwd
    },
    success:function(rs) {
      var rs = $.trim(rs);
      if(rs === 'accepted') {
        $.ajax({
          url:HOME + 'change_password',
          type:'POST',
          cache:false,
          data:{
            'pwd' : pwd
          },
          success:function(rs) {
            var rs = $.trim(rs);
            if(rs === 'success') {
              swal({
                title:'Success',
                text:'Password changed',
                type:'success',
                timer:1000
              });

              setTimeout(function(){
                window.location.reload();
              },1200);
            }
            else {
              swal({
                title:'Error!',
                text:rs,
                type:'error'
              });
            }
          }
        })
      }
      else {
        set_error($('#cpwd'), $('#cpwd-error'), rs);
        return false;
      }
    }
  })

}



//-- use to validate password is strong or not
function validatePassword(input)
{
	var passw = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,20}$/;

	if(input.match(passw))
	{
		return true;
	}
	else
	{
		return false;
	}
}



function check_pwd() {
  var use_strong_pwd = $('#use_strong_pwd').val();
  var pwd = $('#pwd').val();
  var cfpwd = $('#cfpwd').val();
  var el = $('#cfpwd');
  var label = $('#cfpwd-error');

  if(use_strong_pwd == 1) {
    //--- check pwd is strong or not
    if(!validatePassword(pwd)) {
			$('#pwd-error').text('The password must have at least 8 characters, at least 1 digit(s), at least 1 lower case letter(s), at least 1 upper case letter(s), at least 1 non-alphanumeric character(s) ');
      $('#pwd').addClass('has-error');
      return false;
		}
		else {
			$('#pwd-error').text('');
			$('#pwd').removeClass('has-error');
		}
  }
  else {
    if(pwd.length < 6) {
      $('#pwd-error').text('The password must have at least 6 characters');
      $('#pwd').addClass('has-error');
      return false;
    }
    else {
      $('#pwd-error').text('');
			$('#pwd').removeClass('has-error');
    }
  }


  if(pwd !== cfpwd) {
    set_error(el, label, "Password didn't match");
  }
  else {
    clear_error(el, label);
  }
}

$('#pwd').focusout(function(){
  var pwd = $(this).val();
  if(pwd.length > 0) {
    check_pwd();
  }
})


$('#cfpwd').focusout(function(){
  check_pwd();
})
