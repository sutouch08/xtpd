<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/import-modal.css?v=<?php echo date('Ymd'); ?>" />

<div class="modal fade" id="import-modal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:520px;">
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="importModalLabel">Import Items</h4>
      </div>

      <div class="modal-body">
        <form id="upload-form" name="upload-form" method="post" enctype="multipart/form-data">
          <input type="file" class="hide" name="uploadFile" id="uploadFile" accept=".xlsx,.xls" />
          <input type="hidden" name="555" />

          <!-- Drop Zone -->
          <div id="import-drop-zone" onclick="getFile()">
            <i class="fa fa-cloud-upload drop-icon"></i>
            <div class="drop-title">Click to select or drag &amp; drop Excel file here</div>
            <div class="drop-sub">Supports .xlsx, .xls &nbsp;&bull;&nbsp; Max size 5 MB</div>
          </div>

          <!-- File Info -->
          <div id="import-file-info" style="display:none; margin-top:16px; padding:10px 14px; background:#e8f5e9; border:1px solid #a5d6a7; border-radius:6px;">
            <i class="fa fa-file-excel-o file-icon" style="font-size:22px; color:#2e7d32; margin-right:8px;"></i>
            <div style="flex:1; min-width:0;">
              <div class="file-name" id="import-file-name" style="font-size:13px; font-weight:600; color:#1b5e20; word-break:break-all;"></div>
              <div class="file-size" id="import-file-size" style="font-size:11px; color:#4caf50;"></div>
            </div>
            <button type="button" class="btn-remove-file" onclick="clearImportFile()" style="margin-left:auto; background:none; border:none; color:#c62828; font-size:16px; cursor:pointer;">
              <i class="fa fa-times-circle"></i>
            </button>
          </div>
          <div id="import-file-info-flex-helper" style="display:none;"></div>

          <!-- Progress Bar -->
          <div id="import-progress-wrap" style="display:none; margin-top:16px;">
            <div class="progress">
              <div id="import-progress-bar" class="progress-bar progress-bar-striped active" role="progressbar" style="width:0%"></div>
            </div>
            <div class="progress-label" id="import-progress-label" style="font-size:11px; color:#6c757d; text-align:right;">Uploading...</div>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">
          <i class="fa fa-times"></i> Cancel
        </button>
        <button type="button" class="btn btn-success" id="btn-do-import" onclick="uploadfile()">
          <i class="fa fa-cloud-upload"></i> Import
        </button>
      </div>

    </div>
  </div>
</div>

<script>
  function showImportModal() {
    clearImportFile();
    $('#import-modal').modal('show');
  }

  function getFile() {
    $('#uploadFile').click();
  }

  function clearImportFile() {
    document.getElementById('uploadFile').value = '';
    document.getElementById('import-file-info').style.display = 'none';
    document.getElementById('import-drop-zone').style.display = '';
    document.getElementById('import-progress-wrap').style.display = 'none';
    document.getElementById('import-progress-bar').style.width = '0%';
  }

  function formatBytes(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / 1048576).toFixed(1) + ' MB';
  }

  const uploadFile = document.getElementById('uploadFile');

  uploadFile.addEventListener('change', function() {
    if (this.files.length > 0) {
      const file = this.files[0];
      if (file.size > 5000000) {
        swal("ขนาดไฟล์ใหญ่เกินไป", "ไฟล์แนบต้องมีขนาดไม่เกิน 5 MB", "error");
        this.value = '';
        return false;
      }
      document.getElementById('import-file-name').textContent = file.name;
      document.getElementById('import-file-size').textContent = formatBytes(file.size);
      document.getElementById('import-file-info').style.display = 'flex';
      document.getElementById('import-drop-zone').style.display = 'none';
    }
  });

  // Drag & Drop
  const dropZone = document.getElementById('import-drop-zone');

  dropZone.addEventListener('dragover', function(e) {
    e.preventDefault();
    this.classList.add('dragover');
  });

  dropZone.addEventListener('dragleave', function() {
    this.classList.remove('dragover');
  });

  dropZone.addEventListener('drop', function(e) {
    e.preventDefault();
    this.classList.remove('dragover');
    const dt = e.dataTransfer;
    if (dt.files.length > 0) {
      uploadFile.files = dt.files;
      uploadFile.dispatchEvent(new Event('change'));
    }
  });

  async function uploadfile() {
    const id = $('#id').val();
    const file = uploadFile.files[0];

    if (!file) {
      swal("กรุณาเลือกไฟล์ก่อน", "", "warning");
      return false;
    }

    const fd = new FormData();
    fd.append('uploadFile', file);    

    // Show progress
    document.getElementById('import-progress-wrap').style.display = '';
    document.getElementById('btn-do-import').disabled = true;

    // Animate progress bar with XHR for real progress
    const url = `${HOME}import_details/${id}`;

    const xhr = new XMLHttpRequest();

    xhr.upload.addEventListener('progress', function(e) {
      if (e.lengthComputable) {
        const pct = Math.round((e.loaded / e.total) * 90);
        document.getElementById('import-progress-bar').style.width = pct + '%';
        document.getElementById('import-progress-label').textContent = 'Uploading... ' + pct + '%';
      }
    });

    xhr.addEventListener('load', function() {
      document.getElementById('import-progress-bar').style.width = '100%';
      document.getElementById('import-progress-label').textContent = 'Done';
      document.getElementById('btn-do-import').disabled = false;

      const result = xhr.responseText;

      $('#import-modal').modal('hide');

      if (isJson(result)) {
        const res = JSON.parse(result);
        if (res.status === 'success') {
          swal({
            title: 'นำเข้าเรียบร้อยแล้ว',
            text: res.message,
            type: 'success',            
            html: true
          }, function() {
            window.location.reload();
          });
        } 
        else {
          showError(res.message);
        }
      } 
      else {
        showError(result);
      }
    });

    xhr.addEventListener('error', function() {
      document.getElementById('btn-do-import').disabled = false;
      swal("เกิดข้อผิดพลาด", "ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้", "error");
    });

    xhr.open('POST', url);
    xhr.send(fd);
  }
</script>