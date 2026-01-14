<?php
// Include Logic agar variabel ($setting, $schedule, dll) tersedia
include '../config/logic/settings_logic.php'; 

$pageTitle = "System Settings";
$currentPage = "settings";
ob_start();
?>

<?php $customCSS = '
    <link rel="stylesheet" href="../assets/css/dashboard2/settings.css">
    <link rel="stylesheet" href="../assets/css/toaster.css"> 
'; ?>

<?php if(isset($_SESSION['flash_status']) && isset($_SESSION['flash_message'])): ?>
    <script src="../assets/js/toaster.js"></script> 
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let status = "<?= $_SESSION['flash_status'] ?>";
            let msg = "<?= $_SESSION['flash_message'] ?>";
            let title = status === 'success' ? 'Berhasil!' : 'Gagal!';
            showToast(status, title, msg);
        });
    </script>
    <?php 
        unset($_SESSION['flash_status']); 
        unset($_SESSION['flash_message']); 
    ?>
<?php endif; ?>

<div class="header-halaman">
  <h1>Pengaturan Toko</h1>
  <p class="text-muted">Kontrol pusat operasional, jam kerja, dan status restoran.</p>
</div>

<div class="settings-grid">

  <div class="left-col">

    <form method="POST" class="card-glass" id="form-status">
      <div class="card-header">
        <h3>Status Toko</h3>
      </div>
      <div class="form-group">
        <span class="form-desc">Pilih kendali manual untuk menutup atau membuka toko.</span>
        <div class="input-group-inline">
          <select name="force_status" class="form-control"
            data-original="<?= $setting['force_status'] ?>"
            onchange="checkChanges('form-status', 'btn-status')">
            <option value="auto" <?= $setting['force_status'] == 'auto' ? 'selected' : '' ?>>Otomatis</option>
            <option value="open" <?= $setting['force_status'] == 'open' ? 'selected' : '' ?>>Paksa Buka</option>
            <option value="close" <?= $setting['force_status'] == 'close' ? 'selected' : '' ?>>Paksa Tutup</option>
          </select>
          <button type="submit" name="save_status" id="btn-status" class="btn-save-card">
            <i class='bx bxs-save'></i> Save
          </button>
        </div>
        <small class="form-note">*Gunakan Paksa Tutup hanya saat darurat.</small>
      </div>
    </form>

    <form method="POST" class="card-glass" id="form-jadwal">
      <div class="card-header card-header-flex">
        <h3>Jadwal Toko</h3>
        <button type="submit" name="save_schedule" id="btn-jadwal" class="btn-save-card">
          <i class='bx bxs-save'></i> Save
        </button>
      </div>
      <div class="form-group">
        <span class="form-desc" style="margin-bottom: 20px;">Atur jam operasional rutin mingguan.</span>
        <div class="schedule-grid-compact">
          <?php foreach ($schedule as $day): ?>
            <div class="schedule-item-compact">
              <input type="hidden" name="sch_id[]" value="<?= $day['id'] ?>">
              <div class="day-label-compact"><?= $day['day_name'] ?></div>
              <div class="time-row-compact">
                <input type="time" name="sch_open[]" class="time-input-compact"
                  value="<?= $day['open_time'] ?>"
                  data-original="<?= $day['open_time'] ?>"
                  oninput="checkChanges('form-jadwal', 'btn-jadwal')">
                <span style="color:var(--text-muted); font-size:0.8rem;">-</span>
                <input type="time" name="sch_close[]" class="time-input-compact"
                  value="<?= $day['close_time'] ?>"
                  data-original="<?= $day['close_time'] ?>"
                  oninput="checkChanges('form-jadwal', 'btn-jadwal')">
                <button type="button" class="btn-clear-minimal"
                  onclick="clearRow(this); checkChanges('form-jadwal', 'btn-jadwal')">
                  <i class='bx bx-minus'></i>
                </button>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
        <small class="form-note" style="margin-top: 15px;">*Klik tombol (-) untuk mengosongkan jadwal (Libur).</small>
      </div>
    </form>

    <form method="POST" class="card-glass" id="form-shift">
      <div class="card-header card-header-flex">
        <h3>Shift Pegawai</h3>
        <button type="submit" name="save_shift" id="btn-shift" class="btn-save-card">
          <i class='bx bxs-save'></i> Save
        </button>
      </div>
      <div class="form-group">
        <span class="form-desc" style="margin-bottom: 20px;">Tentukan durasi waktu shift kerja.</span>
        <div class="schedule-grid-1col">
          <?php foreach ($shifts as $shift): ?>
            <div class="schedule-item-compact horizontal">
              <input type="hidden" name="shift_id[]" value="<?= $shift['id'] ?>">
              <div class="day-label-compact"><?= $shift['shift_name'] ?></div>
              <div class="time-row-compact">
                <input type="time" name="shift_start[]" class="time-input-compact"
                  value="<?= $shift['start_time'] ?>"
                  data-original="<?= $shift['start_time'] ?>"
                  oninput="checkChanges('form-shift', 'btn-shift')">
                <span style="color:var(--text-muted); font-size:0.8rem;">s/d</span>
                <input type="time" name="shift_end[]" class="time-input-compact"
                  value="<?= $shift['end_time'] ?>"
                  data-original="<?= $shift['end_time'] ?>"
                  oninput="checkChanges('form-shift', 'btn-shift')">
                <button type="button" class="btn-clear-minimal"
                  onclick="clearRow(this); checkChanges('form-shift', 'btn-shift')">
                  <i class='bx bx-minus'></i>
                </button>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
        <small class="form-note" style="margin-top: 15px;">*Shift yang dihapus (-) tidak akan muncul di opsi pegawai.</small>
      </div>
    </form>

  </div>

  <div class="right-col">

    <form method="POST" class="card-glass" id="form-kapasitas">
      <div class="card-header">
        <h3>Kapasitas Resto</h3>
      </div>
      <div class="form-group">
        <span class="form-desc">Total meja fisik tersedia.</span>
        <div class="input-group-inline">
          <div class="input-wrapper">
            <input type="number" name="total_tables" class="form-control"
              placeholder="0" value="<?= $setting['total_tables'] ?>"
              data-original="<?= $setting['total_tables'] ?>"
              oninput="checkChanges('form-kapasitas', 'btn-kapasitas')">
            <span class="input-unit">Meja</span>
          </div>
          <button type="submit" name="save_capacity" id="btn-kapasitas" class="btn-save-card">
            <i class='bx bxs-save'></i> Save
          </button>
        </div>
        <small class="form-note">*Sistem akan menutup reservasi jika semua meja penuh.</small>
      </div>
    </form>

    <div class="card-glass">
      <div class="card-header">
        <h3>Posisi & Jabatan</h3>
      </div>
      <div class="form-group">
        <span class="form-desc">Daftar peran kerja.</span>
        <div class="job-container">
          <?php foreach ($roles as $role): ?>
            <form method="POST" style="display:inline;">
              <input type="hidden" name="job_id" value="<?= $role['id'] ?>">
              <div class="job-badge">
                <?= htmlspecialchars($role['role_name']) ?>
                <button type="submit" name="delete_job" class="btn-del-job" onclick="return confirm('Hapus jabatan ini?')"><i class='bx bx-x'></i></button>
              </div>
            </form>
          <?php endforeach; ?>
          <button type="button" class="btn-add-job" onclick="openJobModal()"><i class='bx bx-plus'></i></button>
        </div>
      </div>
    </div>

    <div class="card-glass">
      <div class="card-header">
        <h3>Kalender Libur</h3>
      </div>
      <div class="form-group">
        <span class="form-desc">Tetapkan tanggal merah spesifik.</span>

        <form method="POST">
          <div class="holiday-form-stack">
            <div>
              <label class="form-label">Pilih Tanggal</label>
              <input type="date" name="h_date" class="form-control" required>
            </div>
            <div>
              <label class="form-label">Keterangan</label>
              <div class="desc-wrapper">
                <input type="text" name="h_desc" class="form-control-desc" placeholder="Contoh: Idul Fitri" maxlength="30" oninput="countChar(this)" required>
                <span class="char-counter" id="charCount">0/30</span>
              </div>
            </div>
            <button type="submit" name="add_holiday" class="btn-create-holiday">Create <i class='bx bx-plus'></i></button>
          </div>
        </form>

        <div class="holiday-list-container">
          <?php if (empty($holidays)): ?>
            <div style="text-align:center; padding:20px; color:var(--text-muted); font-size:0.9rem;">Belum ada jadwal libur.</div>
          <?php else: ?>
            <?php foreach ($holidays as $h): ?>
              <div class="holiday-item">
                <div class="holiday-date-box">
                  <span class="h-date-main"><?= date('d M', strtotime($h['date'])); ?></span>
                  <span class="h-year-small"><?= date('Y', strtotime($h['date'])); ?></span>
                </div>
                <div class="holiday-desc"><?= htmlspecialchars($h['description']); ?></div>
                <form method="POST">
                  <input type="hidden" name="h_id" value="<?= $h['id'] ?>">
                  <button type="submit" name="delete_holiday" class="btn-del-holiday" onclick="return confirm('Hapus tanggal ini?')"><i class='bx bx-trash'></i></button>
                </form>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>

  </div>
</div>

<div id="jobModal" class="modal-overlay-fixed">
  <div class="card-glass" style="width: 350px;">
    <h3>Update</h3>
    <form method="POST">
      <label class="form-label">Nama</label>
      <input type="text" name="role_name" class="form-control" placeholder="Nama posisi..." required style="margin-bottom: 30px;">
      <div style="display:flex; gap:15px; justify-content:center;">
        <button type="button" class="btn-modal-action cancel" onclick="closeJobModal()">Batal</button>
        <button type="submit" name="add_job" class="btn-modal-action save">Simpan</button>
      </div>
    </form>
  </div>
</div>

<script>
  // --- SMART SAVE LOGIC (VALIDASI CLIENT) ---
  function checkChanges(formId, btnId) {
    const form = document.getElementById(formId);
    const btn = document.getElementById(btnId);
    const inputs = form.querySelectorAll('[data-original]');
    let isChanged = false;
    let isValid = true; 

    // 1. Cek Perubahan Nilai
    inputs.forEach(input => {
      if (input.value !== input.getAttribute('data-original')) {
        isChanged = true;
      }
    });

    // 2. Validasi Logika (Jadwal & Shift)
    if (formId === 'form-jadwal' || formId === 'form-shift') {
      const rows = form.querySelectorAll('.time-row-compact');
      let prevEndTime = null; 

      rows.forEach((row, index) => {
        const times = row.querySelectorAll('input[type="time"]');
        if (times.length === 2) {
          const start = times[0].value;
          const end = times[1].value;

          // A. Cek Kelengkapan (Belang)
          if ((start !== '' && end === '') || (start === '' && end !== '')) {
            isValid = false;
          }

          // B. Cek Start < End (Dalam satu baris)
          if (start !== '' && end !== '' && start >= end) {
            isValid = false;
          }

          // C. Cek Tabrakan Shift (Khusus Form Shift)
          if (formId === 'form-shift' && start !== '' && end !== '') {
            if (prevEndTime !== null && start < prevEndTime) {
              isValid = false; 
            }
            prevEndTime = end; 
          }
        }
      });
    }

    // 3. Keputusan Tombol
    if (isChanged && isValid) {
      btn.classList.add('active');
      btn.disabled = false;
      btn.style.pointerEvents = "auto";
      btn.style.opacity = "1";
    } else {
      btn.classList.remove('active');
      btn.disabled = true;
      btn.style.pointerEvents = "none";
      btn.style.opacity = "0.5";
    }
  }

  // --- FUNGSI HAPUS ---
  function clearRow(btn) {
    const parentDiv = btn.parentElement;
    const inputs = parentDiv.querySelectorAll('input[type="time"]');
    inputs.forEach(input => {
      input.value = '';
    });

    const form = btn.closest('form');
    const btnSave = form.querySelector('.btn-save-card');
    checkChanges(form.id, btnSave.id);
  }

  function openJobModal() {
    document.getElementById('jobModal').classList.add('show');
  }

  function closeJobModal() {
    document.getElementById('jobModal').classList.remove('show');
  }

  function countChar(input) {
    document.getElementById('charCount').textContent = `${input.value.length}/30`;
  }
</script>

<?php 
$content = ob_get_clean();
include 'layouts.php'; 
?>