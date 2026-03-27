<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }

include "koneksi.php";

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "Username dan password wajib diisi.";
    } else {
        $u = mysqli_real_escape_string($conn, $username);
        $result = mysqli_query($conn, "SELECT * FROM users WHERE username='$u' LIMIT 1");
        if ($result && mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['user_nama'] = $user['nama'];
                $_SESSION['username']  = $user['username'];
                header("Location: index.php");
                exit;
            } else {
                $error = "Password salah.";
            }
        } else {
            $error = "Username tidak ditemukan.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login — Inventory Qory Busana</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
:root{--dark:#0f0f13;--dark2:#17171d;--accent:#e94560;--light:#f7f5f0;--muted:rgba(255,255,255,.38);--border:rgba(255,255,255,.08);}
body{font-family:'DM Sans',sans-serif;background:var(--dark);min-height:100vh;display:flex;overflow:hidden;}
.panel-left{flex:1;background:var(--dark2);display:flex;flex-direction:column;justify-content:space-between;padding:48px;position:relative;overflow:hidden;}
.panel-left::before{content:'';position:absolute;inset:0;background-image:linear-gradient(rgba(233,69,96,.06) 1px,transparent 1px),linear-gradient(90deg,rgba(233,69,96,.06) 1px,transparent 1px);background-size:40px 40px;pointer-events:none;}
.panel-left::after{content:'';position:absolute;width:400px;height:400px;background:radial-gradient(circle,rgba(233,69,96,.18) 0%,transparent 70%);top:-100px;left:-100px;pointer-events:none;}
.brand{display:flex;align-items:center;gap:12px;position:relative;z-index:1;}
.brand-icon{width:42px;height:42px;background:var(--accent);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:18px;color:#fff;}
.brand-name{font-family:'DM Serif Display',serif;font-size:18px;color:#fff;}
.hero-text{position:relative;z-index:1;}
.hero-text h1{font-family:'DM Serif Display',serif;font-size:clamp(32px,4vw,48px);color:#fff;line-height:1.15;margin-bottom:16px;}
.hero-text h1 span{color:var(--accent);}
.hero-text p{font-size:15px;color:var(--muted);line-height:1.7;max-width:340px;}
.stat-row{display:flex;gap:12px;position:relative;z-index:1;flex-wrap:wrap;}
.stat-chip{background:rgba(255,255,255,.05);border:1px solid var(--border);border-radius:12px;padding:14px 18px;}
.stat-chip .val{font-size:20px;font-weight:600;color:#fff;line-height:1;margin-bottom:3px;}
.stat-chip .lbl{font-size:11.5px;color:var(--muted);}
.panel-right{width:420px;min-width:380px;background:var(--dark);display:flex;align-items:center;justify-content:center;padding:48px 40px;position:relative;}
.panel-right::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,var(--accent),transparent);}
.form-box{width:100%;max-width:340px;}
.form-header{margin-bottom:36px;}
.form-header .tag{display:inline-flex;align-items:center;gap:6px;font-size:11.5px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--accent);background:rgba(233,69,96,.1);border:1px solid rgba(233,69,96,.2);border-radius:20px;padding:4px 12px;margin-bottom:14px;}
.form-header h2{font-family:'DM Serif Display',serif;font-size:28px;color:#fff;margin-bottom:6px;}
.form-header p{font-size:13.5px;color:var(--muted);}
.err-box{background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);border-left:3px solid #ef4444;border-radius:10px;padding:11px 14px;font-size:13px;color:#fca5a5;display:flex;align-items:center;gap:8px;margin-bottom:20px;animation:shake .3s ease;}
@keyframes shake{0%,100%{transform:translateX(0);}25%{transform:translateX(-4px);}75%{transform:translateX(4px);}}
.field{margin-bottom:16px;}
.field label{display:block;font-size:12px;font-weight:600;letter-spacing:.04em;text-transform:uppercase;color:rgba(255,255,255,.5);margin-bottom:8px;}
.input-wrap{position:relative;}
.input-wrap i.ico{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,.25);font-size:15px;pointer-events:none;transition:color .2s;}
.input-wrap input{width:100%;background:rgba(255,255,255,.05);border:1px solid var(--border);border-radius:12px;padding:13px 44px;font-size:14px;font-family:'DM Sans',sans-serif;color:#fff;outline:none;transition:border-color .2s,background .2s,box-shadow .2s;}
.input-wrap input::placeholder{color:rgba(255,255,255,.2);}
.input-wrap input:focus{border-color:var(--accent);background:rgba(233,69,96,.05);box-shadow:0 0 0 3px rgba(233,69,96,.12);}
.input-wrap:focus-within i.ico{color:var(--accent);}
.btn-eye{position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;color:rgba(255,255,255,.25);cursor:pointer;font-size:15px;padding:0;transition:color .2s;}
.btn-eye:hover{color:rgba(255,255,255,.6);}
.btn-submit{width:100%;padding:14px;background:var(--accent);color:#fff;border:none;border-radius:12px;font-size:14px;font-weight:600;font-family:'DM Sans',sans-serif;cursor:pointer;transition:background .2s,transform .15s,box-shadow .2s;display:flex;align-items:center;justify-content:center;gap:8px;margin-top:8px;letter-spacing:.02em;}
.btn-submit:hover{background:#d4304d;transform:translateY(-1px);box-shadow:0 8px 24px rgba(233,69,96,.3);}
.btn-submit:active{transform:translateY(0);}
.hint{margin-top:20px;padding:12px 16px;background:rgba(255,255,255,.03);border:1px solid var(--border);border-radius:10px;text-align:center;font-size:12px;color:var(--muted);}
.hint code{background:rgba(255,255,255,.08);border-radius:5px;padding:2px 7px;font-size:12px;color:rgba(255,255,255,.7);font-family:monospace;}
.form-box>*{opacity:0;transform:translateY(12px);animation:rise .5s ease forwards;}
.form-box>*:nth-child(1){animation-delay:.05s;}
.form-box>*:nth-child(2){animation-delay:.12s;}
.form-box>*:nth-child(3){animation-delay:.19s;}
.form-box>*:nth-child(4){animation-delay:.26s;}
@keyframes rise{to{opacity:1;transform:translateY(0);}}
@media(max-width:768px){body{flex-direction:column;overflow:auto;}.panel-left{display:none;}.panel-right{width:100%;min-width:unset;min-height:100vh;}}
</style>
</head>
<body>

<div class="panel-left">
    <div class="brand">
        <div class="brand-icon"><i class="bi bi-bag-heart-fill"></i></div>
        <div class="brand-name">Qory Busana</div>
    </div>
    <div class="hero-text">
        <h1>Kelola toko<br>lebih <span>cerdas</span>,<br>lebih rapi.</h1>
        <p>Sistem inventory lengkap — stok barang, hutang piutang, dan laporan dalam satu dasbor.</p>
    </div>
    <div class="stat-row">
        <div class="stat-chip">
            <div class="val"><i class="bi bi-box-seam" style="font-size:16px;color:var(--accent);"></i></div>
            <div class="lbl">Kelola Stok</div>
        </div>
        <div class="stat-chip">
            <div class="val"><i class="bi bi-journal-text" style="font-size:16px;color:#3b82f6;"></i></div>
            <div class="lbl">Hutang &amp; Piutang</div>
        </div>
        <div class="stat-chip">
            <div class="val"><i class="bi bi-shield-lock" style="font-size:16px;color:#10b981;"></i></div>
            <div class="lbl">Aman &amp; Privat</div>
        </div>
    </div>
</div>

<div class="panel-right">
    <div class="form-box">
        <div class="form-header">
            <div class="tag"><i class="bi bi-shield-lock-fill" style="font-size:10px;"></i> Admin Only</div>
            <h2>Selamat datang</h2>
            <p>Masuk untuk mengelola toko kamu</p>
        </div>

        <?php if ($error): ?>
        <div class="err-box">
            <i class="bi bi-exclamation-circle-fill" style="font-size:14px;"></i>
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="field">
                <label>Username</label>
                <div class="input-wrap">
                    <i class="bi bi-person-fill ico"></i>
                    <input type="text" name="username" placeholder="admin"
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                           autocomplete="username" autofocus required>
                </div>
            </div>
            <div class="field">
                <label>Password</label>
                <div class="input-wrap">
                    <i class="bi bi-lock-fill ico"></i>
                    <input type="password" name="password" id="pwInput"
                           placeholder="••••••••" autocomplete="current-password" required>
                    <button type="button" class="btn-eye" onclick="togglePw()">
                        <i class="bi bi-eye" id="eyeIco"></i>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn-submit">
                <i class="bi bi-box-arrow-in-right"></i> Masuk ke Dashboard
            </button>
        </form>

        <div class="hint">
            Default &nbsp;|&nbsp; <code>admin</code> &nbsp;/&nbsp; <code>admin123</code>
        </div>
    </div>
</div>

<script>
function togglePw(){
    const i=document.getElementById('pwInput'),e=document.getElementById('eyeIco');
    if(i.type==='password'){i.type='text';e.className='bi bi-eye-slash';}
    else{i.type='password';e.className='bi bi-eye';}
}
</script>
</body>
</html>