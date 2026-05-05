// Fungsi interaktif saat tombol navigasi diklik
function showWelcome() {
    alert("Selamat Datang di MoneyTracker! Mari mulai kelola keuangan Anda dengan lebih cerdas.");
}

// Efek Scroll Halus (Smooth Scroll) untuk navigasi
document.querySelectorAll('.nav-links a').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const targetId = this.getAttribute('href');
        document.querySelector(targetId).scrollIntoView({
            behavior: 'smooth'
        });
    });
});

// Fitur interaktif tambahan: Animasi sederhana saat scroll
window.addEventListener('scroll', () => {
    const header = document.querySelector('header');
    if (window.scrollY > 50) {
        header.style.padding = '10px 8%';
    } else {
        header.style.padding = '15px 8%';
    }
});