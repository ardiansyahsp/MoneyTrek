<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: ../dashboard/dashboard.php");
    exit();
}
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'login';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login & Daftar — MoneyTracker</title>
  <link rel="stylesheet" href="../style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<section class="login-page">

  <!-- LEFT PANEL -->
  <div class="login-left">
    <div class="login-brand">
      <div class="login-brand-logo">
        <div class="lw"><i class="fas fa-wallet"></i></div>
        <span>MoneyTracker</span>
      </div>
      <h1>Kontrol penuh atas <span class="green-word">keuanganmu.</span></h1>
      <p>Catat setiap transaksi, analisis pengeluaran, dan wujudkan tujuan finansialmu bersama MoneyTracker.</p>
    </div>
    <div class="login-features">
      <div class="lf-item">
        <i class="fas fa-chart-pie"></i>
        Visualisasi pengeluaran dengan grafik interaktif
      </div>
      <div class="lf-item">
        <i class="fas fa-bolt"></i>
        Catat transaksi dalam hitungan detik
      </div>
      <div class="lf-item">
        <i class="fas fa-shield-halved"></i>
        Data tersimpan aman di database
      </div>
      <div class="lf-item">
        <i class="fas fa-star"></i>
        100% gratis, selamanya
      </div>
    </div>
  </div>

  <!-- RIGHT PANEL -->
  <div class="login-right">

    <!-- TAB SWITCHER -->
    <div class="auth-tabs">
      <button class="auth-tab <?php echo $tab=='login'?'active':''; ?>" id="tabLogin" onclick="switchTab('login')">Masuk</button>
      <button class="auth-tab <?php echo $tab=='register'?'active':''; ?>" id="tabRegister" onclick="switchTab('register')">Daftar</button>
      <div class="auth-tab-slider" id="tabSlider" style="transform: <?php echo $tab=='login'?'translateX(0)':'translateX(100%)'; ?>"></div>
    </div>

    <!-- ERROR/SUCCESS MESSAGES -->
    <?php if(isset($_SESSION['error'])): ?>
        <div style="padding: 12px; background: var(--red-soft); color: var(--red); border-radius: var(--radius-sm); margin-bottom: 20px; font-size: 0.875rem; border: 1px solid rgba(244,63,94,0.2);">
            <i class="fas fa-circle-exclamation"></i> <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    <?php if(isset($_SESSION['success'])): ?>
        <div style="padding: 12px; background: var(--emerald-soft); color: var(--emerald); border-radius: var(--radius-sm); margin-bottom: 20px; font-size: 0.875rem; border: 1px solid rgba(5,150,105,0.2);">
            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <!-- ═══════ LOGIN FORM ═══════ -->
    <div id="panelLogin" style="display: <?php echo $tab=='login'?'block':'none'; ?>">
      <div class="auth-header">
        <h2>Selamat datang 👋</h2>
        <p>Masuk ke akun MoneyTracker kamu</p>
      </div>

      <form action="proses_login.php" method="POST">
        <div class="form-group">
          <label for="loginEmail">Alamat Email</label>
          <div class="input-wrap">
            <i class="fas fa-envelope input-icon"></i>
            <input type="email" name="email" id="loginEmail" placeholder="nama@email.com" required autocomplete="email">
          </div>
        </div>

        <div class="form-group">
          <label for="loginPassword">Password</label>
          <div class="input-wrap">
            <i class="fas fa-lock input-icon"></i>
            <input type="password" name="password" id="loginPassword" placeholder="Masukkan password" required autocomplete="current-password">
            <button type="button" class="toggle-pw" onclick="togglePw('loginPassword', this)" tabindex="-1">
              <i class="fas fa-eye"></i>
            </button>
          </div>
        </div>

        <button type="submit" class="login-btn">
          <i class="fas fa-right-to-bracket"></i> Masuk ke Dashboard
        </button>
      </form>

      <p class="auth-switch-text">Belum punya akun? <a href="#" onclick="switchTab('register')">Daftar sekarang</a></p>
    </div>

    <!-- ═══════ REGISTER FORM ═══════ -->
    <div id="panelRegister" style="display: <?php echo $tab=='register'?'block':'none'; ?>">
      <div class="auth-header">
        <h2>Buat akun baru ✨</h2>
        <p>Mulai perjalanan finansialmu hari ini</p>
      </div>

      <form action="proses_register.php" method="POST">
        <div class="form-group">
          <label for="regName">Nama Lengkap</label>
          <div class="input-wrap">
            <i class="fas fa-user input-icon"></i>
            <input type="text" name="name" id="regName" placeholder="Nama lengkap kamu" required autocomplete="name">
          </div>
        </div>

        <div class="form-group">
          <label for="regEmail">Alamat Email</label>
          <div class="input-wrap">
            <i class="fas fa-envelope input-icon"></i>
            <input type="email" name="email" id="regEmail" placeholder="nama@email.com" required autocomplete="email">
          </div>
        </div>

        <div class="form-group">
          <label for="regPhone">Nomor Telepon</label>
          <div class="input-wrap">
            <i class="fas fa-phone input-icon"></i>
            <input type="tel" name="phone" id="regPhone" placeholder="08xxxxxxxxxx" required autocomplete="tel">
          </div>
        </div>

        <div class="form-group">
          <label for="regPassword">Password</label>
          <div class="input-wrap">
            <i class="fas fa-lock input-icon"></i>
            <input type="password" name="password" id="regPassword" placeholder="Minimal 8 karakter" required autocomplete="new-password">
            <button type="button" class="toggle-pw" onclick="togglePw('regPassword', this)" tabindex="-1">
              <i class="fas fa-eye"></i>
            </button>
          </div>
        </div>

        <div class="form-group">
          <label for="regConfirm">Konfirmasi Password</label>
          <div class="input-wrap">
            <i class="fas fa-lock input-icon"></i>
            <input type="password" name="confirm_password" id="regConfirm" placeholder="Ulangi password" required autocomplete="new-password">
            <button type="button" class="toggle-pw" onclick="togglePw('regConfirm', this)" tabindex="-1">
              <i class="fas fa-eye"></i>
            </button>
          </div>
        </div>

        <button type="submit" class="login-btn" style="margin-top:14px">
          <i class="fas fa-user-plus"></i> Buat Akun Sekarang
        </button>
      </form>

      <p class="auth-switch-text">Sudah punya akun? <a href="#" onclick="switchTab('login')">Masuk di sini</a></p>
    </div>

  </div>
</section>

<script>
  /* ── TAB SWITCHER ── */
  function switchTab(tab) {
    const isLogin = tab === 'login';
    document.getElementById('panelLogin').style.display    = isLogin ? 'block' : 'none';
    document.getElementById('panelRegister').style.display = isLogin ? 'none'  : 'block';
    document.getElementById('tabLogin').classList.toggle('active', isLogin);
    document.getElementById('tabRegister').classList.toggle('active', !isLogin);
    document.getElementById('tabSlider').style.transform = isLogin ? 'translateX(0)' : 'translateX(100%)';
    // Update URL without reload to persist tab on refresh
    const url = new URL(window.location);
    url.searchParams.set('tab', tab);
    window.history.pushState({}, '', url);
  }

  /* ── SHOW/HIDE PASSWORD ── */
  function togglePw(id, btn) {
    const inp = document.getElementById(id);
    const icon = btn.querySelector('i');
    if (inp.type === 'password') {
      inp.type = 'text';
      icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
      inp.type = 'password';
      icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
  }
</script>
</body>
</html>
