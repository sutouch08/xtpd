var HOME = `${BASE_URL}po_search/`;

function goBack() {
  window.location.href = HOME;
}

$('#fromDate').datepicker({
  dateFormat: 'dd-mm-yy',
  onClose: function(sd) {
    $('#toDate').datepicker('option', 'minDate', sd);
  }
});

$('#toDate').datepicker({
  dateFormat: 'dd-mm-yy',
  onClose: function(sd) {
    $('#fromDate').datepicker('option', 'maxDate', sd);
  }
});


function openFile(fileName) {
  const target = `${HOME}open_file/${fileName}`;
  const width = 1000;
  const height = 600;
  const left = (screen.width - width) / 2;
  const top = (screen.height - height) / 2;
  window.open(target, '_blank', `width=${width},height=${height},left=${left},top=${top}`);
}


function downloadFile(fileName) {
  const target = `${HOME}download_file/${fileName}`;
  window.location.href = target;
}


function printFile(fileName) {
  const target = `${HOME}print_file/${fileName}`;
  const width = 1000;
  const height = 600;
  const left = (screen.width - width) / 2;
  const top = (screen.height - height) / 2;
  const win = window.open(target, '_blank', `width=${width},height=${height},left=${left},top=${top}`);
  win.onload = function () {
    win.print(); // open print dialog
  };
}
