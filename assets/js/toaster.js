// Fungsi Utama: showToast('success', 'Login Berhasil', 'Selamat datang kembali!')
function showToast(type, title, message) {
    // 1. Buat Container jika belum ada
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        document.body.appendChild(container);
    }

    // 2. Tentukan Icon berdasarkan tipe
    let iconClass = '';
    if (type === 'success') iconClass = 'bx-check-circle';
    else if (type === 'error') iconClass = 'bx-x-circle';
    else iconClass = 'bx-info-circle';

    // 3. Buat Elemen Toast
    const toast = document.createElement('div');
    toast.className = `toast-box toast-${type}`;
    
    toast.innerHTML = `
        <i class='bx ${iconClass}'></i>
        <div class="toast-content">
            <span class="toast-title">${title}</span>
            <span class="toast-desc">${message}</span>
        </div>
    `;

    // 4. Masukkan ke container
    container.appendChild(toast);

    // 5. Hapus otomatis setelah 4 detik (4000ms)
    setTimeout(() => {
        toast.style.animation = 'slideOut 0.5s ease forwards';
        // Hapus dari DOM setelah animasi slideOut selesai
        setTimeout(() => {
            toast.remove();
        }, 500); 
    }, 4000);
}