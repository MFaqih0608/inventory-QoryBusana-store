<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' — ' : ''; ?>Inventory Toko Busana</title>
    <?php include_once "auth.php"; ?>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary:       #1a1a2e;
            --primary-light: #16213e;
            --accent:        #e94560;
            --accent-soft:   rgba(233,69,96,0.10);
            --surface:       #ffffff;
            --surface-2:     #f8f9fc;
            --border:        #e8eaf0;
            --text-main:     #1a1a2e;
            --text-muted:    #6b7280;
            --success:       #10b981;
            --warning:       #f59e0b;
            --danger:        #ef4444;
            --sidebar-w:     240px;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--surface-2);
            color: var(--text-main);
            margin: 0;
        }

        /* ── Sidebar ── */
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: var(--primary);
            display: flex;
            flex-direction: column;
            z-index: 100;
            transition: transform .3s ease;
        }

        .sidebar-brand {
            padding: 24px 20px 16px;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }

        .sidebar-brand .brand-icon {
            width: 38px; height: 38px;
            background: var(--accent);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; color: #fff;
            margin-bottom: 10px;
        }

        .sidebar-brand h6 {
            color: #fff;
            font-weight: 700;
            font-size: 14px;
            margin: 0;
            line-height: 1.3;
        }

        .sidebar-brand small {
            color: rgba(255,255,255,.45);
            font-size: 11px;
        }

        .sidebar-nav {
            padding: 16px 12px;
            flex: 1;
        }

        .nav-label {
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: rgba(255,255,255,.3);
            padding: 0 8px;
            margin-bottom: 6px;
        }

        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 10px;
            color: rgba(255,255,255,.6);
            font-size: 13.5px;
            font-weight: 500;
            transition: all .2s;
            margin-bottom: 2px;
            text-decoration: none;
        }

        .sidebar-nav .nav-link i {
            font-size: 16px;
            width: 20px;
            text-align: center;
        }

        .sidebar-nav .nav-link:hover,
        .sidebar-nav .nav-link.active {
            background: rgba(255,255,255,.08);
            color: #fff;
        }

        .sidebar-nav .nav-link.active {
            background: var(--accent);
            color: #fff;
        }

        /* ── Main content ── */
        .main-wrapper {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── Topbar ── */
        .topbar {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 0 28px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 90;
        }

        .topbar .page-title {
            font-size: 16px;
            font-weight: 700;
            margin: 0;
            color: var(--text-main);
        }

        .topbar .topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-sidebar-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 20px;
            color: var(--text-main);
            cursor: pointer;
        }

        /* ── Page content ── */
        .page-content {
            padding: 28px;
            flex: 1;
        }

        /* ── Cards ── */
        .card {
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: 0 1px 4px rgba(0,0,0,.04);
        }

        .card-header {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            border-radius: 14px 14px 0 0 !important;
            padding: 16px 20px;
            font-weight: 600;
            font-size: 14px;
        }

        /* ── Stat cards ── */
        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 20px;
            box-shadow: 0 1px 4px rgba(0,0,0,.04);
            transition: transform .2s, box-shadow .2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,.07);
        }

        .stat-card .stat-icon {
            width: 44px; height: 44px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px;
            margin-bottom: 14px;
        }

        .stat-card .stat-value {
            font-size: 26px;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 4px;
        }

        .stat-card .stat-label {
            font-size: 12.5px;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* ── Table ── */
        .table-custom {
            font-size: 13.5px;
        }

        .table-custom thead th {
            background: var(--surface-2);
            border-bottom: 1px solid var(--border);
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: var(--text-muted);
            padding: 12px 16px;
        }

        .table-custom tbody td {
            padding: 13px 16px;
            vertical-align: middle;
            border-bottom: 1px solid var(--border);
            color: var(--text-main);
        }

        .table-custom tbody tr:last-child td { border-bottom: none; }

        .table-custom tbody tr:hover td {
            background: var(--surface-2);
        }

        /* ── Badges ── */
        .badge-stok {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-stok.ok    { background: rgba(16,185,129,.12); color: #059669; }
        .badge-stok.low   { background: rgba(245,158,11,.12); color: #d97706; }
        .badge-stok.empty { background: rgba(239,68,68,.12);  color: #dc2626; }

        /* ── Buttons ── */
        .btn-accent {
            background: var(--accent);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 9px 18px;
            font-size: 13.5px;
            font-weight: 600;
            transition: background .2s, transform .15s;
        }

        .btn-accent:hover {
            background: #d63350;
            color: #fff;
            transform: translateY(-1px);
        }

        .btn-icon {
            width: 32px; height: 32px;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: var(--surface);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            transition: all .2s;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-icon:hover { background: var(--surface-2); }
        .btn-icon.edit  { color: #3b82f6; }
        .btn-icon.del   { color: var(--danger); }

        /* ── Forms ── */
        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 6px;
        }

        .form-control, .form-select {
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: 13.5px;
            padding: 10px 14px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: border-color .2s, box-shadow .2s;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-soft);
        }

        /* ── Alert stok habis ── */
        .alert-stok {
            background: rgba(239,68,68,.07);
            border: 1px solid rgba(239,68,68,.2);
            border-left: 4px solid var(--danger);
            border-radius: 12px;
            padding: 14px 18px;
            font-size: 13.5px;
            color: var(--text-main);
        }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .main-wrapper {
                margin-left: 0;
            }
            .btn-sidebar-toggle {
                display: block;
            }
            .page-content {
                padding: 16px;
            }
        }

        /* ── Overlay mobile ── */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.4);
            z-index: 99;
        }

        .sidebar-overlay.show { display: block; }
    </style>
</head>
<body>

<!-- Overlay mobile -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon"><i class="bi bi-bag-heart-fill"></i></div>
        <h6>Qory Busana</h6>
        <small>Sistem Inventory</small>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-label">Menu</div>
        <a href="index.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF'])=='index.php'?'active':''; ?>">
            <i class="bi bi-grid-1x2"></i> Dashboard
        </a>
        <a href="barang.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF'])=='barang.php'?'active':''; ?>">
            <i class="bi bi-box-seam"></i> Data Barang
        </a>
        <a href="tambah_barang.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF'])=='tambah_barang.php'?'active':''; ?>">
            <i class="bi bi-plus-circle"></i> Tambah Barang
        </a>

        <div class="nav-label" style="margin-top:12px;">Keuangan</div>
        <a href="keuangan.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF'])=='keuangan.php'?'active':''; ?>">
            <i class="bi bi-wallet2"></i> Pemasukan &amp; Pengeluaran
        </a>
        <a href="hutang.php" class="nav-link <?php echo in_array(basename($_SERVER['PHP_SELF']),['hutang.php','tambah_hutang.php','detail_hutang.php'])?'active':''; ?>">
            <i class="bi bi-journal-text"></i> Hutang &amp; Piutang
        </a>
        <a href="kontak.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF'])=='kontak.php'?'active':''; ?>">
            <i class="bi bi-people"></i> Kontak
        </a>
    </nav>
</aside>

<!-- Main -->
<div class="main-wrapper">
    <!-- Topbar -->
    <header class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn-sidebar-toggle" onclick="toggleSidebar()">
                <i class="bi bi-list"></i>
            </button>
            <h1 class="page-title"><?php echo isset($pageTitle) ? $pageTitle : 'Dashboard'; ?></h1>
        </div>
        <div class="topbar-right">
            <!-- Tanggal -->
            <span style="font-size:12px; color:var(--text-muted); display:flex; align-items:center; gap:5px;">
                <i class="bi bi-calendar3"></i>
                <?php echo date('d M Y'); ?>
            </span>

            <div style="width:1px; height:22px; background:var(--border);"></div>

            <!-- Avatar + nama + logout dalam satu grup -->
            <div style="display:flex; align-items:center; gap:10px; padding:4px 4px 4px 4px;
                        border:1px solid var(--border); border-radius:12px; background:var(--surface-2);">

                <!-- Avatar -->
                <div style="width:30px; height:30px; background:var(--accent); border-radius:8px;
                            display:flex; align-items:center; justify-content:center;
                            font-size:12px; color:#fff; font-weight:700; flex-shrink:0;">
                    <?php echo strtoupper(substr($_SESSION['user_nama'] ?? 'A', 0, 1)); ?>
                </div>

                <!-- Nama & role -->
                <div style="line-height:1.3; padding-right:4px;">
                    <div style="font-size:13px; font-weight:600; color:var(--text-main);">
                        <?php echo htmlspecialchars($_SESSION['user_nama'] ?? 'Admin'); ?>
                    </div>
                    <div style="font-size:11px; color:var(--text-muted);">Administrator</div>
                </div>

                <!-- Divider -->
                <div style="width:1px; height:24px; background:var(--border);"></div>

                <!-- Tombol logout ikon saja -->
                <a href="logout.php"
                   onclick="return confirm('Yakin ingin keluar dari sistem?')"
                   title="Keluar"
                   style="width:30px; height:30px; border-radius:8px; display:flex;
                          align-items:center; justify-content:center;
                          color:#ef4444; text-decoration:none; font-size:15px;
                          transition:background .15s; flex-shrink:0;"
                   onmouseover="this.style.background='rgba(239,68,68,.1)'"
                   onmouseout="this.style.background='transparent'">
                    <i class="bi bi-box-arrow-right"></i>
                </a>
            </div>
        </div>
    </header>

    <!-- Content -->
    <main class="page-content"></main>