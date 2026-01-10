<?php
session_start();
include '../config/db.php';

$pageTitle = "Manajemen Data";
$currentPage = "manage";

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
  header("Location: login.php");
  exit;
}
$adminName = isset($_SESSION['admin_user']) ? ucfirst($_SESSION['admin_user']) : "Admin";

// QUERY DATA
$q_menu = mysqli_query($conn, "SELECT m.*, c.name as cat_name FROM menu_items m LEFT JOIN categories c ON m.category_id = c.id ORDER BY m.id DESC");
$q_cat = mysqli_query($conn, "SELECT * FROM categories ORDER BY id ASC");
$categories = [];
while ($c = mysqli_fetch_assoc($q_cat)) {
  $categories[] = $c;
}
mysqli_data_seek($q_cat, 0);
$q_var = mysqli_query($conn, "SELECT * FROM variants ORDER BY id ASC");
$q_add = mysqli_query($conn, "SELECT * FROM addons ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $pageTitle ?> - Street Sushi</title>

  <link rel="shortcut icon" href="../assets/images/favicon.ico" type="image/x-icon">
  <link rel="stylesheet" href="../assets/css/dashboard/page.css">
  <link rel="stylesheet" href="../assets/css/dashboard/manage.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>

  <div class="wrapper">
    <?php include '../assets/components/sidebar.php'; ?>

    <div class="main-content">
      <?php include '../assets/components/topbar.php'; ?>

      <div class="content-padding">

        <div class="header-halaman">
          <h1>Data Center</h1>
          <p style="color:var(--text-muted); font-size:0.9rem;">Kelola semua aset menu dan varian di sini.</p>
        </div>

        <div class="tab-navigation">
          <button class="tab-btn active" onclick="switchTab(event, 'tab-menu')">Menu Item</button>
          <button class="tab-btn" onclick="switchTab(event, 'tab-kategori')">Category</button>
          <button class="tab-btn" onclick="switchTab(event, 'tab-varian')">Variant</button>
          <button class="tab-btn" onclick="switchTab(event, 'tab-addon')">Add-ons</button>
        </div>

        <div id="tab-menu" class="tab-content active">
          <div class="action-bar">
            <button class="btn-ghost-border" onclick="openModal('modalMenu')">
              <i class='bx bx-plus'></i> New Menu
            </button>
          </div>

          <div class="card-table">
            <table>
              <thead>
                <tr>
                  <th width="80">Img</th>
                  <th>Menu Name</th>
                  <th>Category</th>
                  <th>Price</th>
                  <th class="text-right">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($m = mysqli_fetch_assoc($q_menu)):
                  $img_src = (!empty($m['image']) && file_exists("../assets/uploads/" . $m['image']))
                    ? "../assets/uploads/" . $m['image']
                    : "../assets/images/menu/" . (!empty($m['image']) ? $m['image'] : 'default.jpg');
                ?>
                  <tr>
                    <td><img src="<?= $img_src ?>" class="thumb-menu" alt="img"></td>
                    <td>
                      <strong style="color:var(--text-main); font-size:0.95rem;"><?= $m['name'] ?></strong>
                      <div style="color:var(--text-muted); font-size:0.8rem; margin-top:2px;"><?= substr($m['description'], 0, 35) ?>...</div>
                    </td>
                    <td><span class="badge-pill"><?= $m['cat_name'] ?></span></td>
                    <td style="font-weight:600; font-size:0.95rem; letter-spacing:0.5px; color:var(--success);">
                      Rp <?= number_format($m['base_price'], 0, ',', '.') ?>
                    </td>
                    <td class="text-right">
                      <button class="btn-icon edit" onclick='editMenu(<?= json_encode($m) ?>)'><i class='bx bx-pencil'></i></button>
                      <a href="../config/logic/manage_action.php?act=delete_menu&id=<?= $m['id'] ?>" class="btn-icon delete" onclick="return confirm('Hapus menu ini?')"><i class='bx bx-trash'></i></a>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>

        <div id="tab-kategori" class="tab-content">
          <div class="action-bar">
            <button class="btn-ghost-border" onclick="openModal('modalKategori')">
              <i class='bx bx-layer-plus'></i> New Category
            </button>
          </div>
          <div class="card-table compact">
            <table>
              <thead>
                <tr>
                  <th>Category Name</th>
                  <th class="text-right">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($c = mysqli_fetch_assoc($q_cat)): ?>
                  <tr>
                    <td style="font-weight:600;"><?= $c['name'] ?></td>
                    <td class="text-right">
                      <a href="../config/logic/manage_action.php?act=delete_cat&id=<?= $c['id'] ?>" class="btn-icon delete" onclick="return confirm('Hapus kategori? Semua menu di dalamnya akan ikut terhapus!')"><i class='bx bx-trash'></i></a>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>

        <div id="tab-varian" class="tab-content">
          <div class="action-bar">
            <button class="btn-ghost-border" onclick="openModal('modalVarian')">
              <i class='bx bx-purchase-tag-alt'></i> New Variant
            </button>
          </div>
          <div class="card-table compact">
            <table>
              <thead>
                <tr>
                  <th>Variant Name</th>
                  <th>Extra Price</th>
                  <th class="text-right">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($v = mysqli_fetch_assoc($q_var)): ?>
                  <tr>
                    <td><?= $v['name'] ?></td>
                    <td style="color:var(--success);">+ <?= number_format($v['extra_price'], 0, ',', '.') ?></td>
                    <td class="text-right"><a href="../config/logic/manage_action.php?act=delete_var&id=<?= $v['id'] ?>" class="btn-icon delete" onclick="return confirm('Hapus?')"><i class='bx bx-trash'></i></a></td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>

        <div id="tab-addon" class="tab-content">
          <div class="action-bar">
            <button class="btn-ghost-border" onclick="openModal('modalAddon')">
              <i class='bx bx-list-plus'></i> New Add-on
            </button>
          </div>
          <div class="card-table compact">
            <table>
              <thead>
                <tr>
                  <th>Addon Name</th>
                  <th>Price</th>
                  <th class="text-right">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($a = mysqli_fetch_assoc($q_add)): ?>
                  <tr>
                    <td><?= $a['name'] ?></td>
                    <td style="color:var(--success);">+ <?= number_format($a['price'], 0, ',', '.') ?></td>
                    <td class="text-right"><a href="../config/logic/manage_action.php?act=delete_add&id=<?= $a['id'] ?>" class="btn-icon delete" onclick="return confirm('Hapus?')"><i class='bx bx-trash'></i></a></td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>
  </div>

  <div id="modalMenu" class="modal">
    <div class="modal-content large">
      <div class="modal-header">
        <h3 id="modalTitle">Create Menu</h3>
        <button class="close-modal" onclick="closeModal('modalMenu')">&times;</button>
      </div>
      <form action="../config/logic/manage_action.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="act" value="save_menu">
        <input type="hidden" name="id" id="menuId">

        <div class="modal-grid">
          <div class="left-side">
            <div class="form-group">
              <label>Foto Menu</label>
              <div class="image-upload-wrapper" onclick="document.getElementById('fileInput').click()">
                <img id="previewImg" class="image-preview" src="#" alt="Preview">
                <div class="upload-placeholder" id="uploadPlaceholder">
                  <i class='bx bx-image-add'></i>
                  <span>Upload</span>
                </div>
              </div>
              <input type="file" name="image" id="fileInput" accept="image/*" onchange="previewFile(this)">
            </div>
            <div class="form-group">
              <label>Harga (IDR)</label>
              <input type="text" id="menuPriceDisplay" class="form-control" placeholder="0" onkeyup="formatRupiah(this)" required>
              <input type="hidden" name="base_price" id="menuPriceRaw">
            </div>
          </div>
          <div class="right-side">
            <div class="form-group">
              <label>Nama Menu</label>
              <input type="text" name="name" id="menuName" class="form-control" placeholder="Nama makanan..." required>
            </div>
            <div class="form-group">
              <label>Kategori</label>
              <div class="select-wrapper">
                <select name="category_id" id="menuCat" class="form-control" required>
                  <option value="" disabled selected>Pilih Kategori</option>
                  <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>"><?= $cat['name'] ?></option>
                  <?php endforeach; ?>
                </select>
                <i class='bx bx-chevron-down arrow-icon'></i>
              </div>
            </div>
            <div class="form-group" style="flex-grow:1;">
              <label>Deskripsi</label>
              <textarea name="description" id="menuDesc" class="form-control" placeholder="Deskripsi singkat..." style="height:100px;"></textarea>
            </div>
            <button type="submit" class="btn-save">SIMPAN DATA</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div id="modalKategori" class="modal">
    <div class="modal-content small">
      <div class="modal-header">
        <h3>New Category</h3><button class="close-modal" onclick="closeModal('modalKategori')">&times;</button>
      </div>
      <form action="../config/logic/manage_action.php" method="POST">
        <input type="hidden" name="act" value="save_cat">
        <div class="form-group"><label>Nama Kategori</label><input type="text" name="name" class="form-control" required></div>
        <button type="submit" class="btn-save">Simpan</button>
      </form>
    </div>
  </div>

  <div id="modalVarian" class="modal">
    <div class="modal-content small">
      <div class="modal-header">
        <h3>New Variant</h3><button class="close-modal" onclick="closeModal('modalVarian')">&times;</button>
      </div>
      <form action="../config/logic/manage_action.php" method="POST">
        <input type="hidden" name="act" value="save_var">
        <div class="form-group"><label>Nama Varian</label><input type="text" name="name" class="form-control" required></div>
        <div class="form-group"><label>Harga Tambahan</label><input type="number" name="extra_price" class="form-control" value="0"></div>
        <button type="submit" class="btn-save">Simpan</button>
      </form>
    </div>
  </div>

  <div id="modalAddon" class="modal">
    <div class="modal-content small">
      <div class="modal-header">
        <h3>New Add-on</h3><button class="close-modal" onclick="closeModal('modalAddon')">&times;</button>
      </div>
      <form action="../config/logic/manage_action.php" method="POST">
        <input type="hidden" name="act" value="save_add">
        <div class="form-group"><label>Nama Addon</label><input type="text" name="name" class="form-control" required></div>
        <div class="form-group"><label>Harga</label><input type="number" name="price" class="form-control" value="0"></div>
        <button type="submit" class="btn-save">Simpan</button>
      </form>
    </div>
  </div>

  <div class="mobile-overlay"></div>

  <script>
    function switchTab(evt, tabName) {
      document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
      document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      document.getElementById(tabName).classList.add('active');
      evt.currentTarget.classList.add('active');
    }

    function openModal(id) {
      document.getElementById(id).classList.add('show');
      if (id === 'modalMenu') {
        document.getElementById('modalTitle').innerText = "Create Menu";
        document.getElementById('menuId').value = "";
        document.getElementById('menuName').value = "";
        document.getElementById('menuCat').selectedIndex = 0; // Reset Select
        document.getElementById('menuPriceDisplay').value = "";
        document.getElementById('menuDesc').value = "";
        document.getElementById('previewImg').style.display = 'none';
        document.getElementById('uploadPlaceholder').style.display = 'flex';
      }
    }

    function closeModal(id) {
      document.getElementById(id).classList.remove('show');
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

    function editMenu(data) {
      openModal('modalMenu');
      document.getElementById('modalTitle').innerText = "Edit Menu";
      document.getElementById('menuId').value = data.id;
      document.getElementById('menuName').value = data.name;
      document.getElementById('menuCat').value = data.category_id;
      document.getElementById('menuDesc').value = data.description;

      const price = parseInt(data.base_price);
      document.getElementById('menuPriceDisplay').value = price.toLocaleString('id-ID');
      document.getElementById('menuPriceRaw').value = price;

      if (data.image) {
        let src = (data.image.includes('/')) ? data.image : "../assets/uploads/" + data.image;
        // Fallback simpel, kalau di backend tidak ketemu dia akan pakai default
        document.getElementById('previewImg').src = src;
        document.getElementById('previewImg').style.display = 'block';
        document.getElementById('uploadPlaceholder').style.display = 'none';
      }
    }

    function formatRupiah(input) {
      let value = input.value.replace(/[^0-9]/g, '');
      document.getElementById('menuPriceRaw').value = value;
      if (value) value = parseInt(value).toLocaleString('id-ID');
      input.value = value;
    }

    // Sidebar Mobile
    const sidebar = document.querySelector('.sidebar');
    const toggleBtn = document.querySelector('.toggle-btn');
    const overlay = document.querySelector('.mobile-overlay');
    if (overlay) {
      toggleBtn.addEventListener('click', () => {
        if (window.innerWidth > 768) sidebar.classList.toggle('close');
        else {
          sidebar.classList.toggle('active');
          overlay.classList.toggle('active');
        }
      });
      overlay.addEventListener('click', () => {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
      });
    }
  </script>

</body>

</html>