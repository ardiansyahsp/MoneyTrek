<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard/dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>MoneyTracker — Kelola Keuangan dengan Cerdas</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<header>
  <nav class="navbar">
    <a href="#home" class="logo">
      <div class="logo-wrap"><i class="fas fa-wallet"></i></div>
      MoneyTracker
    </a>
    <ul class="nav-links">
      <li><a href="#home">Beranda</a></li>
      <li><a href="#about">Tentang</a></li>
      <li><a href="#team">Tim</a></li>
    </ul>
    <a href="auth/login.php" class="btn-cta-nav">Mulai Gratis →</a>
  </nav>
</header>

<main>

  <!-- HERO -->
  <section id="home" class="hero">
    <div class="hero-container">

      <div class="hero-text">
        <span class="badge">Platform Keuangan #1</span>
        <h1>Pantau & Kelola Keuanganmu dengan <span>Lebih Cerdas</span></h1>
        <p>MoneyTracker membantu kamu mencatat pemasukan, mengelola pengeluaran, dan mencapai kebebasan finansial — semuanya dalam satu platform.</p>
        <div class="hero-actions">
          <button class="btn-green" onclick="window.location.href='auth/login.php'">
            Mulai Gratis <i class="fas fa-arrow-right"></i>
          </button>
          <button class="btn-white" onclick="document.querySelector('#about').scrollIntoView({behavior:'smooth'})">
            Pelajari Lebih <i class="fas fa-chevron-down"></i>
          </button>
        </div>
      </div>

      <div class="hero-mockup">
        <div class="phone-frame">
          <img src="p.png" alt="MoneyTracker Dashboard Preview">
        </div>
      </div>

      <div class="hero-features">
        <div class="feat-item">
          <div class="feat-icon"><i class="fas fa-chart-line"></i></div>
          <div>
            <h4>Pantau Real-Time</h4>
            <p>Lihat ringkasan keuangan secara langsung dan akurat setiap saat.</p>
          </div>
        </div>
        <div class="feat-item">
          <div class="feat-icon"><i class="fas fa-pen-to-square"></i></div>
          <div>
            <h4>Catat Transaksi</h4>
            <p>Tambah pemasukan dan pengeluaran hanya dalam beberapa detik.</p>
          </div>
        </div>
        <div class="feat-item">
          <div class="feat-icon"><i class="fas fa-bullseye"></i></div>
          <div>
            <h4>Capai Tujuan</h4>
            <p>Tetapkan target finansial dan pantau progres tabunganmu.</p>
          </div>
        </div>
      </div>

    </div>
  </section>

  <!-- STATS BAR -->
  <div class="stats-bar">
    <div class="stats-inner">
      <div class="stat-item">
        <div class="stat-num">10K+</div>
        <div class="stat-label">Pengguna Aktif</div>
      </div>
      <div class="stat-item">
        <div class="stat-num">98%</div>
        <div class="stat-label">Kepuasan Pengguna</div>
      </div>
      <div class="stat-item">
        <div class="stat-num">500M+</div>
        <div class="stat-label">Transaksi Dicatat</div>
      </div>
      <div class="stat-item">
        <div class="stat-num">100%</div>
        <div class="stat-label">Gratis Selamanya</div>
      </div>
    </div>
  </div>

  <!-- ABOUT -->
  <section id="about" class="about">
    <div class="section-tag">Tentang Platform</div>
    <h2>Kenapa <span>MoneyTracker?</span></h2>
    <p class="section-sub">Kami percaya bahwa mengelola keuangan tidak harus rumit. Platform kami dirancang agar siapa pun bisa mengontrol keuangannya dengan mudah.</p>
    <div class="about-grid">
      <div class="about-box">
        <div class="box-icon"><i class="fas fa-info-circle"></i></div>
        <h3>Apa itu MoneyTracker?</h3>
        <p>MoneyTracker adalah platform manajemen keuangan pribadi yang memudahkan kamu mencatat, memantau, dan menganalisis arus kas harian secara efisien.</p>
      </div>
      <div class="about-box">
        <div class="box-icon"><i class="fas fa-bullseye"></i></div>
        <h3>Tujuan Kami</h3>
        <p>Membantu jutaan orang memahami pola pengeluaran mereka, membangun kebiasaan menabung yang konsisten, dan mencapai kebebasan finansial jangka panjang.</p>
      </div>
      <div class="about-box">
        <div class="box-icon"><i class="fas fa-seedling"></i></div>
        <h3>Manfaat Nyata</h3>
        <p>Dengan visualisasi data yang jelas, kamu bisa membuat keputusan keuangan yang lebih cerdas — mulai dari penghematan harian hingga perencanaan masa depan.</p>
      </div>
    </div>
  </section>

  <!-- TEAM -->
  <section id="team" class="team">
    <div class="section-tag">Orang di Balik Layar</div>
    <h2>Tim <span>Kami</span></h2>
    <p class="section-sub">Empat mahasiswa berdedikasi yang membangun MoneyTracker dengan penuh semangat untuk membantu sesama.</p>
    <div class="team-grid">
      <div class="team-card">
        <div class="team-avatar"><i class="fas fa-user"></i></div>
        <h3>Meyla Kusuma Firdaus</h3>
        <div class="nim">13182420142</div>
        <span class="tag">Frontend Developer</span>
      </div>
      <div class="team-card">
        <div class="team-avatar"><i class="fas fa-user"></i></div>
        <h3>Ivan Hazim Al-Aziz</h3>
        <div class="nim">13182420150</div>
        <span class="tag orange">UI/UX Designer</span>
      </div>
      <div class="team-card">
        <div class="team-avatar"><i class="fas fa-user"></i></div>
        <h3>Ardiansyah Sisworo P</h3>
        <div class="nim">13182420152</div>
        <span class="tag blue">Backend Developer</span>
      </div>
      <div class="team-card">
        <div class="team-avatar"><i class="fas fa-user"></i></div>
        <h3>Aang Abdul Rahman</h3>
        <div class="nim">13182420181</div>
        <span class="tag red">Backend Developer</span>
      </div>
    </div>
  </section>

</main>

<footer>
  <div class="footer-wrap">
    <div class="footer-logo">
      <div class="logo-dot"></div>
      <span><i class="fas fa-wallet" style="color:#34d399;margin-right:8px"></i>MoneyTracker</span>
    </div>
    <p>&copy; 2026 MoneyTracker. All rights reserved.</p>
    <div class="footer-social">
      <i class="fab fa-instagram"></i>
      <i class="fab fa-github"></i>
      <i class="fas fa-envelope"></i>
    </div>
  </div>
</footer>

<script src="script.js"></script>
</body>
</html>
