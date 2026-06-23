<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar">
    <div class="sidebar-top">
        <div class="sidebar-logo"><i class="fas fa-wallet"></i></div>
        <div class="nav-menu">
            <a href="dashboard.php" class="nav-item <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>" title="Dashboard"><i class="fas fa-home"></i></a>
            <a href="history.php" class="nav-item <?php echo ($current_page == 'history.php') ? 'active' : ''; ?>" title="History"><i class="far fa-clock"></i></a>
            <a href="wallet.php" class="nav-item <?php echo ($current_page == 'wallet.php') ? 'active' : ''; ?>" title="Wallet"><i class="fas fa-wallet"></i></a>
            <a href="settings.php" class="nav-item <?php echo ($current_page == 'settings.php') ? 'active' : ''; ?>" title="Settings"><i class="fas fa-cog"></i></a>
        </div>
    </div>
    <div class="nav-menu">
        <a href="#" class="nav-item" title="Help"><i class="far fa-question-circle"></i></a>
        <a href="../auth/logout.php" class="nav-item" title="Logout"><i class="fas fa-sign-out-alt"></i></a>
    </div>
</div>
