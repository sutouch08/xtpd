function doLogin(){
  var pwd = $('#txtPassword').val();
  var user = $('#txtUserName').val();
  var remember = 0;
  var err = $('#error');

  if($('#remember').is(":checked")){
    remember = 1;
  }

  if(user.length == 0) {
    err.text('Empty Username!');
    return false;
  }
  else {
    err.text('');
  }


  if(pwd.length == 0){
    err.text('Empty password!');
    return false;
  }
  else {
    err.text('');
  }

  $.ajax({
    url: BASE_URL + 'authentication/validate_credentials',
    type:'POST',
    cache:false,
    data:{
      'uname' : user,
      'pwd' : pwd,
      'remember' : remember
    }, success:function(rs) {
      var rs = $.trim(rs);
      if(rs === 'success') {
        window.location.href = BASE_URL + "main";
      }
      else {
        err.text(rs);
      }
    }
  })
}


$('#txtUserName').keyup(function(e){
  if(e.keyCode === 13) {
    
    let user = $(this).val();
    let pwd = $('#txtPassword').val();

    if(user.length > 0) {
      if(pwd.length === 0) {
        $('#txtPassword').focus();
      }
      else {
        doLogin();
      }
    }
  }
});


$('#txtPassword').keyup(function(e) {
  if(e.keyCode === 13) {
    let user = $('#txtUserName').val();
    let pwd = $(this).val();

    if(user.length === 0) {
      $('#txtUserName').focus();
    }
    else {
      doLogin();
    }
  }
})
