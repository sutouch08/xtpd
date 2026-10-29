$(document).ready(function() {
  syncData();
});


function syncData(){
  setTimeout(function(){
    syncQuotation();
  },1000);
}

// //--1 Sync Quotation
// function syncQuotation(){
//     $('body').append('Getting unSync Quotation ...<br/>');
//   $.get(BASE_URL +'sync_data/syncQuotationCode', function(){
//     $('body').append('finish update : Quotation ...<br/>');
//     $('body').append('============================================ <br/>');
//     setTimeout(function(){
//       syncCustomer();
//     },1000);
//   });
// }

//--1 Sync Quotation
function syncQuotation(){
    $('body').append('Getting unSync Quotation ...<br/>');
  $.get(BASE_URL +'sync_data/syncQuotationCode', function(){
    $('body').append('finish update : Quotation ...<br/>');
    $('body').append('============================================ <br/>');
    setTimeout(function(){
      syncCustomer();
    },1000);
  });
}


//--- 2. Sync Customer
function syncCustomer(){
    $('body').append('Getting unSync Business Partner ...<br/>');
  $.get(BASE_URL+'sync_data/syncCustomer', function(){
    $('body').append('finish update : Business Partner ...<br/>');
    $('body').append('============================================ <br/>');
    $('body').append('All done!');
    window.close();
  });
}
