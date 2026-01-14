<?php
// Include logic untuk menyiapkan semua variabel ($roles, $shifts, $q_menu, dll)
include '../config/logic/manage_logic.php';

$pageTitle = "Manajemen Data";
$currentPage = "manage";

ob_start();
?>

<?php $customCSS = '
<link rel="stylesheet" href="../assets/css/dashboard2/manage.css">
<link rel="stylesheet" href="../assets/css/toaster.css"> 
'; ?>

<div class="header-halaman">
  <h1><i class='bx bx-data'></i> Manajemen Data</h1>
  <p class="text-muted">Pusat kontrol menu, pegawai, kategori, varian, dan add-ons.</p>
</div>

<div class="tab-navigation">
  <button class="tab-btn active" onclick="switchTab('menu')">Menu</button>
  <button class="tab-btn" onclick="switchTab('employees')">Pegawai</button>
  <button class="tab-btn" onclick="switchTab('categories')">Kategori</button>
  <button class="tab-btn" onclick="switchTab('variants')">Varian</button>
  <button class="tab-btn" onclick="switchTab('addons')">Add-ons</button>
</div>

<div id="tab-menu" class="tab-content active">
  <div class="card-glass">
    <button class="btn-add-float" onclick="openMenuModal()">
      <i class='bx bx-plus'></i> Menu Baru
    </button>
    <div class="card-table-wrapper">
      <div class="table-responsive">
        <table class="table-cyber">
          <thead>
            <tr>
              <th width="60">Img</th>
              <th class="text-left">Menu</th>
              <th class="text-center">Kategori</th>
              <th class="text-center">Harga</th>
              <th class="text-center">Status</th>
              <th class="text-center" width="100">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (mysqli_num_rows($q_menu) > 0): ?>
              <?php while ($m = mysqli_fetch_assoc($q_menu)):
                $realPath = "../assets/uploads/" . $m['image'];
                $imgSrc = (!empty($m['image']) && file_exists($realPath)) ? $realPath : "../assets/images/Default.jpg";
              ?>
                <tr>
                  <td>
                    <div class="img-box">
                      <img src="<?= $imgSrc ?>" onerror="this.src='../assets/images/Default.jpg'">
                    </div>
                  </td>
                  <td class="text-left">
                    <div class="row-title"><?= htmlspecialchars($m['name']) ?></div>
                    <div class="row-sub"><?= htmlspecialchars(substr($m['description'], 0, 40)) ?>...</div>
                  </td>
                  <td class="text-center"><span class="badge-cat"><?= htmlspecialchars($m['cat_name'] ?? 'Umum') ?></span></td>
                  <td class="text-center text-price">Rp <?= number_format($m['base_price'], 0, ',', '.') ?></td>
                  <td class="text-center">
                    <span class="status-pill <?= $m['is_available'] ? 'ready' : 'habis' ?>">
                      <?= $m['is_available'] ? 'Ready' : 'Habis' ?>
                    </span>
                  </td>
                  <td class="text-center">
                    <div class="action-flex">
                      <button class="btn-icon edit" onclick='editMenu(<?= json_encode($m) ?>)'><i class='bx bx-edit'></i></button>
                      <form method="POST" onsubmit="return confirm('Hapus menu ini?');" style="margin:0;">
                        <input type="hidden" name="menu_id" value="<?= $m['id'] ?>">
                        <button type="submit" name="delete_menu" class="btn-icon delete"><i class='bx bx-trash'></i></button>
                      </form>
                    </div>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" class="text-center" style="padding:60px;">Menu masih kosong.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
    <div class="pagination-container">
      <div class="pagination">
        <a href="?page_menu=<?= max(1, $page_menu - 1) ?>&tab=menu" class="page-link"><i class='bx bx-chevron-left'></i></a>
        <?php for ($i = 1; $i <= $total_pages_menu; $i++): ?>
          <a href="?page_menu=<?= $i ?>&tab=menu" class="page-link <?= ($page_menu == $i) ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
        <a href="?page_menu=<?= min($total_pages_menu, $page_menu + 1) ?>&tab=menu" class="page-link"><i class='bx bx-chevron-right'></i></a>
      </div>
    </div>
  </div>
</div>

<div id="tab-employees" class="tab-content">
  <div class="card-glass">
    <button class="btn-add-float" onclick="openEmpModal()">
      <i class='bx bx-user-plus'></i> Tambah Pegawai
    </button>
    <div class="card-table-wrapper">
      <div class="table-responsive">
        <table class="table-cyber">
          <thead>
            <tr>
              <th class="text-left" style="width: 250px;">Nama Pegawai</th>
              <th class="text-center">Jabatan</th>
              <th class="text-center">Shift</th>
              <th class="text-center">Jam Kerja</th>
              <th class="text-center">Keterangan</th>
              <th class="text-center" style="width: 100px;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($e = mysqli_fetch_assoc($q_emp)): ?>
              <tr>
                <td class="text-left">
                  <span style="font-weight: 600; color: #fff;"><?= htmlspecialchars($e['name']) ?></span>
                </td>
                <td class="text-center">
                  <span class="badge-role"><?= htmlspecialchars($e['role_name'] ?? '-') ?></span>
                </td>
                <td class="text-center">
                  <?= isset($e['shift_name']) ? htmlspecialchars($e['shift_name']) : '<span class="text-muted">-</span>' ?>
                </td>

                <td class="text-center">
                  <?php if (!empty($e['start_time']) && !empty($e['end_time'])) {
                    echo "<span class='mono-font'>" . date('H:i', strtotime($e['start_time'])) . " - " . date('H:i', strtotime($e['end_time'])) . "</span>";
                  } else {
                    echo "<span class='text-muted'>-</span>";
                  } ?>
                </td>

                <td class="text-center">
                  <?php
                  if ($e['keterangan'] !== '-' && !empty($e['keterangan'])) {
                    echo "<span style='color: #868585; font-weight:500;'>" . htmlspecialchars($e['keterangan']) . "</span>";
                  } else {
                    echo "<span class='text-muted'>-</span>";
                  }
                  ?>
                </td>

                <td class="text-center">
                  <div class="action-flex">
                    <button class="btn-icon edit" onclick='editEmp(<?= json_encode($e) ?>)'><i class='bx bx-edit'></i></button>
                    <form method="POST" onsubmit="return confirm('Hapus pegawai?');" style="margin:0;">
                      <input type="hidden" name="emp_id" value="<?= $e['id'] ?>">
                      <button type="submit" name="delete_employee" class="btn-icon delete"><i class='bx bx-trash'></i></button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endwhile; ?>
            <?php if (mysqli_num_rows($q_emp) == 0): ?>
              <tr>
                <td colspan="6" class="text-center" style="padding:40px;">Belum ada pegawai.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php if ($total_pages_emp > 1): ?>
      <div class="pagination-container">
        <div class="pagination">
          <a href="?page_emp=<?= max(1, $page_emp - 1) ?>&tab=employees" class="page-link"><i class='bx bx-chevron-left'></i></a>
          <?php for ($i = 1; $i <= $total_pages_emp; $i++): ?>
            <a href="?page_emp=<?= $i ?>&tab=employees" class="page-link <?= ($page_emp == $i) ? 'active' : '' ?>"><?= $i ?></a>
          <?php endfor; ?>
          <a href="?page_emp=<?= min($total_pages_emp, $page_emp + 1) ?>&tab=employees" class="page-link"><i class='bx bx-chevron-right'></i></a>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>

<div id="tab-categories" class="tab-content">
  <div class="card-glass">
    <button class="btn-add-float" onclick="openCatModal()">
      <i class='bx bx-plus'></i> Kategori Baru
    </button>
    <div class="card-table-wrapper">
      <div class="table-responsive">
        <table class="table-cyber">
          <thead>
            <tr>
              <th class="text-left" width="30%">Nama Kategori</th>
              <th class="text-center">Item</th>
              <th class="text-center">Terjual</th>
              <th class="text-center">Omset</th>
              <th class="text-center" width="100">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($c = mysqli_fetch_assoc($q_cat)): ?>
              <tr>
                <td class="text-left">
                  <span style="font-weight: 600; color: #fff; font-size: 0.95rem;"><?= htmlspecialchars($c['name']) ?></span>
                </td>
                <td class="text-center">
                  <span class="badge-cat" style="background: rgba(255,255,255,0.1); color: #fff;"><?= $c['total_items'] ?> Item</span>
                </td>
                <td class="text-center"><span class="text-muted">-</span></td>
                <td class="text-center"><span class="mono-font text-muted">-</span></td>
                <td class="text-center">
                  <div class="action-flex">
                    <button class="btn-icon edit" onclick='editCat(<?= json_encode($c) ?>)'><i class='bx bx-edit'></i></button>
                    <form method="POST" onsubmit="return confirm('Hapus kategori?');" style="margin:0;">
                      <input type="hidden" name="cat_id" value="<?= $c['id'] ?>">
                      <button type="submit" name="delete_category" class="btn-icon delete"><i class='bx bx-trash'></i></button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endwhile; ?>
            <?php if (mysqli_num_rows($q_cat) == 0): ?>
              <tr>
                <td colspan="5" class="text-center" style="padding:40px;">Belum ada kategori.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<div id="tab-variants" class="tab-content">
  <div class="card-glass">
    <button class="btn-add-float" onclick="openVarModal()">
      <i class='bx bx-purchase-tag-alt'></i> Varian Baru
    </button>
    <div class="card-table-wrapper">
      <div class="table-responsive">
        <table class="table-cyber">
          <thead>
            <tr>
              <th class="text-left" width="50%">Nama Varian</th>
              <th class="text-center">Harga</th>
              <th class="text-center" width="100">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($v = mysqli_fetch_assoc($q_var)): ?>
              <tr>
                <td class="text-left">
                  <span style="font-weight: 600; color: #fff;"><?= htmlspecialchars($v['name']) ?></span>
                </td>
                <td class="text-center">
                  <?php if ($v['extra_price'] > 0): ?>
                    <span class="mono-font text-price-plus">+ Rp <?= number_format($v['extra_price'], 0, ',', '.') ?></span>
                  <?php else: ?>
                    <span class="mono-font text-price-zero">Rp 0 (Free)</span>
                  <?php endif; ?>
                </td>
                <td class="text-center">
                  <div class="action-flex">
                    <button class="btn-icon edit" onclick='editVar(<?= json_encode($v) ?>)'><i class='bx bx-edit'></i></button>
                    <form method="POST" onsubmit="return confirm('Hapus varian?');" style="margin:0;">
                      <input type="hidden" name="var_id" value="<?= $v['id'] ?>">
                      <button type="submit" name="delete_variant" class="btn-icon delete"><i class='bx bx-trash'></i></button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endwhile; ?>
            <?php if (mysqli_num_rows($q_var) == 0): ?>
              <tr>
                <td colspan="3" class="text-center text-muted" style="padding:30px;">Belum ada varian.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<div id="tab-addons" class="tab-content">
  <div class="card-glass">
    <button class="btn-add-float" onclick="openAddonModal()">
      <i class='bx bx-list-plus'></i> Add-on Baru
    </button>
    <div class="card-table-wrapper">
      <div class="table-responsive">
        <table class="table-cyber">
          <thead>
            <tr>
              <th class="text-left" width="50%">Nama Add-on</th>
              <th class="text-center">Harga</th>
              <th class="text-center" width="100">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($a = mysqli_fetch_assoc($q_add)): ?>
              <tr>
                <td class="text-left">
                  <span style="font-weight: 600; color: #fff;"><?= htmlspecialchars($a['name']) ?></span>
                </td>
                <td class="text-center">
                  <?php if ($a['price'] > 0): ?>
                    <span class="mono-font text-price-plus">+ Rp <?= number_format($a['price'], 0, ',', '.') ?></span>
                  <?php else: ?>
                    <span class="mono-font text-price-zero">Rp 0</span>
                  <?php endif; ?>
                </td>
                <td class="text-center">
                  <div class="action-flex">
                    <button class="btn-icon edit" onclick='editAddon(<?= json_encode($a) ?>)'><i class='bx bx-edit'></i></button>
                    <form method="POST" onsubmit="return confirm('Hapus add-on?');" style="margin:0;">
                      <input type="hidden" name="addon_id" value="<?= $a['id'] ?>">
                      <button type="submit" name="delete_addon" class="btn-icon delete"><i class='bx bx-trash'></i></button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endwhile; ?>
            <?php if (mysqli_num_rows($q_add) == 0): ?>
              <tr>
                <td colspan="3" class="text-center text-muted" style="padding:30px;">Belum ada add-on.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<div id="menuModal" class="modal-overlay-fixed">
  <div class="card-glass">
    <h3 id="menuModalTitle" style="margin-bottom: 20px;">Menu Baru</h3>
    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="menu_id" id="menuIdInput">
      <div class="modal-grid">
        <div class="left-pane">
          <div class="upload-area" onclick="document.getElementById('fileInput').click()">
            <img id="previewImg" src="#" alt="Preview">
            <div class="upload-text" id="uploadPlaceholder">
              <i class='bx bx-cloud-upload'></i><span>Upload Foto</span>
            </div>
            <input type="file" name="image" id="fileInput" accept="image/*" onchange="previewFile(this)" style="display:none;">
          </div>
          <div class="form-group" style="margin-top:20px;">
            <label>Harga (Rp)</label>
            <input type="text" name="price" id="menuPriceInput" class="form-control" placeholder="0" required>
          </div>
          <div class="form-group">
            <label>Status Ketersediaan</label>
            <input type="checkbox" name="is_available" id="menuStatusInput" class="switch-input" checked>
            <label for="menuStatusInput" class="switch-wrapper">
              <span class="switch-text">Tampilkan di Menu</span>
              <div class="toggle-ios"></div>
            </label>
          </div>
        </div>
        <div class="right-pane">
          <div class="form-group">
            <label>Nama Menu</label>
            <input type="text" name="name" id="menuNameInput" class="form-control" placeholder="Nama menu..." required>
          </div>
          <div class="form-group">
            <label>Kategori</label>
            <select name="category_id" id="menuCatInput" class="form-control">
              <?php foreach ($cat_list as $c): ?>
                <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Deskripsi</label>
            <div style="position: relative;">
              <textarea name="description" id="menuDescInput" class="form-control" rows="4" maxlength="120" placeholder="Keterangan..." style="resize: none; padding-bottom: 25px;"></textarea>
              <div style="position: absolute; bottom: 8px; right: 12px; font-size: 0.75rem; color: var(--text-muted); pointer-events: none;">
                <span id="currentDescCount">0</span>/120
              </div>
            </div>
          </div>
          <div class="modal-buttons">
            <button type="button" class="btn-modal-action cancel" onclick="closeModal('menuModal')">Batal</button>
            <button type="submit" name="save_menu" class="btn-modal-action save">Simpan Data</button>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<div id="empModal" class="modal-overlay-fixed">
  <div class="card-glass" style="width: 400px;">
    <h3 id="empModalTitle" style="margin-bottom: 20px;">Pegawai Baru</h3>
    <form method="POST">
      <input type="hidden" name="emp_id" id="empIdInput">
      <div class="form-group"><label>Nama</label><input type="text" name="emp_name" id="empNameInput" class="form-control" required></div>

      <div class="form-group"><label>Jabatan</label><select name="emp_role" id="empRoleInput" class="form-control" required>
          <?php foreach ($roles as $r): ?>
            <option value="<?= $r['id'] ?>"><?= $r['role_name'] ?></option>
          <?php endforeach; ?>
        </select></div>

      <div class="form-group">
        <label>Shift / Status</label>
        <select name="emp_shift_input" id="empShiftInput" class="form-control" required>

          <option value="" disabled selected hidden> Pilih Jadwal </option>

          <option value="Cuti">Cuti Kerja</option>
          <option value="Izin">Izin Kerja</option>

          <?php foreach ($shifts as $s): ?>
            <option value="<?= $s['id'] ?>">
              <?= htmlspecialchars($s['shift_name']) ?>
            </option>
          <?php endforeach; ?>

        </select>
      </div>

      <div class="modal-buttons"><button type="button" class="btn-modal-action cancel" onclick="closeModal('empModal')">Batal</button><button type="submit" name="save_employee" class="btn-modal-action save">Simpan</button></div>
    </form>
  </div>
</div>

<div id="catModal" class="modal-overlay-fixed">
  <div class="card-glass" style="width: 350px;">
    <h3 id="catModalTitle" style="margin-bottom: 20px;">Kategori Baru</h3>
    <form method="POST">
      <input type="hidden" name="cat_id" id="catIdInput">
      <div class="form-group">
        <label>Nama Kategori</label>
        <input type="text" name="cat_name" id="catNameInput" class="form-control" required placeholder="Contoh: Makanan Berat">
      </div>
      <div class="modal-buttons">
        <button type="button" class="btn-modal-action cancel" onclick="closeModal('catModal')">Batal</button>
        <button type="submit" name="save_category" class="btn-modal-action save">Simpan</button>
      </div>
    </form>
  </div>
</div>

<div id="varModal" class="modal-overlay-fixed">
  <div class="card-glass" style="width: 350px;">
    <h3 id="varModalTitle" style="margin-bottom: 20px;">Varian Baru</h3>
    <form method="POST">
      <input type="hidden" name="var_id" id="varIdInput">
      <div class="form-group">
        <label>Nama Varian (Contoh: Level 1)</label>
        <input type="text" name="var_name" id="varNameInput" class="form-control" required>
      </div>
      <div class="form-group">
        <label>Harga Tambahan</label>
        <input type="text" name="var_price" id="varPriceInput" class="form-control input-rupiah" placeholder="0">
      </div>
      <div class="modal-buttons">
        <button type="button" class="btn-modal-action cancel" onclick="closeModal('varModal')">Batal</button>
        <button type="submit" name="save_variant" class="btn-modal-action save">Simpan</button>
      </div>
    </form>
  </div>
</div>

<div id="addonModal" class="modal-overlay-fixed">
  <div class="card-glass" style="width: 350px;">
    <h3 id="addonModalTitle" style="margin-bottom: 20px;">Add-on Baru</h3>
    <form method="POST">
      <input type="hidden" name="addon_id" id="addonIdInput">
      <div class="form-group">
        <label>Nama Add-on (Contoh: Extra Keju)</label>
        <input type="text" name="addon_name" id="addonNameInput" class="form-control" required>
      </div>
      <div class="form-group">
        <label>Harga</label>
        <input type="text" name="addon_price" id="addonPriceInput" class="form-control input-rupiah" placeholder="0">
      </div>
      <div class="modal-buttons">
        <button type="button" class="btn-modal-action cancel" onclick="closeModal('addonModal')">Batal</button>
        <button type="submit" name="save_addon" class="btn-modal-action save">Simpan</button>
      </div>
    </form>
  </div>
</div>

<?php if (isset($_SESSION['flash_message'])): ?>
  <script src="../assets/js/toaster.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      showToast("<?= $_SESSION['flash_status'] ?>", "Info", "<?= $_SESSION['flash_message'] ?>");
    });
  </script>
  <?php unset($_SESSION['flash_message']);
  unset($_SESSION['flash_status']); ?>
<?php endif; ?>

<script>
  // 1. COUNTER DESKRIPSI
  const descInput = document.getElementById('menuDescInput');
  const descCounter = document.getElementById('currentDescCount');
  if (descInput && descCounter) {
    descInput.addEventListener('input', function() {
      descCounter.innerText = this.value.length;
    });
  }

  // 2. TAB LOGIC
  function switchTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
    document.getElementById('tab-' + tabName).classList.add('active');

    const btns = document.querySelectorAll('.tab-btn');
    const tabMap = {
      'menu': 0,
      'employees': 1,
      'categories': 2,
      'variants': 3,
      'addons': 4
    };
    if (tabMap[tabName] !== undefined) btns[tabMap[tabName]].classList.add('active');

    const url = new URL(window.location);
    url.searchParams.set('tab', tabName);
    window.history.pushState({}, '', url);
  }

  document.addEventListener("DOMContentLoaded", function() {
    const urlParams = new URLSearchParams(window.location.search);
    const activeTab = urlParams.get('tab') || 'menu';
    switchTab(activeTab);
    initRupiahInputs();
  });

  // 3. MODAL & HELPER FUNCTIONS
  function closeModal(id) {
    document.getElementById(id).classList.remove('show');
  }

  // KATEGORI
  function openCatModal() {
    document.getElementById('catModal').classList.add('show');
    document.getElementById('catModalTitle').innerText = "Kategori Baru";
    document.getElementById('catIdInput').value = "";
    document.getElementById('catNameInput').value = "";
  }

  function editCat(data) {
    openCatModal();
    document.getElementById('catModalTitle').innerText = "Edit Kategori";
    document.getElementById('catIdInput').value = data.id;
    document.getElementById('catNameInput').value = data.name;
  }

  // VARIAN
  function openVarModal() {
    document.getElementById('varModal').classList.add('show');
    document.getElementById('varModalTitle').innerText = "Varian Baru";
    document.getElementById('varIdInput').value = "";
    document.getElementById('varNameInput').value = "";
    document.getElementById('varPriceInput').value = "";
  }

  function editVar(data) {
    openVarModal();
    document.getElementById('varModalTitle').innerText = "Edit Varian";
    document.getElementById('varIdInput').value = data.id;
    document.getElementById('varNameInput').value = data.name;
    let harga = parseInt(data.extra_price).toLocaleString('id-ID');
    document.getElementById('varPriceInput').value = (data.extra_price > 0) ? harga : "";
  }

  // ADDON
  function openAddonModal() {
    document.getElementById('addonModal').classList.add('show');
    document.getElementById('addonModalTitle').innerText = "Add-on Baru";
    document.getElementById('addonIdInput').value = "";
    document.getElementById('addonNameInput').value = "";
    document.getElementById('addonPriceInput').value = "";
  }

  function editAddon(data) {
    openAddonModal();
    document.getElementById('addonModalTitle').innerText = "Edit Add-on";
    document.getElementById('addonIdInput').value = data.id;
    document.getElementById('addonNameInput').value = data.name;
    let harga = parseInt(data.price).toLocaleString('id-ID');
    document.getElementById('addonPriceInput').value = (data.price > 0) ? harga : "";
  }

  // PEGAWAI
  function openEmpModal() {
    document.getElementById('empModal').classList.add('show');
    document.getElementById('empIdInput').value = "";
    document.getElementById('empNameInput').value = "";
    document.getElementById('empModalTitle').innerText = "Pegawai Baru";
  }

  function editEmp(data) {
    openEmpModal();
    document.getElementById('empModalTitle').innerText = "Edit Pegawai";

    document.getElementById('empIdInput').value = data.id;
    document.getElementById('empNameInput').value = data.name;
    document.getElementById('empRoleInput').value = data.role_id;

    // --- LOGIKA AGAR BISA BACA SHIFT ATAU CUTI/IZIN ---
    if (data.shift_id) {
      // Kalau punya Shift ID (Angka), pilih shiftnya
      document.getElementById('empShiftInput').value = data.shift_id;
    } else {
      // Kalau Shift kosong, cek kolom keterangannya
      let ket = data.keterangan;
      if (ket && ket !== '-') {
        // Pilih opsi teks ("Cuti", "Izin", dll)
        document.getElementById('empShiftInput').value = ket;
      } else {
        // Kalau ngga ada apa-apa, reset
        document.getElementById('empShiftInput').value = "";
      }
    }
  }

  // MENU
  function openMenuModal() {
    document.getElementById('menuModal').classList.add('show');
    document.getElementById('menuModalTitle').innerText = "Menu Baru";
    document.getElementById('menuIdInput').value = "";
    document.getElementById('menuNameInput').value = "";
    document.getElementById('menuPriceInput').value = "";
    document.getElementById('menuDescInput').value = "";
    if (document.getElementById('currentDescCount')) document.getElementById('currentDescCount').innerText = "0";
    document.getElementById('previewImg').style.display = 'none';
    document.getElementById('uploadPlaceholder').style.display = 'flex';
  }

  function editMenu(data) {
    openMenuModal();
    document.getElementById('menuModalTitle').innerText = "Edit Menu";
    document.getElementById('menuIdInput').value = data.id;
    document.getElementById('menuNameInput').value = data.name;
    document.getElementById('menuPriceInput').value = parseInt(data.base_price).toLocaleString('id-ID');
    let descText = data.description || "";
    document.getElementById('menuDescInput').value = descText;
    if (document.getElementById('currentDescCount')) document.getElementById('currentDescCount').innerText = descText.length;
    document.getElementById('menuCatInput').value = data.category_id;
    document.getElementById('menuStatusInput').checked = (data.is_available == 1);
    if (data.image) {
      document.getElementById('previewImg').src = "../assets/uploads/" + data.image;
      document.getElementById('previewImg').style.display = 'block';
      document.getElementById('uploadPlaceholder').style.display = 'none';
    }
  }

  function previewFile(input) {
    const file = input.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function(e) {
        document.getElementById('previewImg').src = e.target.result;
        document.getElementById('previewImg').style.display = 'block';
        document.getElementById('uploadPlaceholder').style.display = 'none';
      }
      reader.readAsDataURL(file);
    }
  }

  // 4. FORMAT RUPIAH
  function initRupiahInputs() {
    const inputs = document.querySelectorAll('.input-rupiah, #menuPriceInput');
    inputs.forEach(input => {
      input.addEventListener('keyup', function(e) {
        let value = this.value.replace(/[^,\d]/g, '').toString();
        let split = value.split(',');
        let sisa = split[0].length % 3;
        let rupiah = split[0].substr(0, sisa);
        let ribuan = split[0].substr(sisa).match(/\d{3}/gi);
        if (ribuan) {
          let separator = sisa ? '.' : '';
          rupiah += separator + ribuan.join('.');
        }
        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        this.value = rupiah;
      });
    });
  }
</script>

<?php $content = ob_get_clean();
include 'layouts.php'; ?>