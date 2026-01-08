const toggleBtn = document.getElementById('theme-toggle');
const themeIcon = document.getElementById('theme-icon');
const htmlTag = document.documentElement;
const menuToggle = document.getElementById('menu-toggle');
const navMenu = document.getElementById('nav-menu');

// 1. Update Ikon Tema Dinamis
function updateThemeIcon(theme) {
  if (themeIcon) {
    themeIcon.className = theme === 'dark' ? 'bx bx-sun' : 'bx bx-moon';
  }
}

// 2. Klik Toggle Tema
if (toggleBtn) {
  toggleBtn.addEventListener('click', () => {
    const currentTheme = htmlTag.getAttribute('data-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    htmlTag.setAttribute('data-theme', newTheme);
    localStorage.setItem('selected-theme', newTheme);
    updateThemeIcon(newTheme);
  });
}

// 3. Dropdown Menu Mobile + Lock Scroll
if (menuToggle && navMenu) {
  menuToggle.addEventListener('click', () => {
    navMenu.classList.toggle('active');
    // KUNCI SCROLL: Halaman belakang tidak akan bergeser saat menu buka
    document.body.classList.toggle('no-scroll'); 
    menuToggle.innerText = navMenu.classList.contains('active') ? '✕' : '☰';
  });

  // Tutup menu otomatis jika link diklik
  document.querySelectorAll('.nav-links a').forEach(link => {
    link.addEventListener('click', () => {
      navMenu.classList.remove('active');
      document.body.classList.remove('no-scroll');
      menuToggle.innerText = '☰';
    });
  });
}

// Cek Tema Saat Awal Load
const savedTheme = localStorage.getItem('selected-theme') || 'dark';
htmlTag.setAttribute('data-theme', savedTheme);
updateThemeIcon(savedTheme);