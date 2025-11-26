// js/script.js (Tambahkan fungsi kontrol UI ini)

const authModal = document.getElementById('auth-modal');
const loginContainer = document.getElementById('login-container');
const registerContainer = document.getElementById('register-container');
const loginNavBtn = document.getElementById('login-nav-btn');

// Fungsi untuk membuka modal
loginNavBtn.addEventListener('click', () => {
    authModal.style.display = 'flex';
    toggleAuthForm(true); // Default tampilkan login
});

// Fungsi untuk menutup modal
function closeAuthModal() {
    authModal.style.display = 'none';
}

// Fungsi untuk beralih antara Login dan Register
function toggleAuthForm(showLogin) {
    if (showLogin) {
        loginContainer.style.display = 'block';
        registerContainer.style.display = 'none';
    } else {
        loginContainer.style.display = 'none';
        registerContainer.style.display = 'block';
    }
}

// Fungsi autohide header
let lastScrollTop = 0;
const header = document.getElementById('main-header');
const scrollThreshold = 50; // Jarak scroll minimal sebelum header disembunyikan

window.addEventListener('scroll', function () {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

    if (scrollTop > lastScrollTop && scrollTop > scrollThreshold) {
        // Scroll ke bawah dan melebihi threshold - sembunyikan header
        header.classList.add('header-hidden');
    } else {
        // Scroll ke atas - tampilkan header
        header.classList.remove('header-hidden');
    }

    lastScrollTop = scrollTop;
});


// Panggil fungsi submit form (dari jawaban sebelumnya)
// ... tambahkan kode submit register/login form dari langkah sebelumnya di sini ...