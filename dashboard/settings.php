<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Settings — MoneyTrek</title>
    <?php include '../includes/head.php'; ?>
    <style>
        .settings-container {
            background: var(--card-bg);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255,255,255,0.5);
            border-radius: var(--radius-lg);
            padding: 30px;
            box-shadow: var(--shadow-soft);
            max-width: 800px;
        }
        .setting-group {
            margin-bottom: 30px;
        }
        .setting-group h3 {
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .setting-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
        }
        .setting-info h4 {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--text-dark);
        }
        .setting-info p {
            font-size: 0.8rem;
            color: var(--text-muted);
            font-weight: 500;
            margin-top: 4px;
        }
        
        /* Toggle Switch Mockup */
        .toggle-switch {
            width: 50px;
            height: 26px;
            background: #ddd;
            border-radius: 15px;
            position: relative;
            cursor: pointer;
            transition: 0.3s;
        }
        .toggle-switch.active { background: var(--blue-accent); }
        .toggle-switch::after {
            content: '';
            position: absolute;
            top: 3px;
            left: 3px;
            width: 20px;
            height: 20px;
            background: white;
            border-radius: 50%;
            transition: 0.3s;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .toggle-switch.active::after { transform: translateX(24px); }
        
        .btn-outline {
            padding: 8px 16px;
            border: 1.5px solid var(--text-muted);
            background: transparent;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.85rem;
            color: var(--text-dark);
            cursor: pointer;
            transition: 0.2s;
        }
        .btn-outline:hover { background: var(--text-dark); color: white; border-color: var(--text-dark);}
    </style>
</head>
<body>

<div class="app-window">
    <!-- SIDEBAR -->
    <?php include '../includes/sidebar.php'; ?>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="page-title-header">
            <div>
                <h2>Settings</h2>
                <p>Manage your account preferences and app settings.</p>
            </div>
        </div>

        <div class="settings-container">
            <div class="setting-group">
                <h3>Profile</h3>
                <div class="setting-item">
                    <div class="setting-info">
                        <h4>Username</h4>
                        <p><?php echo htmlspecialchars($username); ?></p>
                    </div>
                    <button class="btn-outline">Edit</button>
                </div>
                <div class="setting-item">
                    <div class="setting-info">
                        <h4>Email Address</h4>
                        <p>user@example.com</p>
                    </div>
                    <button class="btn-outline">Edit</button>
                </div>
            </div>

            <div class="setting-group">
                <h3>Preferences</h3>
                <div class="setting-item">
                    <div class="setting-info">
                        <h4>Dark Mode</h4>
                        <p>Enable dark theme for the dashboard</p>
                    </div>
                    <div class="toggle-switch" onclick="this.classList.toggle('active')"></div>
                </div>
                <div class="setting-item">
                    <div class="setting-info">
                        <h4>Email Notifications</h4>
                        <p>Receive weekly summary reports</p>
                    </div>
                    <div class="toggle-switch active" onclick="this.classList.toggle('active')"></div>
                </div>
                <div class="setting-item">
                    <div class="setting-info">
                        <h4>Currency</h4>
                        <p>Base currency for your wallet (IDR)</p>
                    </div>
                    <button class="btn-outline">Change</button>
                </div>
            </div>

            <div class="setting-group" style="border:none;">
                <h3>Security</h3>
                <div class="setting-item">
                    <div class="setting-info">
                        <h4>Change Password</h4>
                        <p>Update your account password</p>
                    </div>
                    <button class="btn-outline">Update</button>
                </div>
                <div class="setting-item">
                    <div class="setting-info">
                        <h4 style="color:var(--red-accent);">Delete Account</h4>
                        <p>Permanently remove your account and data</p>
                    </div>
                    <button class="btn-outline" style="border-color:var(--red-accent); color:var(--red-accent);">Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
