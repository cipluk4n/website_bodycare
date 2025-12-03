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

document.addEventListener('DOMContentLoaded', function() {
    // Cari semua tautan yang memiliki anchor (href dimulai dengan #)
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            
            // 1. Matikan perilaku default (melompat instan)
            e.preventDefault();

            // 2. Ambil ID elemen tujuan dari atribut href
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);

            if (targetElement) {
                // 3. Gulir ke elemen tersebut dengan animasi 'smooth'
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start' // Menggulir elemen ke bagian atas layar
                });
            }
        });
    });
});