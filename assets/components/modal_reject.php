<style>
    /* Modal Overlay Gelap */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.8);
        backdrop-filter: blur(5px);
        display: none;
        /* Hidden by default */
        align-items: center;
        justify-content: center;
        z-index: 1000;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .modal-overlay.show {
        display: flex;
        opacity: 1;
    }

    /* Modal Box Futuristik */
    .futuristic-modal {
        background: var(--bg-dark-secondary);
        border: 1px solid var(--neon-red);
        box-shadow: 0 0 30px rgba(255, 46, 99, 0.2);
        padding: 30px;
        border-radius: 20px;
        width: 400px;
        transform: scale(0.8);
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .modal-overlay.show .futuristic-modal {
        transform: scale(1);
    }

    .modal-header h3 {
        margin: 0;
        color: var(--neon-red);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .modal-body {
        margin: 20px 0;
    }

    .modal-body textarea {
        width: 100%;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--glass-border);
        color: white;
        padding: 15px;
        border-radius: 10px;
        resize: none;
        outline: none;
        font-family: 'Inter';
    }

    .modal-body textarea:focus {
        border-color: var(--neon-red);
        box-shadow: 0 0 10px rgba(255, 46, 99, 0.2);
    }

    .modal-footer {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
    }

    .btn-cancel {
        background: transparent;
        color: var(--text-secondary);
        border: 1px solid var(--glass-border);
    }
</style>

<div class="modal-overlay" id="rejectModal">
    <div class="futuristic-modal">
        <form action="" method="POST">
            <div class="modal-header">
                <h3><i class='bx bxs-error-circle'></i> TOLAK PESANAN</h3>
            </div>
            <div class="modal-body">
                <input type="hidden" name="action" value="reject">
                <input type="hidden" name="order_id" id="input_order_id">
                <p style="margin-bottom: 10px;">Beritahu pelanggan alasan penolakan:</p>
                <textarea name="alasan_tolak" rows="4" placeholder="Contoh: Maaf, bahan baku habis..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-action btn-cancel" onclick="closeModal()">Batal</button>
                <button type="submit" class="btn-action btn-tolak">Konfirmasi Tolak</button>
            </div>
        </form>
    </div>
</div>

<script>
    let currentOrderId = null;

    function openRejectModal(orderId) {
        // 1. Masukkan ID ke input hidden di form
        document.getElementById('input_order_id').value = orderId;

        // 2. Tampilkan modal seperti biasa
        const modal = document.getElementById('rejectModal');
        modal.style.display = 'flex';
        setTimeout(() => modal.classList.add('show'), 10);
    }

    function closeModal() {
        const modal = document.getElementById('rejectModal');
        modal.classList.remove('show');
        setTimeout(() => modal.style.display = 'none', 300);
        currentOrderId = null;
    }

    function confirmReject() {
        alert("Pesanan ID " + currentOrderId + " ditolak! (Implementasikan logika PHP di sini)");
        closeModal();
    }

    // Tutup modal kalau klik di luar box
    window.onclick = function (event) {
        const modal = document.getElementById('rejectModal');
        if (event.target == modal) { closeModal(); }
    }
</script>