<?php
// sidebar.php
$current = basename($_SERVER['PHP_SELF']);
?>

<style>
    /* CSS Khusus Sidebar agar Sinkron dengan Dashboard */
    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        width: var(--sidebar-w);
        height: 100vh;
        background: #0f172a;
        display: flex;
        flex-direction: column;
        z-index: 1050; /* Lebih tinggi dari overlay */
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .sidebar-brand {
        padding: 1.5rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.07);
    }

    .sidebar-brand .logo-icon {
        width: 36px;
        height: 36px;
        background: var(--primary);
        border-radius: 10px;
        display: grid;
        place-items: center;
        color: white;
    }

    .sidebar-brand span {
        font-weight: 800;
        color: white;
        font-size: 1.1rem;
        letter-spacing: -0.5px;
    }

    .sidebar-nav {
        flex: 1;
        padding: 1.25rem 0.75rem;
        overflow-y: auto;
    }

    .nav-label {
        font-size: 0.65rem;
        font-weight: 700;
        color: rgba(255, 255, 255, 0.4);
        text-transform: uppercase;
        letter-spacing: 0.1em;
        padding: 0 0.75rem 0.5rem;
    }

    .sidebar-link {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 0.85rem;
        border-radius: 12px;
        color: rgba(255, 255, 255, 0.6);
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s ease;
        margin-bottom: 4px;
    }

    .sidebar-link i {
        font-size: 1.1rem;
    }

    .sidebar-link:hover {
        background: rgba(255, 255, 255, 0.05);
        color: white;
    }

    .sidebar-link.active {
        background: var(--primary);
        color: white;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
    }

    .sidebar-footer {
        padding: 1rem 0.75rem;
        border-top: 1px solid rgba(255, 255, 255, 0.07);
    }

    .sidebar-link.logout {
        color: #fb7185;
    }

    .sidebar-link.logout:hover {
        background: rgba(225, 29, 72, 0.1);
        color: #f43f5e;
    }

    /* Logic Responsif untuk HP */
    @media (max-width: 991.98px) {
        .sidebar {
            transform: translateX(-100%); /* Sembunyi secara default di HP */
        }
        .sidebar.open {
            transform: translateX(0); /* Muncul saat class .open ditambahkan */
        }
    }
</style>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="logo-icon"><i class="bi bi-megaphone-fill"></i></div>
        <span>AspirasiKu</span>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-label">Main Menu</div>

        <a href="halaman_admin.php"
           class="sidebar-link <?= ($current == 'halaman_admin.php') ? 'active' : '' ?>">
            <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>

        <a href="laporan.php"
           class="sidebar-link <?= ($current == 'laporan.php') ? 'active' : '' ?>">
            <i class="bi bi-chat-left-text-fill"></i> Laporan
        </a>

        <div class="nav-label mt-4">Manajemen</div>

        <a href="kategori.php"
           class="sidebar-link <?= ($current == 'kategori.php') ? 'active' : '' ?>">
            <i class="bi bi-tags-fill"></i> Kategori
        </a>
        
    </nav>

    <div class="sidebar-footer">
        <a href="../logout.php" class="sidebar-link logout">
            <i class="bi bi-box-arrow-left"></i> Keluar
        </a>
    </div>
</aside>