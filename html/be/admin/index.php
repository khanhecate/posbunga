<?php
session_start();
require_once __DIR__ . '/../config/database.php';
$db = getDB();

// Router - determine which page to load
$page = $_GET['page'] ?? 'dashboard';
$allowedPages = ['dashboard', 'produk', 'transaksi', 'order-detail', 'pelanggan', 'supplier', 'laporan', 'pengguna', 'ganti-password'];

if (!in_array($page, $allowedPages)) {
    $page = 'dashboard';
}

// Process POST/redirects BEFORE any HTML output
// This prevents "headers already sent" errors
$pageFile = __DIR__ . '/pages/' . $page . '.php';
ob_start();
include $pageFile;
$pageContent = ob_get_clean();

// If a redirect was triggered during include, it already happened via exit
// Otherwise we render the layout with captured content

$currentPage = $page;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - POS Toko Bunga</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <!-- Header -->
    <header class="admin-header">
        <div class="admin-header-inner">
            <a href="?page=dashboard" class="admin-logo">Admin Panel</a>
            <div class="admin-user-dropdown">
                <?php
                $adminAvatar = $db->prepare("SELECT foto_profil FROM users WHERE id = :id");
                $adminAvatar->execute([':id' => $_SESSION['user']['id'] ?? 0]);
                $adminAvatarFile = $adminAvatar->fetchColumn();
                $adminAvatarSrc = $adminAvatarFile ? '/be/assets/users/' . $adminAvatarFile : '/be/assets/default-avatar.png';
                ?>
                <button class="admin-user-btn" onclick="toggleAdminDropdown()">
                    <img src="<?php echo $adminAvatarSrc; ?>" alt="" style="width:22px;height:22px;border-radius:50%;object-fit:cover;vertical-align:middle;margin-right:4px;">
                    <?php echo htmlspecialchars($_SESSION['user']['username'] ?? 'Admin'); ?> ▾
                </button>
                <div class="admin-dropdown-menu" id="admin-dropdown-menu">
                    <a href="?page=ganti-password" class="admin-dropdown-item">Ganti Password</a>
                    <a href="?page=pengguna" class="admin-dropdown-item">Pengaturan Toko</a>
                    <div class="admin-dropdown-divider"></div>
                    <a href="/fe/index.php" class="admin-dropdown-item">Lihat Toko</a>
                    <a href="/fe/?page=logout" class="admin-dropdown-item admin-dropdown-logout">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <nav class="admin-nav">
                <a href="?page=dashboard" class="admin-nav-item <?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>">
                    Dashboard
                </a>
                <a href="?page=produk" class="admin-nav-item <?php echo $currentPage === 'produk' ? 'active' : ''; ?>">
                    Data Produk
                </a>
                <a href="?page=transaksi" class="admin-nav-item <?php echo in_array($currentPage, ['transaksi', 'order-detail']) ? 'active' : ''; ?>">
                    Transaksi
                </a>
                <a href="?page=pelanggan" class="admin-nav-item <?php echo $currentPage === 'pelanggan' ? 'active' : ''; ?>">
                    Pelanggan
                </a>
                <a href="?page=supplier" class="admin-nav-item <?php echo $currentPage === 'supplier' ? 'active' : ''; ?>">
                    Supplier
                </a>
                <div class="admin-nav-group">
                    <button class="admin-nav-item admin-nav-toggle <?php echo $currentPage === 'laporan' ? 'active' : ''; ?>" onclick="toggleSubMenu(this)">
                        Laporan <span class="toggle-arrow <?php echo $currentPage === 'laporan' ? 'open' : ''; ?>">&#9656;</span>
                    </button>
                    <div class="admin-sub-menu <?php echo $currentPage === 'laporan' ? 'open' : ''; ?>">
                        <?php $tab = $_GET['tab'] ?? 'overview'; ?>
                        <a href="?page=laporan&tab=overview" class="admin-sub-item <?php echo ($currentPage === 'laporan' && $tab === 'overview') ? 'active' : ''; ?>">Penjualan</a>
                        <a href="?page=laporan&tab=stok" class="admin-sub-item <?php echo ($currentPage === 'laporan' && $tab === 'stok') ? 'active' : ''; ?>">Stok & Inventaris</a>
                        <a href="?page=laporan&tab=keuangan" class="admin-sub-item <?php echo ($currentPage === 'laporan' && $tab === 'keuangan') ? 'active' : ''; ?>">Keuangan</a>
                        <a href="?page=laporan&tab=pelanggan" class="admin-sub-item <?php echo ($currentPage === 'laporan' && $tab === 'pelanggan') ? 'active' : ''; ?>">Pelanggan</a>
                        <a href="?page=laporan&tab=supplier" class="admin-sub-item <?php echo ($currentPage === 'laporan' && $tab === 'supplier') ? 'active' : ''; ?>">Supplier</a>
                        <a href="?page=laporan&tab=arsip" class="admin-sub-item <?php echo ($currentPage === 'laporan' && $tab === 'arsip') ? 'active' : ''; ?>">Arsip PDF</a>
                    </div>
                </div>
                <a href="?page=pengguna" class="admin-nav-item <?php echo $currentPage === 'pengguna' ? 'active' : ''; ?>">
                    Pengaturan Toko
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <?php echo $pageContent; ?>
        </main>
    </div>

    <script>
    function toggleAdminDropdown() {
        document.getElementById('admin-dropdown-menu').classList.toggle('show');
    }
    document.addEventListener('click', function(e) {
        const dd = document.querySelector('.admin-user-dropdown');
        if (dd && !dd.contains(e.target)) {
            document.getElementById('admin-dropdown-menu')?.classList.remove('show');
        }
    });
    function toggleSubMenu(btn) {
        const group = btn.closest('.admin-nav-group');
        const subMenu = group.querySelector('.admin-sub-menu');
        const arrow = btn.querySelector('.toggle-arrow');
        subMenu.classList.toggle('open');
        arrow.classList.toggle('open');
    }
    </script>
</body>
</html>
