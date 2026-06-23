<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

function formatRupiah($number) { return 'Rp ' . number_format(abs($number), 0, ',', '.'); }

function getWalletBalances($conn, $user_id) {
    $wallets = ['Tunai' => 0, 'Rekening BCA' => 0, 'GoPay' => 0, 'OVO' => 0];
    $stmt = $conn->prepare("SELECT wallet_type, 
        SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) - 
        SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as bal 
        FROM transactions WHERE user_id = ? GROUP BY wallet_type");
    $stmt->bind_param("i", $user_id); $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $w = $row['wallet_type'] ?: 'Tunai';
        if (array_key_exists($w, $wallets)) {
            $wallets[$w] += $row['bal'];
        } else {
            $wallets[$w] = $row['bal'];
        }
    }
    $stmt->close();
    
    $formatted = [];
    foreach ($wallets as $k => $v) {
        $formatted[$k] = formatRupiah($v);
    }
    return $formatted;
}

// Handle add transaction via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_action'])) {
    header('Content-Type: application/json');
    if ($_POST['ajax_action'] === 'add') {
        $type = $_POST['type'];
        $amount = $_POST['amount'];
        $description = $_POST['description'];
        $date = $_POST['date'];
        $category = $_POST['category'] ?? 'Lainnya';
        $wallet_type = $_POST['wallet_type'] ?? 'Tunai';
        
        $check = $conn->query("SHOW COLUMNS FROM transactions LIKE 'category'");
        if ($check->num_rows == 0) {
            $conn->query("ALTER TABLE transactions ADD COLUMN category VARCHAR(50) DEFAULT 'Lainnya' AFTER description");
        }
        
        $stmt = $conn->prepare("INSERT INTO transactions (user_id, type, amount, description, date, category, wallet_type) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isdssss", $user_id, $type, $amount, $description, $date, $category, $wallet_type);
        
        $save_to_savings = isset($_POST['save_to_savings']) && $_POST['save_to_savings'] === 'true';
        $savings_id = isset($_POST['savings_id']) ? intval($_POST['savings_id']) : 0;
        
        if ($stmt->execute()) { 
            $new_id = $conn->insert_id;
            
            // Add to savings if selected
            if ($save_to_savings && $savings_id > 0) {
                $stmt_upd_sav = $conn->prepare("UPDATE savings SET current_amount = current_amount + ? WHERE id = ? AND user_id = ?");
                $stmt_upd_sav->bind_param("dii", $amount, $savings_id, $user_id);
                $stmt_upd_sav->execute();
                $stmt_upd_sav->close();
            }
            
            // Recalculate totals
            $stmt_inc = $conn->prepare("SELECT COALESCE(SUM(amount),0) AS total FROM transactions WHERE user_id = ? AND type = 'income'");
            $stmt_inc->bind_param("i", $user_id); $stmt_inc->execute();
            $t_inc = $stmt_inc->get_result()->fetch_assoc()['total']; $stmt_inc->close();

            $stmt_exp = $conn->prepare("SELECT COALESCE(SUM(amount),0) AS total FROM transactions WHERE user_id = ? AND type = 'expense'");
            $stmt_exp->bind_param("i", $user_id); $stmt_exp->execute();
            $t_exp = $stmt_exp->get_result()->fetch_assoc()['total']; $stmt_exp->close();

            $bal = $t_inc - $t_exp;
            $grand = $t_inc + $t_exp;
            $r_inc = $grand > 0 ? round($t_inc / $grand * 100) : 50;
            $r_exp = 100 - $r_inc;

            // Formatted Strings for JS
            $fmt_amount = ($type === 'income' ? '+' : '') . formatRupiah($amount);
            $fmt_date = date('d F, Y', strtotime($date));
            $icon_class = $type === 'income' ? 'fa-arrow-down' : 'fa-shopping-bag';

            $new_row_html = '
            <div class="trx-item" id="trx-'.$new_id.'">
                <div class="trx-left">
                    <div class="trx-icon '.$type.'"><i class="fas '.$icon_class.'"></i></div>
                    <div class="trx-info">
                        <h4>'.htmlspecialchars($description).'</h4>
                        <p>'.$fmt_date.'</p>
                    </div>
                </div>
                <div class="trx-right">
                    <div class="trx-amount '.($type == 'income' ? 'inc' : '').'">'.$fmt_amount.'</div>
                    <button class="trx-action" onclick="delTrx('.$new_id.')"><i class="fas fa-trash-alt" style="font-size:0.9rem;"></i></button>
                </div>
            </div>';

            echo json_encode([
                'success' => true, 
                'id' => $new_id,
                'balance_html' => formatRupiah($bal),
                'ratio_inc' => $r_inc,
                'ratio_exp' => $r_exp,
                'wallets' => getWalletBalances($conn, $user_id),
                'new_row_html' => $new_row_html
            ]); 
        } else { 
            echo json_encode(['success' => false, 'error' => $stmt->error]); 
        }
        $stmt->close();
        exit();
    }
    
    if ($_POST['ajax_action'] === 'delete') {
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("DELETE FROM transactions WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $id, $user_id);
        if ($stmt->execute()) {
            // Recalculate
            $stmt_inc = $conn->prepare("SELECT COALESCE(SUM(amount),0) AS total FROM transactions WHERE user_id = ? AND type = 'income'");
            $stmt_inc->bind_param("i", $user_id); $stmt_inc->execute();
            $t_inc = $stmt_inc->get_result()->fetch_assoc()['total']; $stmt_inc->close();

            $stmt_exp = $conn->prepare("SELECT COALESCE(SUM(amount),0) AS total FROM transactions WHERE user_id = ? AND type = 'expense'");
            $stmt_exp->bind_param("i", $user_id); $stmt_exp->execute();
            $t_exp = $stmt_exp->get_result()->fetch_assoc()['total']; $stmt_exp->close();

            $bal = $t_inc - $t_exp;
            $grand = $t_inc + $t_exp;
            $r_inc = $grand > 0 ? round($t_inc / $grand * 100) : 50;
            $r_exp = 100 - $r_inc;

            echo json_encode([
                'success' => true,
                'balance_html' => formatRupiah($bal),
                'ratio_inc' => $r_inc,
                'ratio_exp' => $r_exp,
                'wallets' => getWalletBalances($conn, $user_id)
            ]);
        } else {
            echo json_encode(['success' => false]);
        }
        $stmt->close();
        exit();
    }
}

// Ensure columns exist
$checkWallet = $conn->query("SHOW COLUMNS FROM transactions LIKE 'wallet_type'");
if ($checkWallet->num_rows == 0) {
    $conn->query("ALTER TABLE transactions ADD COLUMN wallet_type VARCHAR(50) DEFAULT 'Tunai' AFTER category");
}
$wallet_balances = getWalletBalances($conn, $user_id);

// Get Data for initial load
$stmt = $conn->prepare("SELECT COALESCE(SUM(amount),0) AS total FROM transactions WHERE user_id = ? AND type = 'income'");
$stmt->bind_param("i", $user_id); $stmt->execute();
$total_income = $stmt->get_result()->fetch_assoc()['total']; $stmt->close();

$stmt = $conn->prepare("SELECT COALESCE(SUM(amount),0) AS total FROM transactions WHERE user_id = ? AND type = 'expense'");
$stmt->bind_param("i", $user_id); $stmt->execute();
$total_expense = $stmt->get_result()->fetch_assoc()['total']; $stmt->close();

$balance = $total_income - $total_expense;

$stmt = $conn->prepare("SELECT * FROM transactions WHERE user_id = ? ORDER BY date DESC, created_at DESC LIMIT 6");
$stmt->bind_param("i", $user_id); $stmt->execute();
$transactions_result = $stmt->get_result();
$transactions = [];
while ($r = $transactions_result->fetch_assoc()) { $transactions[] = $r; }
$stmt->close();

$grand = $total_income + $total_expense;
$ratio_inc = $grand > 0 ? round($total_income / $grand * 100) : 50;
$ratio_exp = 100 - $ratio_inc;
$avatar_initial = strtoupper(substr($username, 0, 1));
$first_name = explode(' ', trim($username))[0];

// Fetch savings goals for the transaction modal
$stmt_sav = $conn->prepare("SELECT id, item_name FROM savings WHERE user_id = ? ORDER BY created_at DESC");
$stmt_sav->bind_param("i", $user_id);
$stmt_sav->execute();
$savings_goals = [];
$res_sav = $stmt_sav->get_result();
while ($row = $res_sav->fetch_assoc()) {
    $savings_goals[] = $row;
}
$stmt_sav->close();

// Fetch last 5 months income data for chart
$monthly_data = [];
$monthly_labels = [];
for ($i = 4; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i month"));
    $label = strtoupper(date('M', strtotime("-$i month")));
    $monthly_data[$month] = 0;
    $monthly_labels[] = $label;
}

$start_date = date('Y-m-01', strtotime("-4 month"));
$end_date = date('Y-m-t');
$stmt = $conn->prepare("SELECT DATE_FORMAT(date, '%Y-%m') as month, SUM(amount) as total FROM transactions WHERE user_id = ? AND type = 'income' AND date >= ? AND date <= ? GROUP BY month");
$stmt->bind_param("iss", $user_id, $start_date, $end_date);
$stmt->execute();
$res = $stmt->get_result();
while($row = $res->fetch_assoc()) {
    if(isset($monthly_data[$row['month']])) {
        $monthly_data[$row['month']] = $row['total'];
    }
}
$stmt->close();

$monthly_earnings = array_values($monthly_data);

$curr = $monthly_earnings[4];
$prev = $monthly_earnings[3];
$diff = $curr - $prev;
if ($prev > 0) {
    $perc = round(($diff / $prev) * 100);
} else {
    $perc = ($curr > 0) ? 100 : 0;
}
$icon_class = $perc >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';
$perc_text = abs($perc) . '%';
$badge_color = $perc >= 0 ? '#059669' : '#ef4444';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Dashboard — MoneyTrek</title>
    <?php include '../includes/head.php'; ?>
</head>
<body>

<div class="app-window">
    
    <!-- SIDEBAR -->
    <?php include '../includes/sidebar.php'; ?>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        
        <!-- HEADER -->
        <div class="top-header">
            <div class="header-left">
                <div class="sub-brand">Finance Dashboard <span>MoneyTrek</span></div>
                <div class="greeting">
                    <h1>Hello, <span><?php echo htmlspecialchars($first_name); ?></span></h1>
                    <p>View and control your finances seamlessly</p>
                </div>
            </div>
            <div class="header-right">
                <div class="avatars-group">
                    <div class="ava-circle" style="background: url('https://i.pravatar.cc/100?img=11') center/cover;"></div>
                    <div class="ava-circle" style="background: url('https://i.pravatar.cc/100?img=12') center/cover;"></div>
                    <div class="ava-circle" style="background: url('https://i.pravatar.cc/100?img=33') center/cover;"></div>
                    <div class="ava-circle" style="background: url('https://i.pravatar.cc/100?img=44') center/cover;"></div>
                    <div class="ava-circle" style="background: url('https://i.pravatar.cc/100?img=15') center/cover;"></div>
                    <i class="fas fa-chevron-right"></i>
                </div>
                <button class="icon-btn"><i class="far fa-bell"></i></button>
                <div class="search-bar">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search...">
                </div>
                <div class="my-avatar"><?php echo $avatar_initial; ?></div>
            </div>
        </div>

        <!-- BENTO GRID -->
        <div class="bento-grid">
            
            <!-- BALANCE -->
            <div class="card balance-card">
                <div>
                    <div class="card-title">Balance Statistics</div>
                    <div class="balance-amount" id="dom-balance"><?php echo formatRupiah($balance); ?> <span class="balance-label">Total amount</span></div>
                </div>
                <div>
                    <p style="font-size:0.75rem; color:var(--text-muted); font-weight:600; line-height:1.5; margin-bottom: 10px;">Always see<br>your earning updates</p>
                    <div class="chart-mockup" style="position: relative; height: 80px; width: 100%; margin-top: 10px; background: none; align-items: stretch; display: block;">
                        <div class="badge-up" style="color: <?php echo $badge_color; ?>;"><i class="fas <?php echo $icon_class; ?>"></i> <?php echo $perc_text; ?></div>
                        <canvas id="earningChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- BANK CARD -> MY WALLET -->
            <div class="card wallet-card-container">
                <div class="card-title">My Wallet</div>
                <div class="wallet-grid">
                    <div class="wallet-item">
                        <div class="wallet-icon" style="background: var(--blue-accent); color: white;"><i class="fas fa-wallet"></i></div>
                        <div class="wallet-info">
                            <span class="wallet-name">Tunai</span>
                            <h4 class="wallet-bal" id="dom-wallet-Tunai"><?php echo $wallet_balances['Tunai'] ?? 'Rp 0'; ?></h4>
                        </div>
                    </div>
                    <div class="wallet-item">
                        <div class="wallet-icon" style="background: #0066AE; color: white;"><i class="fas fa-university"></i></div>
                        <div class="wallet-info">
                            <span class="wallet-name">Rekening BCA</span>
                            <h4 class="wallet-bal" id="dom-wallet-BCA"><?php echo $wallet_balances['Rekening BCA'] ?? 'Rp 0'; ?></h4>
                        </div>
                    </div>
                    <div class="wallet-item">
                        <div class="wallet-icon" style="background: #00aed6; color: white;"><i class="fas fa-mobile-alt"></i></div>
                        <div class="wallet-info">
                            <span class="wallet-name">GoPay</span>
                            <h4 class="wallet-bal" id="dom-wallet-GoPay"><?php echo $wallet_balances['GoPay'] ?? 'Rp 0'; ?></h4>
                        </div>
                    </div>
                    <div class="wallet-item">
                        <div class="wallet-icon" style="background: #4C3494; color: white;"><i class="fas fa-bolt"></i></div>
                        <div class="wallet-info">
                            <span class="wallet-name">OVO</span>
                            <h4 class="wallet-bal" id="dom-wallet-OVO"><?php echo $wallet_balances['OVO'] ?? 'Rp 0'; ?></h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ANALYTICS -->
            <div class="card analytics-card">
                <div class="card-title">Analytics <i class="fas fa-ellipsis-h"></i></div>
                <ul class="legend">
                    <li><span class="dot" style="background:var(--blue-accent)"></span> Income</li>
                    <li><span class="dot" style="background:var(--sidebar-bg)"></span> Expense</li>
                    <li><span class="dot" style="background:var(--red-accent)"></span> Saved</li>
                </ul>
                <div class="donut-container">
                    <canvas id="donutChart"></canvas>
                    <div class="donut-inner">
                        <h3 id="dom-ratio-inc-center"><?php echo $ratio_inc; ?>%</h3>
                        <span>Income</span>
                    </div>
                </div>
            </div>

            <!-- LAST TRANSACTIONS -->
            <div class="card trx-card">
                <div class="card-title">
                    Last Transactions 
                    <a href="history.php" style="font-size:0.8rem; color:var(--blue-accent); text-decoration:none; font-weight:700;">View All</a>
                </div>
                <div class="trx-list" id="dom-trx-list">
                    <?php if (count($transactions) === 0): ?>
                        <div class="empty-state" id="trx-empty">
                            <i class="fas fa-receipt"></i>
                            <p>No transactions yet.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($transactions as $t): 
                            $isInc = $t['type'] === 'income';
                            $icon = $isInc ? 'fa-arrow-down' : 'fa-shopping-bag';
                        ?>
                        <div class="trx-item" id="trx-<?php echo $t['id']; ?>">
                            <div class="trx-left">
                                <div class="trx-icon <?php echo $t['type']; ?>"><i class="fas <?php echo $icon; ?>"></i></div>
                                <div class="trx-info">
                                    <h4><?php echo htmlspecialchars($t['description']); ?></h4>
                                    <p><?php echo date('d F, Y', strtotime($t['date'])); ?></p>
                                </div>
                            </div>
                            <div class="trx-right">
                                <div class="trx-amount <?php echo $isInc ? 'inc' : ''; ?>"><?php echo ($isInc ? '+' : '') . formatRupiah($t['amount']); ?></div>
                                <button class="trx-action" onclick="delTrx(<?php echo $t['id']; ?>)"><i class="fas fa-trash-alt" style="font-size:0.9rem;"></i></button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- EXPENSES & INCOME + ADD BTN -->
            <div class="right-stack">
                <div class="card">
                    <div class="card-title">Expenses & Income</div>
                    <div class="ei-stats">
                        <div class="ei-stat"><h2 id="dom-ratio-exp"><?php echo $ratio_exp; ?>%</h2><span>Expenses</span></div>
                        <div class="ei-stat"><h2 id="dom-ratio-inc"><?php echo $ratio_inc; ?>%</h2><span>Income</span></div>
                    </div>
                    <div class="ei-bars">
                        <div class="ei-bar" id="dom-bar-exp" style="width: <?php echo $ratio_exp; ?>%; background: var(--sidebar-bg);"></div>
                        <div class="ei-bar" id="dom-bar-inc" style="width: <?php echo $ratio_inc; ?>%; background: var(--blue-accent);"></div>
                    </div>
                </div>

                <div class="premium-card" onclick="openModal()">
                    <div class="premium-icon"><i class="fas fa-plus-circle"></i></div>
                    <div class="premium-text">
                        <h4>New Transaction</h4>
                        <p>Keep your finances up to date easily.</p>
                    </div>
                    <button class="premium-btn">Add New</button>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- MODAL ADD TRANSACTION -->
<div class="modal-overlay" id="addModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Add Transaction</h3>
            <button class="close-btn" onclick="closeModal()" type="button"><i class="fas fa-times"></i></button>
        </div>
        <form id="txForm">
            <div class="type-toggle">
                <div class="type-btn active inc" onclick="setType('income', this)">Income</div>
                <div class="type-btn exp" onclick="setType('expense', this)">Expense</div>
            </div>
            <input type="hidden" id="txType" value="income">
            
            <div class="form-group">
                <label>Description</label>
                <input type="text" id="txName" placeholder="e.g. Salary, Spotify..." required>
            </div>
            <div class="form-group">
                <label>Amount (Rp)</label>
                <input type="number" id="txAmount" placeholder="0" required min="1">
            </div>
            <div class="form-group">
                <label>Wallet</label>
                <select id="txWallet" required>
                    <option value="Tunai">Tunai</option>
                    <option value="Rekening BCA">Rekening BCA</option>
                    <option value="GoPay">GoPay</option>
                    <option value="OVO">OVO</option>
                </select>
            </div>
            <div class="form-group">
                <label>Date</label>
                <input type="date" id="txDate" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div class="form-group" style="display: flex; align-items: center; gap: 10px; margin-top: 15px;">
                <input type="checkbox" id="txSaveToSavings" style="width: 20px; height: 20px;">
                <label for="txSaveToSavings" style="margin: 0;">Simpan ke Tabungan?</label>
            </div>
            <div class="form-group" id="savingsSelectGroup" style="display: none; margin-top: 15px;">
                <label>Pilih Target Tabungan</label>
                <select id="txSavingsId">
                    <option value="">-- Pilih Target Tabungan --</option>
                    <?php foreach($savings_goals as $sav): ?>
                        <option value="<?php echo $sav['id']; ?>"><?php echo htmlspecialchars($sav['item_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="submit-btn" id="btnSubmit">Save Transaction</button>
        </form>
    </div>
</div>

<div id="toast"><i class="fas fa-check-circle"></i> <span id="toast-msg">Success!</span></div>

<script>
    // CHART JS DONUT
    let donutChart;
    const ctx = document.getElementById('donutChart').getContext('2d');
    
    function initChart(incRatio, expRatio) {
        if(donutChart) donutChart.destroy();
        donutChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Income', 'Expense', 'Saved'],
                datasets: [{
                    data: [incRatio, expRatio, 10], // 10 is a mocked saved value for aesthetics
                    backgroundColor: ['#059669', '#0d1117', '#ef4444'],
                    borderWidth: 0,
                    cutout: '75%',
                    hoverOffset: 4
                }]
            },
            options: { 
                plugins: { legend: { display: false }, tooltip: { enabled: true, backgroundColor: '#1c1c1e', padding: 10, cornerRadius: 8 } }, 
                responsive: true, maintainAspectRatio: false,
                animation: { animateScale: true, animateRotate: true }
            }
        });
    }
    
    initChart(<?php echo $ratio_inc; ?>, <?php echo $ratio_exp; ?>);

    // CHART JS EARNING (BAR CHART)
    const earningCtx = document.getElementById('earningChart').getContext('2d');
    const earningChart = new Chart(earningCtx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($monthly_labels); ?>,
            datasets: [{
                label: 'Income',
                data: <?php echo json_encode($monthly_earnings); ?>,
                backgroundColor: '#059669',
                borderRadius: 4,
                barPercentage: 0.6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { enabled: true, backgroundColor: '#1c1c1e', padding: 10, cornerRadius: 8 } },
            scales: {
                x: { display: true, grid: { display: false, drawBorder: false }, border: { display: false }, ticks: { font: { size: 10, weight: 'bold' }, color: '#9ca3af' } },
                y: { display: false, grid: { display: false, drawBorder: false }, border: { display: false } }
            }
        }
    });

    // MODAL LOGIC
    const modal = document.getElementById('addModal');
    function openModal() { modal.classList.add('show'); }
    function closeModal() { 
        modal.classList.remove('show'); 
        document.getElementById('txForm').reset(); 
        document.getElementById('txDate').value = '<?php echo date('Y-m-d'); ?>'; 
        document.getElementById('savingsSelectGroup').style.display = 'none';
        document.getElementById('txSavingsId').required = false;
    }
    
    document.getElementById('txSaveToSavings').addEventListener('change', function() {
        document.getElementById('savingsSelectGroup').style.display = this.checked ? 'block' : 'none';
        document.getElementById('txSavingsId').required = this.checked;
    });
    
    function setType(val, el) {
        document.getElementById('txType').value = val;
        document.querySelectorAll('.type-btn').forEach(btn => btn.classList.remove('active'));
        el.classList.add('active');
    }

    function showToast(msg) {
        const t = document.getElementById('toast');
        document.getElementById('toast-msg').textContent = msg;
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 3000);
    }

    function updateDOM(data) {
        // Update Balance
        document.getElementById('dom-balance').innerHTML = `${data.balance_html} <span class="balance-label">Total amount</span>`;
        
        // Update Chart
        initChart(data.ratio_inc, data.ratio_exp);
        document.getElementById('dom-ratio-inc-center').textContent = `${data.ratio_inc}%`;
        
        // Update Wallets
        if(data.wallets) {
            if(document.getElementById('dom-wallet-Tunai')) document.getElementById('dom-wallet-Tunai').textContent = data.wallets['Tunai'] || 'Rp 0';
            if(document.getElementById('dom-wallet-BCA')) document.getElementById('dom-wallet-BCA').textContent = data.wallets['Rekening BCA'] || 'Rp 0';
            if(document.getElementById('dom-wallet-GoPay')) document.getElementById('dom-wallet-GoPay').textContent = data.wallets['GoPay'] || 'Rp 0';
            if(document.getElementById('dom-wallet-OVO')) document.getElementById('dom-wallet-OVO').textContent = data.wallets['OVO'] || 'Rp 0';
        }

        // Update Expenses & Income block
        document.getElementById('dom-ratio-exp').textContent = `${data.ratio_exp}%`;
        document.getElementById('dom-ratio-inc').textContent = `${data.ratio_inc}%`;
        document.getElementById('dom-bar-exp').style.width = `${data.ratio_exp}%`;
        document.getElementById('dom-bar-inc').style.width = `${data.ratio_inc}%`;
    }

    // ADD TRANSACTION AJAX
    document.getElementById('txForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('btnSubmit');
        btn.textContent = 'Saving...'; btn.disabled = true;

        const formData = new FormData();
        formData.append('ajax_action', 'add');
        formData.append('type', document.getElementById('txType').value);
        formData.append('amount', document.getElementById('txAmount').value);
        formData.append('description', document.getElementById('txName').value);
        formData.append('wallet_type', document.getElementById('txWallet').value);
        formData.append('date', document.getElementById('txDate').value);
        formData.append('save_to_savings', document.getElementById('txSaveToSavings').checked);
        formData.append('savings_id', document.getElementById('txSavingsId').value);

        fetch('dashboard.php', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(d => {
            if(d.success) { 
                showToast('Transaction added successfully!'); 
                closeModal();
                
                // Remove empty state if exists
                const emptyState = document.getElementById('trx-empty');
                if(emptyState) emptyState.remove();

                // Prepend new row
                const list = document.getElementById('dom-trx-list');
                list.insertAdjacentHTML('afterbegin', d.new_row_html);
                
                // Update UI numbers
                updateDOM(d);

            } else { 
                alert('Error saving data: ' + d.error); 
            }
        })
        .catch(err => {
            console.error(err);
            alert('A network error occurred.');
        })
        .finally(() => {
            btn.textContent = 'Save Transaction'; btn.disabled = false;
        });
    });

    // DELETE TRANSACTION AJAX
    window.delTrx = function(id) {
        if(confirm('Are you sure you want to delete this transaction?')) {
            const fd = new FormData();
            fd.append('ajax_action', 'delete'); fd.append('id', id);
            
            fetch('dashboard.php', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(d => {
                if(d.success) { 
                    showToast('Transaction deleted!'); 
                    
                    // Remove from DOM
                    const row = document.getElementById('trx-' + id);
                    if(row) row.remove();

                    // Check if empty
                    const list = document.getElementById('dom-trx-list');
                    if(list.children.length === 0) {
                        list.innerHTML = `<div class="empty-state" id="trx-empty"><i class="fas fa-receipt"></i><p>No transactions yet.</p></div>`;
                    }

                    // Update UI numbers
                    updateDOM(d);
                } else {
                    alert('Error deleting transaction.');
                }
            });
        }
    };
</script>
</body>
</html>