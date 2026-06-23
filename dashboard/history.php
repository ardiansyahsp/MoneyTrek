<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

function formatRupiah($number) { return 'Rp ' . number_format(abs($number), 0, ',', '.'); }

// Handle Filters
$period = $_GET['period'] ?? 'all';
$type = $_GET['type'] ?? 'all';

$query = "SELECT * FROM transactions WHERE user_id = ?";
$types = "i";
$params = [$user_id];

if ($period === 'this_month') {
    $query .= " AND MONTH(date) = MONTH(CURRENT_DATE()) AND YEAR(date) = YEAR(CURRENT_DATE())";
} elseif ($period === 'last_month') {
    $query .= " AND MONTH(date) = MONTH(CURRENT_DATE() - INTERVAL 1 MONTH) AND YEAR(date) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)";
}

if ($type === 'income' || $type === 'expense') {
    $query .= " AND type = ?";
    $types .= "s";
    $params[] = $type;
}

$query .= " ORDER BY date DESC, created_at DESC";

$stmt = $conn->prepare($query);
if (count($params) === 1) {
    $stmt->bind_param($types, $params[0]);
} elseif (count($params) === 2) {
    $stmt->bind_param($types, $params[0], $params[1]);
}

$stmt->execute();
$transactions_result = $stmt->get_result();
$transactions = [];
while ($r = $transactions_result->fetch_assoc()) { $transactions[] = $r; }
$stmt->close();

$period_label = "All Time";
if ($period === 'this_month') $period_label = "This Month";
if ($period === 'last_month') $period_label = "Last Month";

$type_label = "All Types";
if ($type === 'income') $type_label = "Income";
if ($type === 'expense') $type_label = "Expense";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>History — MoneyTrek</title>
    <?php include '../includes/head.php'; ?>
    <style>
        .history-container {
            background: var(--card-bg);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255,255,255,0.5);
            border-radius: var(--radius-lg);
            padding: 30px;
            box-shadow: var(--shadow-soft);
            height: calc(100vh - 140px);
            display: flex;
            flex-direction: column;
        }
        .history-list {
            flex: 1;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 15px;
            padding-right: 10px;
            margin-top: 20px;
        }
        .history-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        /* Dropdown Styles */
        .dropdown {
            position: relative;
            display: inline-block;
        }
        .filter-btn {
            background: white;
            border: 1px solid #ddd;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 700;
            cursor: pointer;
            color: var(--text-dark);
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .filter-btn:hover { background: var(--app-bg); }
        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: white;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.1);
            z-index: 100;
            border-radius: 12px;
            overflow: hidden;
            margin-top: 8px;
            border: 1px solid #eee;
        }
        .dropdown-content a {
            color: var(--text-dark);
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            transition: 0.2s;
        }
        .dropdown-content a:hover { background-color: var(--app-bg); color: var(--blue-accent); }
        .dropdown.show .dropdown-content { display: block; }
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
                <h2>Transaction History</h2>
                <p>Track all your past income and expenses.</p>
            </div>
            <button class="icon-btn"><i class="fas fa-download"></i></button>
        </div>

        <div class="history-container">
            <div class="history-header">
                <h3>All Transactions</h3>
                <div style="display:flex; gap:10px;">
                    
                    <!-- Period Filter -->
                    <div class="dropdown" id="periodDropdown">
                        <button class="filter-btn" onclick="toggleDropdown('periodDropdown')">
                            <?php echo $period_label; ?> <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="dropdown-content">
                            <a href="?period=all&type=<?php echo $type; ?>">All Time</a>
                            <a href="?period=this_month&type=<?php echo $type; ?>">This Month</a>
                            <a href="?period=last_month&type=<?php echo $type; ?>">Last Month</a>
                        </div>
                    </div>

                    <!-- Type Filter -->
                    <div class="dropdown" id="typeDropdown">
                        <button class="filter-btn" onclick="toggleDropdown('typeDropdown')">
                            <i class="fas fa-filter"></i> <?php echo $type_label; ?>
                        </button>
                        <div class="dropdown-content">
                            <a href="?period=<?php echo $period; ?>&type=all">All Types</a>
                            <a href="?period=<?php echo $period; ?>&type=income">Income Only</a>
                            <a href="?period=<?php echo $period; ?>&type=expense">Expense Only</a>
                        </div>
                    </div>

                </div>
            </div>

            <div class="history-list">
                <?php if (count($transactions) === 0): ?>
                    <div class="empty-state">
                        <i class="fas fa-receipt"></i>
                        <p>No transactions found for this filter.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($transactions as $t): 
                        $isInc = $t['type'] === 'income';
                        $icon = $isInc ? 'fa-arrow-down' : 'fa-shopping-bag';
                    ?>
                    <div class="trx-item">
                        <div class="trx-left">
                            <div class="trx-icon <?php echo $t['type']; ?>"><i class="fas <?php echo $icon; ?>"></i></div>
                            <div class="trx-info">
                                <h4><?php echo htmlspecialchars($t['description']); ?></h4>
                                <p><?php echo date('d F, Y', strtotime($t['date'])); ?> • <?php echo htmlspecialchars($t['category'] ?? 'Lainnya'); ?></p>
                            </div>
                        </div>
                        <div class="trx-right">
                            <div class="trx-amount <?php echo $isInc ? 'inc' : ''; ?>"><?php echo ($isInc ? '+' : '') . formatRupiah($t['amount']); ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    // Close dropdowns when clicking outside
    window.onclick = function(event) {
        if (!event.target.matches('.filter-btn') && !event.target.matches('.filter-btn *')) {
            var dropdowns = document.getElementsByClassName("dropdown");
            for (var i = 0; i < dropdowns.length; i++) {
                var openDropdown = dropdowns[i];
                if (openDropdown.classList.contains('show')) {
                    openDropdown.classList.remove('show');
                }
            }
        }
    }

    function toggleDropdown(id) {
        // Close others first
        var dropdowns = document.getElementsByClassName("dropdown");
        for (var i = 0; i < dropdowns.length; i++) {
            if(dropdowns[i].id !== id) {
                dropdowns[i].classList.remove('show');
            }
        }
        document.getElementById(id).classList.toggle("show");
    }
</script>

</body>
</html>
