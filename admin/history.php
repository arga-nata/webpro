<?php
// Include Logic (Query, Filter, dll)
include '../config/logic/history_logic.php';

// Mulai Output HTML
ob_start();
?>

<?php $customCSS = '<link rel="stylesheet" href="../assets/css/dashboard/history.css">'; ?>

<div class="header-halaman">
    <h1><i class='bx bx-history'></i> Riwayat Pesanan</h1>
    <p>Pantau semua transaksi masuk, diproses, dan selesai.</p>
</div>

<form id="filterForm" method="GET" class="action-bar-wrapper">
    <input type="hidden" name="limit" value="<?= $limit ?>">
    <input type="hidden" name="page" value="1" id="pageInput">

    <div class="filter-group">
        <i class='bx bx-search' style="color:var(--text-muted)"></i>
        <input type="text" name="search" id="searchInput" class="search-input"
            placeholder="Cari ID / Nama..." value="<?= htmlspecialchars($search) ?>" autocomplete="off">
    </div>

    <div class="filter-group">
        <input type="date" name="start" class="date-input" value="<?= $startDate ?>" onchange="submitFilter()">
        <span style="color:var(--text-muted)">-</span>
        <input type="date" name="end" class="date-input" value="<?= $endDate ?>" onchange="submitFilter()">
    </div>

    <div class="filter-group">
        <i class='bx bx-sort-alt-2' style="color:var(--text-muted)"></i>
        <select name="sort" class="select-sort" onchange="submitFilter()">
            <option value="desc" <?= $sort == 'desc' ? 'selected' : '' ?>>Terbaru</option>
            <option value="asc" <?= $sort == 'asc' ? 'selected' : '' ?>>Terlama</option>
        </select>
    </div>
</form>

<div id="dataContainer">
    <div class="card-table-clean">
        <div class="table-responsive">
            <table class="table-cyber">
                <thead>
                    <tr>
                        <th class="col-left w-id">ID Order</th>
                        <th class="col-left w-date">Waktu</th>
                        <th class="col-left w-user">Pelanggan</th>
                        <th class="col-center w-method">Metode</th>
                        <th class="col-center w-total">Total</th>
                        <th class="col-center w-status">Status</th>
                        <th class="col-center w-action">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <?php
                            $st = strtolower($row['status']);
                            $badgeClass = 'pending';
                            if ($st == 'cooking') $badgeClass = 'cooking';
                            elseif ($st == 'delivery') $badgeClass = 'delivery';
                            elseif ($st == 'completed') $badgeClass = 'completed';
                            elseif ($st == 'cancelled') $badgeClass = 'cancelled';
                            ?>
                            <tr>
                                <td class="col-left w-id">
                                    <span style="font-family:'Consolas'; color:var(--brand-primary); font-weight:600;">
                                        #<?= str_pad($row['id'], 4, '0', STR_PAD_LEFT) ?>
                                    </span>
                                </td>
                                <td class="col-left w-date">
                                    <div style="display:flex; flex-direction:column; line-height:1.4;">
                                        <span style="color:white; font-weight:500;"><?= date('d M Y', strtotime($row['created_at'])) ?></span>
                                        <span style="color:var(--text-muted); font-size:0.8rem;"><?= date('H:i', strtotime($row['created_at'])) ?> WIB</span>
                                    </div>
                                </td>
                                <td class="col-left w-user">
                                    <span style="font-weight:600; color:white;"><?= htmlspecialchars($row['customer_name']) ?></span>
                                </td>
                                <td class="col-center w-method">
                                    <span style="color:var(--text-secondary); font-size:0.85rem; font-weight:600;">
                                        <?= ucfirst($row['payment_method']) ?>
                                    </span>
                                </td>
                                <td class="col-center w-total">
                                    <span style="font-family:'Outfit'; font-weight:700;">
                                        Rp <?= number_format($row['total_amount'], 0, ',', '.') ?>
                                    </span>
                                </td>
                                <td class="col-center w-status">
                                    <span class="status-pill <?= $badgeClass ?>">
                                        <?= ucfirst($st) ?>
                                    </span>
                                </td>
                                <td class="col-center w-action">
                                    <button class="btn-check" onclick='openDetail(<?= json_encode([
                                                                                        "id" => "#" . str_pad($row["id"], 4, "0", STR_PAD_LEFT),
                                                                                        "name" => $row["customer_name"],
                                                                                        "phone" => $row["customer_phone"],
                                                                                        "time" => date("d M Y, H:i", strtotime($row["created_at"])),
                                                                                        "method" => ucfirst($row["payment_method"]),
                                                                                        "items" => $row["item_details"],
                                                                                        "total" => "Rp " . number_format($row["total_amount"], 0, ",", ".")
                                                                                    ]) ?>)'>
                                        <i class='bx bx-show'></i> Check
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align:center; padding: 50px; color:var(--text-muted);">Tidak ada data ditemukan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="pagination-container">
        <div class="pagination">
            <a href="?page=<?= max(1, $page - 1) ?><?= $urlParams ?>" class="page-link <?= ($page <= 1) ? 'disabled' : '' ?>">
                <i class='bx bx-chevron-left'></i>
            </a>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php if ($i == 1 || $i == $totalPages || ($i >= $page - 1 && $i <= $page + 1)): ?>
                    <a href="?page=<?= $i ?><?= $urlParams ?>" class="page-link <?= ($i == $page) ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php elseif ($i == $page - 2 || $i == $page + 2): ?>
                    <span style="color:var(--text-muted); font-size:0.8rem;">...</span>
                <?php endif; ?>
            <?php endfor; ?>

            <a href="?page=<?= min($totalPages, $page + 1) ?><?= $urlParams ?>" class="page-link <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                <i class='bx bx-chevron-right'></i>
            </a>
        </div>
    </div>
</div>

<div id="detailModal" class="modal-overlay-fixed">
    <div class="modal-futuristic">
        <div class="modal-head">
            <h3>Detail Transaksi</h3>
            <div style="display:flex; align-items:center; gap:15px;">
                <span id="d-id">#0000</span>
                <button onclick="closeDetail()" class="btn-close-abs">&times;</button>
            </div>
        </div>

        <div class="info-section">
            <div class="info-row">
                <span class="info-label">Pelanggan</span>
                <span class="info-val" id="d-name">Nama</span>
            </div>
            <div class="info-row">
                <span class="info-label">Kontak / HP</span>
                <span class="info-val" id="d-phone" style="font-family:'Consolas'; color:var(--text-muted);">08xx</span>
            </div>
            <div class="info-row">
                <span class="info-label">Waktu</span>
                <span class="info-val" id="d-time" style="font-family:'Consolas'; color:var(--text-muted);">--:--</span>
            </div>
            <div class="info-row">
                <span class="info-label">Metode Pembayaran</span>
                <span class="info-val" id="d-method" style="font-family:'Consolas'; color:var(--text-muted);">CASH</span>
            </div>
        </div>

        <div class="items-scroll-area" id="d-items"></div>

        <div class="modal-footer">
            <span class="total-label">Total Transaksi</span>
            <span class="total-val" id="d-total">Rp 0</span>
        </div>
    </div>
</div>

<script>
    // 1. LIVE SEARCH LOGIC
    const searchInput = document.getElementById('searchInput');
    let timeout = null;

    searchInput.addEventListener('input', function() {
        clearTimeout(timeout);
        timeout = setTimeout(function() {
            submitFilter();
        }, 400);
    });

    // 2. AJAX SUBMIT FILTER
    function submitFilter() {
        const form = document.getElementById('filterForm');
        const url = new URL(window.location.href);
        const params = new URLSearchParams(new FormData(form));
        window.history.pushState({}, '', '?' + params.toString());

        fetch('history.php?' + params.toString())
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newData = doc.getElementById('dataContainer');
                document.getElementById('dataContainer').innerHTML = newData.innerHTML;
            })
            .catch(err => console.error('Error fetching data:', err));
    }

    // 3. MODAL LOGIC
    function openDetail(data) {
        document.getElementById('detailModal').classList.add('show');
        document.getElementById('d-id').innerText = data.id;
        document.getElementById('d-name').innerText = data.name;
        document.getElementById('d-phone').innerText = data.phone || '-';
        document.getElementById('d-time').innerText = data.time;
        document.getElementById('d-method').innerText = data.method;
        document.getElementById('d-total').innerText = data.total;

        const list = document.getElementById('d-items');
        list.innerHTML = "";

        if (data.items) {
            let items = data.items.split('||');
            items.forEach(itemStr => {
                let parts = itemStr.split('|');
                let priceFmt = new Intl.NumberFormat('id-ID').format(parts[2]);
                let subtotalFmt = new Intl.NumberFormat('id-ID').format(parts[3]);
                let row = `
                    <div class="item-row">
                        <div>
                            <span class="item-name">${parts[0]}</span>
                            <span class="item-qty">x${parts[1]}</span>
                        </div>
                        <span class="item-price">Rp ${subtotalFmt}</span>
                    </div>
                `;
                list.innerHTML += row;
            });
        } else {
            list.innerHTML = '<div style="text-align:center; color:var(--text-muted); font-size:0.8rem;">Item kosong</div>';
        }
    }

    function closeDetail() {
        document.getElementById('detailModal').classList.remove('show');
    }
    window.onclick = function(e) {
        if (e.target == document.getElementById('detailModal')) closeDetail();
    }
</script>

<?php
$content = ob_get_clean();
include 'layouts.php';
?>