<?php $this->load->view('include/header'); ?>
<script src="<?php echo base_url(); ?>assets/js/html5-qrcode.min.js"></script>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
		<div id="reader" style="width:600px;">

		</div>
	</div>
</div>

<script>
// Create instance of the object. The only argument is the "id" of HTML element created above.
//const html5QrCode = new Html5Qrcode("reader");

// html5QrCode.start(
//   cameraId,     // retreived in the previous step.
//   {
//     fps: 10,    // sets the framerate to 10 frame per second
//     qrbox: 250  // sets only 250 X 250 region of viewfinder to
//                 // scannable, rest shaded.
//   },
//   qrCodeMessage => {
//     // do something when code is read. For example:
//     console.log(`QR Code detected: ${qrCodeMessage}`);
//   },
//   errorMessage => {
//     // parse error, ideally ignore it. For example:
//     console.log(`QR Code no longer in front of camera.`);
//   })
// .catch(err => {
//   // Start failed, handle it. For example,
//   console.log(`Unable to start scanning, error: ${err}`);
// });


function onScanSuccess(decodedText, decodedResult) {
	alert(`Code scanned = ${decodedText}`, decodedResult);
	return
}

var html5QrcodeScanner = new Html5QrcodeScanner(
"reader", { formatsToSupport: [ Html5QrcodeSupportedFormats.EAN_13 ], fps: 20, qrbox: 550 });
html5QrcodeScanner.render(onScanSuccess);
</script>
<?php $this->load->view('include/footer'); ?>
