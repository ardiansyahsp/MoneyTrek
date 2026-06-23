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
    <title>Savings Goals — MoneyTrek</title>
    <?php include '../includes/head.php'; ?>
    <style>
        .wallet-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .card-list {
            background: var(--card-bg);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255,255,255,0.5);
            border-radius: var(--radius-lg);
            padding: 30px;
            box-shadow: var(--shadow-soft);
        }
        .card-list h3 {
            margin-bottom: 20px;
            font-size: 1.2rem;
            font-weight: 800;
        }
        
        .mock-card {
            background: linear-gradient(135deg, #1c1c1e, #3a3a3c);
            border-radius: 20px;
            padding: 20px;
            color: white;
            margin-bottom: 15px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            transition: transform 0.2s;
            cursor: pointer;
        }
        .mock-card:hover { transform: translateY(-3px); }
        .mock-card.blue { background: linear-gradient(135deg, #a8bce6, #809cdd); color: white;}
        .mock-card.yellow { background: linear-gradient(135deg, #e5cc8a, #d4b568); color: #111;}
        
        .mc-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;}
        .mc-type { font-size: 0.8rem; font-weight: 700; opacity: 0.8; letter-spacing: 1px; text-transform: uppercase;}
        .mc-bal { font-size: 1.6rem; font-weight: 800; }
        .mc-bottom { display: flex; justify-content: space-between; align-items: flex-end;}
        .mc-num { font-family: monospace; font-size: 1.1rem; letter-spacing: 2px; font-weight: 600; opacity: 0.9;}
        
        .add-card-btn {
            width: 100%;
            padding: 15px;
            border: 2px dashed #ccc;
            border-radius: 20px;
            background: transparent;
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .add-card-btn:hover { background: rgba(255,255,255,0.5); border-color: var(--emerald); color: var(--emerald); }
        
        /* Savings Progress Cards */
        .savings-card {
            background: white;
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            border: 1px solid var(--line-light, #eee);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .savings-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            border-color: var(--emerald, #059669);
        }
        .sc-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .sc-title {
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--ink, #111);
        }
        .sc-percent {
            font-size: 0.9rem;
            font-weight: 800;
            color: var(--emerald, #059669);
            background: var(--emerald-soft, #ecfdf5);
            padding: 4px 10px;
            border-radius: 20px;
        }
        .sc-progress-bg {
            width: 100%;
            height: 12px;
            background: var(--bg, #f0f0f0);
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 12px;
        }
        .sc-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--blue-light, #34d399), var(--emerald, #059669));
            border-radius: 10px;
            transition: width 1s ease-in-out;
        }
        .sc-footer {
            display: flex;
            justify-content: space-between;
            font-size: 0.85rem;
            color: var(--text-muted, #777);
            font-weight: 600;
        }
        .sc-footer span strong {
            color: var(--ink, #111);
        }
        
        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(5px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }
        .modal-content {
            background: var(--surface, #ffffff);
            border: 1px solid var(--line-light, rgba(0,0,0,0.1));
            border-radius: var(--radius, 18px);
            padding: 30px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            transform: translateY(20px);
            transition: all 0.3s ease;
            color: var(--ink, #000);
        }
        .modal-overlay.active .modal-content {
            transform: translateY(0);
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .modal-header h3 { margin: 0; font-size: 1.2rem; color: var(--ink, #000); }
        .close-modal {
            background: none; border: none; color: var(--muted, #666);
            font-size: 1.5rem; cursor: pointer; opacity: 0.7;
        }
        .close-modal:hover { opacity: 1; color: var(--red, #f00); }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-size: 0.9rem; font-weight: 600; color: var(--ink, #000);}
        .form-group input, .form-group select {
            width: 100%; padding: 12px 15px;
            border-radius: 12px;
            border: 1px solid var(--line-light, #ccc);
            background: var(--bg, #f6f8fa);
            color: var(--ink, #000); font-size: 1rem;
            font-family: inherit;
        }
        .form-group input:focus, .form-group select:focus {
            outline: none; border-color: var(--emerald, #059669);
            box-shadow: 0 0 0 3px var(--emerald-glow, rgba(5,150,105,0.2));
        }
        .btn-submit {
            width: 100%; padding: 14px;
            border: none; border-radius: 12px;
            background: var(--emerald, #059669);
            color: white; font-weight: bold; font-size: 1rem;
            cursor: pointer; margin-top: 10px; transition: all 0.3s;
            box-shadow: 0 4px 12px var(--emerald-glow, rgba(5,150,105,0.2));
        }
        .btn-submit:hover { background: var(--emerald-dark, #047857); transform: translateY(-2px); }
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
                <h2>My Savings Goals</h2>
                <p>Track your progress towards your dream items.</p>
            </div>
            <div class="my-avatar"><?php echo strtoupper(substr($username, 0, 1)); ?></div>
        </div>

        <div class="wallet-grid">
            <div class="card-list">
                <h3>My Targets</h3>
                
                <div id="cards-container">
                    <!-- Cards will be loaded here via AJAX -->
                </div>

                <button class="add-card-btn" onclick="openModal()"><i class="fas fa-plus"></i> Add New Goal</button>
            </div>

            <div class="card-list">
                <h3>Recent Activity (Savings)</h3>
                <div class="empty-state" style="margin-top:50px;">
                    <i class="fas fa-bullseye"></i>
                    <p>Keep saving! Your milestones will appear here.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Wallet Modal -->
<div class="modal-overlay" id="addWalletModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Add Savings Goal</h3>
            <button class="close-modal" onclick="closeModal()">&times;</button>
        </div>
        <form id="addWalletForm">
            <div class="form-group">
                <label>Item Name (e.g., iPhone 15 Pro)</label>
                <input type="text" name="item_name" required placeholder="Item Name">
            </div>
            <div class="form-group">
                <label>Target Amount (Rp)</label>
                <input type="number" name="target_amount" required min="1" step="0.01" placeholder="e.g., 20000000">
            </div>
            <div class="form-group">
                <label>Initial Saved Amount (Rp)</label>
                <input type="number" name="current_amount" required min="0" step="0.01" placeholder="0.00" value="0">
            </div>
            <button type="submit" class="btn-submit">Add Goal</button>
        </form>
    </div>
</div>

<script>
function openModal() {
    document.getElementById('addWalletModal').classList.add('active');
}

function closeModal() {
    document.getElementById('addWalletModal').classList.remove('active');
    document.getElementById('addWalletForm').reset();
}

// Fetch cards on page load
document.addEventListener('DOMContentLoaded', fetchWallets);

function fetchWallets() {
    fetch('api_savings.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                renderCards(data.data);
            } else {
                console.error(data.message);
            }
        })
        .catch(err => console.error('Error fetching savings:', err));
}

function renderCards(cards) {
    const container = document.getElementById('cards-container');
    container.innerHTML = '';
    
    if (cards.length === 0) {
        container.innerHTML = '<p style="text-align:center; opacity:0.7; margin-bottom:20px;">No savings goals yet. Start saving!</p>';
        return;
    }

    cards.forEach(card => {
        const target = parseFloat(card.target_amount);
        const current = parseFloat(card.current_amount);
        let percent = target > 0 ? (current / target) * 100 : 0;
        if(percent > 100) percent = 100;
        
        const currentFmt = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(current);
        const targetFmt = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(target);

        const cardHtml = `
            <div class="savings-card">
                <div class="sc-header">
                    <div class="sc-title">${card.item_name}</div>
                    <div class="sc-percent">${percent.toFixed(1)}%</div>
                </div>
                <div class="sc-progress-bg">
                    <div class="sc-progress-fill" style="width: ${percent}%;"></div>
                </div>
                <div class="sc-footer">
                    <span><strong>${currentFmt}</strong> saved</span>
                    <span>Target: ${targetFmt}</span>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', cardHtml);
    });
}

document.getElementById('addWalletForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('.btn-submit');
    submitBtn.textContent = 'Adding...';
    submitBtn.disabled = true;

    fetch('api_savings.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            closeModal();
            fetchWallets(); // Reload list
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(err => {
        console.error('Error adding goal:', err);
        alert('An error occurred.');
    })
    .finally(() => {
        submitBtn.textContent = 'Add Goal';
        submitBtn.disabled = false;
    });
});
</script>

</body>
</html>
