<?php
// admin.php - Admin Console for CASA & CO. Membership System
session_start();

// Handle Logout
if (isset($_GET['logout'])) {
    unset($_SESSION['admin_logged_in']);
    header("Location: admin.php");
    exit;
}

// Handle Login
$login_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';
    
    // Default Admin Credentials
    if ($user === 'admin' && $pass === 'admin') {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin.php");
        exit;
    } else {
        $login_error = '帳號或密碼錯誤 / Invalid username or password';
    }
}

// Render Login Page if not authenticated
if (empty($_SESSION['admin_logged_in'])):
?>
<!DOCTYPE html>
<html lang="zh-HK">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CASA & CO. — Admin Login</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600&family=Noto+Sans+TC:wght@400;500;700&display=swap" rel="stylesheet">
<style>
  body { font-family: 'Outfit', 'Noto Sans TC', sans-serif; background: #F4F1EA; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
  .login-box { background: #FFFFFF; padding: 40px 32px; border-radius: 24px; box-shadow: 0 20px 40px -10px rgba(58,64,54,0.15); width: 100%; max-width: 360px; text-align: center; border: 1px solid rgba(255,255,255,0.6); }
  .login-box h2 { margin: 0 0 8px; color: #3A4036; font-size: 24px; font-weight: 600; letter-spacing: 0.02em; }
  .login-box p { color: rgba(58,64,54,0.6); font-size: 13.5px; margin-bottom: 28px; }
  .input-group { text-align: left; margin-bottom: 18px; }
  .input-group label { display: block; font-size: 12px; font-weight: 500; color: rgba(58,64,54,0.6); margin-bottom: 6px; }
  .input-group input { width: 100%; padding: 12px 14px; border: 1px solid rgba(58,64,54,0.2); border-radius: 8px; outline: none; box-sizing: border-box; font-family: 'Outfit', sans-serif; font-size: 14px; color: #3A4036; transition: border-color 0.2s; }
  .input-group input:focus { border-color: #788A6E; }
  .btn { background: #788A6E; color: #fff; border: none; width: 100%; padding: 14px; border-radius: 100px; font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.2s ease; margin-top: 10px; box-shadow: 0 4px 12px rgba(120,138,110,0.3); }
  .btn:hover { background: #5C6B54; transform: translateY(-2px); box-shadow: 0 6px 16px rgba(120,138,110,0.4); }
  .err { background: rgba(196,90,90,0.1); color: #C45A5A; font-size: 13px; font-weight: 500; padding: 10px; border-radius: 8px; margin-bottom: 20px; }
  .icon-hero { width: 56px; height: 56px; border-radius: 50%; background: #F4F1EA; color: #788A6E; display: flex; align-items: center; justify-content: center; font-size: 26px; margin: 0 auto 16px; box-shadow: inset 0 2px 5px rgba(0,0,0,0.05); }
</style>
</head>
<body>
  <div class="login-box">
    <div class="icon-hero">🛋️</div>
    <h2>CASA & CO.</h2>
    <p>管理員登入 / Admin Portal</p>
    <?php if($login_error): ?><div class="err"><?= htmlspecialchars($login_error) ?></div><?php endif; ?>
    <form method="POST">
      <input type="hidden" name="login" value="1">
      <div class="input-group">
        <label>用戶名 (Username)</label>
        <input type="text" name="username" required placeholder="admin">
      </div>
      <div class="input-group">
        <label>密碼 (Password)</label>
        <input type="password" name="password" required placeholder="••••••••">
      </div>
      <button type="submit" class="btn">登入系統 Sign In</button>
    </form>
  </div>
</body>
</html>
<?php exit; endif; ?>

<!DOCTYPE html>
<html lang="zh-HK">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CASA & CO. — Admin 管理後台</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,500;0,600;1,400&family=Noto+Sans+TC:wght@400;500;700&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<style>
  :root {
    --page: #F4F1EA; --paper: #FFFFFF; --card: #FFFFFF;
    --ink: #3A4036; --ink-soft: rgba(58, 64, 54, 0.6); --ink-faint: rgba(58, 64, 54, 0.4);
    --line: rgba(58, 64, 54, 0.12); --line-strong: rgba(58, 64, 54, 0.25);
    --primary: #788A6E; --primary-dark: #5C6B54;
    --accent: #C4A47C; --accent-dark: #A38561;
    --blue: #8C9CA6; --ok: #6E8A7B; --warn: #C45A5A;
    --tier-yellow: #A38561; --tier-red: #5C6B54; --tier-black: #3A4036;
    --radius-sm: 8px; --radius-md: 16px; --radius-lg: 24px;
    --shadow-float: 0 12px 32px -8px rgba(58, 64, 54, 0.12);
  }
  * { margin:0; padding:0; box-sizing:border-box; }
  body {
    background: var(--page); color: var(--ink); font-family: 'Noto Sans TC', 'Outfit', sans-serif;
    -webkit-font-smoothing: antialiased; min-height: 100vh; overflow-x: hidden;
  }
  h1, h2, h3 { font-family: 'Lora', serif; font-weight: 500; letter-spacing: 0.02em; }
  .mono { font-family: 'Outfit', sans-serif; }
  button, input, select { font-family: inherit; }
  button { cursor: pointer; }
  ::-webkit-scrollbar { width: 8px; height: 8px; }
  ::-webkit-scrollbar-thumb { background: var(--line-strong); border-radius: 100px; }

  .admin-shell { display: flex; min-height: 100vh; position: relative; }

  /* Sidebar */
  .sidebar {
    width: 250px; flex-shrink: 0; background: var(--card); border-right: 1px solid var(--line);
    padding: 30px 20px; display: flex; flex-direction: column; position: sticky; top: 0; height: 100vh;
    transition: transform 0.3s ease;
  }
  .sidebar-brand { display: flex; align-items: center; gap: 12px; margin-bottom: 8px; }
  .sidebar-brand .icon {
    width: 40px; height: 40px; border-radius: 50%; background: var(--page); color: var(--primary);
    display: flex; align-items: center; justify-content: center; font-size: 19px; flex-shrink: 0;
  }
  .sidebar-brand .name { font-size: 18px; line-height: 1.2; color: var(--ink); }
  .sidebar-brand .sub { font-size: 10px; color: var(--ink-soft); letter-spacing: 0.1em; text-transform: uppercase; margin-top: 2px; }
  
  .sidebar-nav { margin-top: 36px; display: flex; flex-direction: column; gap: 3px; }
  .sidebar-nav button {
    display: flex; align-items: center; gap: 13px; padding: 12px 14px; border-radius: var(--radius-sm);
    border: none; background: none; text-align: left; font-size: 14px; font-weight: 500; color: var(--ink-soft);
    transition: all 0.2s ease; width: 100%;
  }
  .sidebar-nav button .ic { font-size: 17px; width: 20px; text-align: center; }
  .sidebar-nav button:hover { background: var(--page); color: var(--ink); }
  .sidebar-nav button.active { background: var(--primary); color: #fff; font-weight: 700; box-shadow: 0 6px 16px rgba(120,138,110,0.35); }
  .sidebar-foot { margin-top: auto; font-size: 11px; color: var(--ink-faint); line-height: 1.7; padding-top: 20px; border-top: 1px solid var(--line); }
  .sidebar-foot b { color: var(--ink-soft); }
  .logout-link { color: var(--warn); text-decoration: none; display: flex; align-items: center; gap: 8px; font-weight: 600; font-size: 13px; transition: opacity 0.2s; background: none; border: none; padding: 10px 0; cursor: pointer; }
  .logout-link:hover { opacity: 0.7; }
  
  .menu-toggle { display: none; background: none; border: none; font-size: 26px; color: var(--ink); cursor: pointer; padding: 0; margin-right: 14px; }
  .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 999; }

  /* Main content */
  .main { flex: 1; padding: 36px 44px 60px; max-width: 1280px; width: 100%; }
  .main-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 30px; flex-wrap: wrap; gap: 16px; }
  .header-left { display: flex; align-items: center; gap: 16px; }
  .main-header h1 { font-size: 26px; display: inline-block; margin: 0; line-height: 1.1; }
  .main-header .desc { font-size: 13px; color: var(--ink-soft); margin-top: 6px; }
  .demo-tag { font-size: 10.5px; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; background: rgba(120, 138, 110, 0.15); color: var(--primary-dark); padding: 5px 12px; border-radius: 100px; }

  .page-section { display: none; }
  .page-section.active { display: block; animation: fadeIn 0.35s ease forwards; }
  @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }

  .btn { display: inline-flex; align-items: center; justify-content: center; gap: 7px; padding: 11px 18px; border-radius: var(--radius-sm); font-size: 13px; font-weight: 500; border: 1px solid transparent; background: none; transition: all 0.2s ease; white-space: nowrap; }
  .btn-primary { background: var(--primary); color: #fff; box-shadow: 0 4px 12px rgba(120,138,110,0.3); }
  .btn-primary:hover { background: var(--primary-dark); transform: translateY(-1px); }
  .btn-outline { border-color: var(--line-strong); color: var(--ink); }
  .btn-outline:hover { background: var(--page); }
  .btn-danger { color: var(--warn); }
  .btn-danger:hover { background: rgba(196,90,90,0.08); }
  .btn-sm { padding: 7px 12px; font-size: 12px; }

  .stat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; margin-bottom: 22px; }
  .stat-card { background: var(--card); border-radius: var(--radius-md); box-shadow: var(--shadow-float); padding: 22px; }
  .stat-card .label { font-size: 12px; color: var(--ink-soft); margin-bottom: 10px; }
  .stat-card .value { font-family: 'Lora', serif; font-size: 32px; color: var(--primary-dark); line-height: 1; }
  .stat-card .sub { font-size: 11.5px; color: var(--ink-faint); margin-top: 8px; }

  .card { background: var(--card); border-radius: var(--radius-md); box-shadow: var(--shadow-float); padding: 26px; margin-bottom: 22px; }
  .card-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; flex-wrap: wrap; gap: 12px; }
  .card-head h2 { font-size: 17px; }
  .card-head .sub { font-size: 12.5px; color: var(--ink-soft); margin-top: 3px; }

  .tier-breakdown { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
  .tier-mini { border-radius: var(--radius-sm); padding: 16px; background: var(--page); }
  .tier-mini .dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; margin-right: 6px; }
  .tier-mini .tname { font-size: 13px; font-weight: 700; }
  .tier-mini .tcount { font-family: 'Lora', serif; font-size: 26px; margin-top: 8px; }
  .tier-mini .tpct { font-size: 11.5px; color: var(--ink-soft); margin-top: 2px; }

  .activity-row { display: flex; gap: 14px; align-items: flex-start; padding: 12px 0; border-bottom: 1px dashed var(--line); }
  .activity-row:last-child { border-bottom: none; }
  .activity-row .a-ic { width: 32px; height: 32px; border-radius: 50%; background: var(--page); flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 14px; }
  .activity-row .a-text { font-size: 13.5px; }
  .activity-row .a-text b { font-weight: 600; }
  .activity-row .a-time { font-size: 11.5px; color: var(--ink-faint); margin-top: 2px; }

  .table-toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; gap: 12px; flex-wrap: wrap; }
  .search-box { position: relative; flex: 1; max-width: 320px; min-width: 200px; }
  .search-box input { width: 100%; padding: 10px 14px 10px 36px; border-radius: var(--radius-sm); border: 1px solid var(--line-strong); background: #fff; font-size: 13.5px; outline: none; color: var(--ink); }
  .search-box input:focus { border-color: var(--primary); }
  .search-box .s-ic { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); opacity: 0.45; font-size: 13px; }

  table.data-table { width: 100%; border-collapse: collapse; }
  .data-table th { text-align: left; font-size: 10.5px; text-transform: uppercase; letter-spacing: 0.06em; color: var(--ink-soft); padding: 12px 14px; border-bottom: 1.5px solid var(--line); background: var(--page); }
  .data-table th:first-child { border-top-left-radius: var(--radius-sm); }
  .data-table th:last-child { border-top-right-radius: var(--radius-sm); }
  .data-table td { padding: 13px 14px; border-bottom: 1px solid var(--line); font-size: 13.5px; vertical-align: middle; }
  .data-table tr:last-child td { border-bottom: none; }
  .data-table tbody tr:hover td { background: rgba(120,138,110,0.045); }
  .data-table .member-name { font-weight: 600; }
  .data-table .muted { color: var(--ink-faint); font-size: 12px; }
  .data-table img.receipt-thumb { max-height: 48px; border-radius: 4px; border: 1px solid var(--line-strong); cursor: pointer; }

  .tier-badge { font-size: 10.5px; font-weight: 700; padding: 3px 10px; border-radius: 100px; display: inline-block; background: var(--page); border: 1px solid currentColor; }
  .status-chip { font-size: 10.5px; font-weight: 700; padding: 3px 10px; border-radius: 100px; display: inline-block; }
  .status-chip.unused, .status-chip.pending { background: rgba(110,138,123,0.15); color: var(--ok); }
  .status-chip.used, .status-chip.rejected { background: rgba(58,64,54,0.08); color: var(--ink-soft); }
  .status-chip.approved { background: rgba(196,164,124,0.18); color: var(--accent-dark); }
  .empty-state { text-align: center; padding: 40px 20px; color: var(--ink-soft); font-size: 13.5px; }

  .edit-row { display: flex; align-items: center; gap: 12px; padding: 14px 0; border-bottom: 1px solid var(--line); }
  .edit-row:last-child { border-bottom: none; }
  .edit-row .r-icon { width: 40px; height: 40px; border-radius: 50%; background: var(--page); display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; box-shadow: inset 0 2px 4px rgba(0,0,0,0.04); }
  .edit-row .r-name-input { flex: 1; border: 1px solid transparent; background: none; font-size: 14px; font-weight: 500; padding: 8px 10px; border-radius: 6px; color: var(--ink); min-width: 100px; transition: 0.2s; }
  .edit-row .r-name-input:hover, .edit-row .r-name-input:focus { border-color: var(--line-strong); background: var(--page); outline: none; }
  .edit-row .num-input { width: 86px; padding: 8px 12px; border-radius: 6px; border: 1px solid var(--line-strong); font-size: 13.5px; text-align: right; background: #fff; color: var(--ink); }
  .edit-row .num-input:focus { border-color: var(--primary); outline: none; }
  .edit-row select { padding: 8px 12px; border-radius: 6px; border: 1px solid var(--line-strong); font-size: 13.5px; background: #fff; color: var(--ink); }
  .edit-row select:focus { border-color: var(--primary); outline: none; }
  .edit-row .pct-display { font-size: 12.5px; color: var(--ink-soft); width: 44px; text-align: right; }
  .edit-row .del-btn { color: var(--line-strong); background: none; border: none; font-size: 16px; padding: 4px 6px; flex-shrink: 0; transition: color 0.2s; }
  .edit-row .del-btn:hover { color: var(--warn); }
  
  .add-row-form { display: flex; gap: 12px; align-items: center; margin-top: 14px; padding-top: 20px; border-top: 1px dashed var(--line); flex-wrap: wrap; }
  .add-row-form input, .add-row-form select { padding: 10px 14px; border-radius: 8px; border: 1px solid var(--line-strong); font-size: 13.5px; background: #fff; color: var(--ink); }
  .add-row-form input:focus, .add-row-form select:focus { border-color: var(--primary); outline: none; }

  .modal-overlay { position: fixed; inset: 0; z-index: 1000; background: rgba(58,64,54,0.35); display: flex; align-items: center; justify-content: center; padding: 24px; opacity: 0; pointer-events: none; transition: opacity 0.25s ease; }
  .modal-overlay.show { opacity: 1; pointer-events: auto; }
  .modal-box { background: var(--card); border-radius: var(--radius-lg); width: 100%; max-width: 440px; padding: 30px 28px; position: relative; box-shadow: 0 40px 80px -20px rgba(0,0,0,0.3); transform: scale(0.95) translateY(8px); transition: transform 0.25s ease; max-height: 86vh; overflow-y: auto; }
  .modal-overlay.show .modal-box { transform: scale(1) translateY(0); }
  .modal-close { position: absolute; top: 16px; right: 16px; width: 30px; height: 30px; border-radius: 50%; border: 1px solid var(--line-strong); background: none; display: flex; align-items: center; justify-content: center; color: var(--ink); transition: 0.2s; }
  .modal-close:hover { background: var(--line-strong); }
  .modal-box h3 { font-size: 19px; margin-bottom: 4px; }
  .modal-box .modal-sub { font-size: 12.5px; color: var(--ink-soft); margin-bottom: 22px; }
  
  .form-field { text-align: left; margin-bottom: 16px; }
  .form-field label { display: block; font-size: 12px; color: var(--ink-soft); margin-bottom: 6px; font-weight: 500; }
  .form-field input, .form-field select { width: 100%; padding: 11px 14px; border-radius: var(--radius-sm); border: 1px solid var(--line-strong); background: #fff; font-size: 13.5px; color: var(--ink); outline: none; transition: border-color 0.2s; }
  .form-field input:focus, .form-field select:focus { border-color: var(--primary); }
  .adjust-row { display: flex; align-items: center; gap: 10px; }
  .adjust-row button { width: 34px; height: 34px; border-radius: 8px; border: 1px solid var(--line-strong); background: #fff; font-size: 16px; color: var(--ink); flex-shrink: 0; transition: background 0.2s; }
  .adjust-row button:hover { background: var(--page); }
  .adjust-row input { text-align: center; }
  .voucher-mini-list { max-height: 140px; overflow-y: auto; border: 1px solid var(--line); border-radius: 8px; padding: 4px 10px; }
  .voucher-mini-row { display: flex; justify-content: space-between; align-items: center; padding: 8px 2px; font-size: 12.5px; border-bottom: 1px dashed var(--line); }
  .voucher-mini-row:last-child { border-bottom: none; }

  .bar-setting-row { display: flex; align-items: center; justify-content: space-between; padding: 12px 0; border-bottom: 1px dashed var(--line); }
  .bar-setting-row:last-child { border-bottom: none; }
  .bar-setting-row input[type="text"] { width: 120px; padding: 6px 10px; border-radius: 6px; border: 1px solid var(--line-strong); }

  .switch { position: relative; width: 42px; height: 24px; flex-shrink: 0; display: inline-block; }
  .switch input { display: none; }
  .switch .track { position: absolute; inset: 0; background: var(--line-strong); border-radius: 100px; transition: background 0.25s ease; cursor: pointer; }
  .switch .thumb { position: absolute; top: 2px; left: 2px; width: 20px; height: 20px; border-radius: 50%; background: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.2); transition: transform 0.25s ease; pointer-events: none; }
  .switch input:checked + .track { background: var(--primary); }
  .switch input:checked + .track + .thumb { transform: translateX(18px); }

  .toast { position: fixed; bottom: 26px; left: 50%; transform: translateX(-50%) translateY(20px); background: var(--ink); color: #fff; padding: 13px 22px; border-radius: 100px; font-size: 13px; font-weight: 500; display: flex; align-items: center; gap: 9px; opacity: 0; pointer-events: none; transition: all 0.3s ease; z-index: 1000; box-shadow: 0 20px 40px -10px rgba(0,0,0,0.35); white-space: nowrap; }
  .toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }
  .toast .dot { width: 7px; height: 7px; border-radius: 50%; background: var(--accent); flex-shrink: 0; }

  /* Fully Mobile Friendly Sidebar UI */
  @media (max-width: 900px) {
    .sidebar { position: fixed; left: 0; top: 0; z-index: 1000; transform: translateX(-100%); width: 250px; background: var(--paper); box-shadow: 4px 0 24px rgba(0,0,0,0.1); }
    .sidebar.open { transform: translateX(0); }
    .sidebar-overlay.open { display: block; }
    .menu-toggle { display: flex; align-items: center; justify-content: center; }
    .main-header h1 { font-size: 22px; }
    .main { padding: 26px 20px 50px; overflow-x: hidden; }
    
    .stat-grid { grid-template-columns: repeat(2, 1fr); }
    .tier-breakdown { grid-template-columns: 1fr; }
  }

  @media (max-width: 640px) {
    .stat-grid { grid-template-columns: 1fr 1fr; gap: 12px; }
    .stat-card { padding: 16px; }
    .stat-card .value { font-size: 26px; }
    .card { padding: 18px 16px; }
    
    .add-row-form input, .add-row-form select { flex: 1; }
    .add-row-form button { width: 100%; }

    /* ---- tables stack on mobile ---- */
    .stack-mobile thead { display: none; }
    .stack-mobile, .stack-mobile tbody, .stack-mobile tr, .stack-mobile td { display: block; width: 100%; }
    .stack-mobile tr { margin-bottom: 12px; border: 1px solid var(--line); border-radius: var(--radius-md); padding: 6px 14px; box-shadow: var(--shadow-float); background: var(--card); }
    .stack-mobile tr:last-child { margin-bottom: 0; }
    .stack-mobile td { display: flex; justify-content: space-between; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px dashed var(--line); text-align: right; white-space: normal; }
    .stack-mobile td:last-child { border-bottom: none; }
    .stack-mobile td[data-label]::before { content: attr(data-label); font-size: 11px; color: var(--ink-soft); font-weight: 600; text-align: left; flex-shrink: 0; }
    .stack-mobile td[data-label=""]::before { content: none; }
    .stack-mobile td[data-label=""] { justify-content: center; padding: 12px 0 4px; }
  }
</style>
</head>
<body>

<div class="admin-shell">

  <!-- Mobile Overlay -->
  <div class="sidebar-overlay" onclick="toggleMenu()"></div>

  <!-- Sidebar -->
  <div class="sidebar" id="adminSidebar">
    <div class="sidebar-brand">
      <div class="icon">🛋️</div>
      <div>
        <div class="name">CASA & CO.</div>
        <div class="sub">Admin Console</div>
      </div>
    </div>
    <div class="sidebar-nav" style="flex: 1; display: flex; flex-direction: column;">
      <button data-page="dashboard" class="active" onclick="switchPage('dashboard'); toggleMenu();"><span class="ic">📊</span><span>總覽儀表板</span></button>
      <button data-page="members" onclick="switchPage('members'); toggleMenu();"><span class="ic">👥</span><span>會員列表</span></button>
      <button data-page="tasks" onclick="switchPage('tasks'); toggleMenu();"><span class="ic">📌</span><span>任務審核</span></button>
      <button data-page="vouchers" onclick="switchPage('vouchers'); toggleMenu();"><span class="ic">🏷️</span><span>發出卡券記錄</span></button>
      <button data-page="rewards" onclick="switchPage('rewards'); toggleMenu();"><span class="ic">🎁</span><span>獎賞與轉盤設定</span></button>
      <button data-page="staff" onclick="switchPage('staff'); toggleMenu();"><span class="ic">💼</span><span>店員管理</span></button>
      <button data-page="settings" onclick="switchPage('settings'); toggleMenu();"><span class="ic">⚙️</span><span>系統設定</span></button>
      
      <div style="margin-top: auto; border-top: 1px dashed var(--line); padding-top: 10px;">
        <button onclick="window.location.href='?logout=1'" class="logout-link"><span class="ic">🚪</span><span>登出系統</span></button>
      </div>
    </div>
    <div class="sidebar-foot">
      登入身份：<b>Admin Console</b><br>
      資料庫：<b>MySQL Connected</b>
    </div>
  </div>

  <div class="main">

    <!-- ============ DASHBOARD ============ -->
    <div class="page-section active" id="page-dashboard">
      <div class="main-header">
        <div class="header-left">
          <button class="menu-toggle" onclick="toggleMenu()">☰</button>
          <div>
            <h1>總覽儀表板</h1>
            <div class="desc">會員系統整體表現與即時數據</div>
          </div>
        </div>
        <span class="demo-tag">System Mode: <span id="dashSystemModeLabel">Points</span></span>
      </div>

      <div class="stat-grid" id="statGrid"></div>

      <div class="card">
        <div class="card-head">
          <div>
            <h2>會員等級分佈</h2>
            <div class="sub">依累積積分自動分級</div>
          </div>
        </div>
        <div class="tier-breakdown" id="tierBreakdown"></div>
      </div>

      <div class="card">
        <div class="card-head">
          <div>
            <h2>近期動態記錄</h2>
            <div class="sub">會員活動即時紀錄</div>
          </div>
        </div>
        <div id="activityList"></div>
      </div>
    </div>

    <!-- ============ MEMBERS ============ -->
    <div class="page-section" id="page-members">
      <div class="main-header">
        <div class="header-left">
          <button class="menu-toggle" onclick="toggleMenu()">☰</button>
          <div>
            <h1>會員列表</h1>
            <div class="desc">查看所有註冊會員，調整積分/印花或手動發券</div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="table-toolbar">
          <div class="search-box">
            <span class="s-ic">🔍</span>
            <input type="text" id="memberSearch" placeholder="搜尋會員姓名或用戶名..." oninput="renderMembers()">
          </div>
          <span class="sub mono" id="memberCountLabel" style="color:var(--ink-soft); font-size:12.5px;"></span>
        </div>
        <div style="overflow-x:auto;">
          <table class="data-table stack-mobile">
            <thead>
              <tr>
                <th>姓名</th><th>用戶名</th><th>等級</th><th>積分</th><th>印花</th><th>發出卡券</th><th>入會日期</th><th>操作</th>
              </tr>
            </thead>
            <tbody id="membersTableBody"></tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ============ TASKS REVIEW ============ -->
    <div class="page-section" id="page-tasks">
      <div class="main-header">
        <div class="header-left">
          <button class="menu-toggle" onclick="toggleMenu()">☰</button>
          <div>
            <h1>額外任務審核</h1>
            <div class="desc">審核會員申請的額外獎賞任務 (如上傳照片、推薦好友)</div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-head" style="margin-bottom: 8px;">
          <div>
            <h2>額外獎賞任務設定</h2>
            <div class="sub">設定會員可執行的任務，同系統模式下不可有重複名稱的任務</div>
          </div>
        </div>
        <div id="tasksConfigAdminList">
          <!-- Dynamically populated tasks config rows -->
        </div>
        
        <form id="addTaskConfigForm" class="add-row-form" onsubmit="addTaskConfig(event)" style="display: flex; flex-direction: column; align-items: stretch; gap: 12px;">
          <div style="display: flex; gap: 12px; flex-wrap: wrap; width: 100%;">
            <input type="text" id="addTaskName" placeholder="任務名稱" style="flex: 1; min-width: 150px;" required>
            <input type="text" id="addTaskDesc" placeholder="任務說明" style="flex: 2; min-width: 200px;" required>
          </div>
          <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap; width: 100%;">
            <div style="display: flex; align-items: center; gap: 6px;">
              <span style="font-size: 13px; color: var(--ink-soft);">觸發類型</span>
              <select id="addTaskTriggerType" style="width: 160px;" onchange="toggleAddTaskTargetField()">
                <option value="manual">手動審核 (Manual)</option>
                <option value="checkin">每日簽到 (Check In)</option>
                <option value="spend_money">今日消費達標 (Today's Spend)</option>
              </select>
            </div>
            
            <div id="addTaskTargetValueGroup" style="display: none; align-items: center; gap: 6px;">
              <span style="font-size: 13px; color: var(--ink-soft);">達標金額 (HK$)</span>
              <input type="number" id="addTaskTargetValue" placeholder="例如: 5" style="width: 80px;" min="1" value="5">
            </div>

            <div style="display: flex; align-items: center; gap: 6px;">
              <span style="font-size: 13px; color: var(--ink-soft);">獎勵類型</span>
              <select id="addTaskRewardType" style="width: 100px;">
                <option value="points">積分</option>
                <option value="stamps">印花</option>
                <option value="spins">抽獎次數</option>
              </select>
            </div>

            <div style="display: flex; align-items: center; gap: 6px;">
              <span style="font-size: 13px; color: var(--ink-soft);">數量</span>
              <input type="number" id="addTaskReward" placeholder="數量" style="width: 80px;" min="1" required>
            </div>
            
            <button type="submit" class="btn btn-outline" style="border-radius: 8px; border-color: var(--line-strong); padding: 8px 16px; margin-left: auto;">+ 新增任務</button>
          </div>
        </form>
      </div>

      <div class="card">
        <h2 class="card-title">待處理任務申請</h2>
        <div style="overflow-x:auto; margin-top: 16px;">
          <table class="data-table stack-mobile">
            <thead>
              <tr>
                <th>申請時間</th><th>會員</th><th>任務名稱</th><th>狀態</th><th>預計發放獎勵</th><th>操作</th>
              </tr>
            </thead>
            <tbody id="tasksTableBody"></tbody>
          </table>
        </div>
      </div>
    </div>


    <!-- ============ VOUCHERS LIST ============ -->
    <div class="page-section" id="page-vouchers">
      <div class="main-header">
        <div class="header-left">
          <button class="menu-toggle" onclick="toggleMenu()">☰</button>
          <div>
            <h1>發出卡券記錄</h1>
            <div class="desc">查看所有發出給會員的優惠券明細及核銷狀態</div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="table-toolbar">
          <div class="search-box">
            <span class="s-ic">🔍</span>
            <input type="text" id="voucherSearch" placeholder="搜尋卡券名稱或代碼..." oninput="renderVouchers()">
          </div>
          <span class="sub mono" id="voucherCountLabel" style="color:var(--ink-soft); font-size:12.5px;"></span>
        </div>
        <div style="overflow-x:auto;">
          <table class="data-table stack-mobile">
            <thead>
              <tr><th>會員</th><th>卡券名稱</th><th>代碼</th><th>到期日</th><th>狀態</th><th>操作</th></tr>
            </thead>
            <tbody id="vouchersTableBody"></tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ============ REWARDS & WHEEL SETTINGS ============ -->
    <div class="page-section" id="page-rewards">
      <div class="main-header">
        <div class="header-left">
          <button class="menu-toggle" onclick="toggleMenu()">☰</button>
          <div>
            <h1>獎賞與轉盤設定</h1>
            <div class="desc">編輯積分兌換獎品，或調整幸運轉盤的獎品與機率</div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-head" style="margin-bottom: 8px;">
          <div>
            <h2>積分兌換獎賞</h2>
            <div class="sub">會員在「獎賞」頁面可用積分兌換的項目</div>
          </div>
        </div>
        <div id="rewardsAdminList">
          <!-- Dynamically populated rows -->
        </div>
        
        <form id="addRewardForm" class="add-row-form" onsubmit="addReward(event)">
          <input type="text" id="addRewardIcon" placeholder="Emoji" style="width: 70px;" required>
          <input type="text" id="addRewardName" placeholder="獎品名稱" style="flex: 1;" required>
          <div style="display: flex; align-items: center; gap: 8px;">
            <span style="font-size: 13px; color: var(--ink-soft);" id="rewardCostLabel">所需積分</span>
            <input type="number" id="addRewardCost" placeholder="成本" style="width: 100px;" required>
          </div>
          <button type="submit" class="btn btn-outline" style="border-radius: 8px; border-color: var(--line-strong);">+ 新增獎賞</button>
        </form>
      </div>

      <div class="card">
        <div class="card-head" style="margin-bottom: 8px;">
          <div>
            <h2>幸運轉盤獎品</h2>
            <div class="sub">權重決定中獎機率（總和不需為 100，系統會自動換算百分比）</div>
          </div>
        </div>
        <div id="wheelAdminList">
          <!-- Dynamically populated rows -->
        </div>

        <form id="addWheelForm" class="add-row-form" onsubmit="addWheelPrize(event)">
          <input type="text" id="addWheelName" placeholder="獎品名稱" style="flex: 1;" required>
          <select id="addWheelType" style="width: 100px;" onchange="handleWheelTypeChange(this.value, 'addWheelValue')">
            <!-- Dynamically populated based on system mode -->
          </select>
          <input type="number" id="addWheelValue" placeholder="數值(Value)" style="width: 80px;" oninput="enforceNumber(this, 'addWheelType')" required>
          <div style="display: flex; align-items: center; gap: 8px;">
            <span style="font-size: 13px; color: var(--ink-soft);">權重</span>
            <input type="number" id="addWheelWeight" placeholder="數值" style="width: 60px;" required>
          </div>
          <button type="submit" class="btn btn-outline" style="border-radius: 8px; border-color: var(--line-strong);">+ 新增獎品</button>
        </form>
      </div>
    </div>

    <!-- ============ STAFF MANAGEMENT ============ -->
    <div class="page-section" id="page-staff">
      <div class="main-header">
        <div class="header-left">
          <button class="menu-toggle" onclick="toggleMenu()">☰</button>
          <div>
            <h1>店員管理</h1>
            <div class="desc">管理門市店員登入核銷系統的手機帳號與密碼</div>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="table-toolbar">
          <button class="btn btn-primary" onclick="openStaffModal()">+ 新增店員帳號</button>
        </div>
        <div style="overflow-x:auto;">
          <table class="data-table stack-mobile">
            <thead>
              <tr><th>店員名稱</th><th>登入帳號</th><th>建立時間</th><th>操作</th></tr>
            </thead>
            <tbody id="staffTableBody"></tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ============ SYSTEM SETTINGS ============ -->
    <div class="page-section" id="page-settings">
      <div class="main-header">
        <div class="header-left">
          <button class="menu-toggle" onclick="toggleMenu()">☰</button>
          <div>
            <h1>系統設定</h1>
            <div class="desc">配置會員積點、動態底欄與 OTP 安全登入機制</div>
          </div>
        </div>
      </div>

      <form id="systemSettingsForm" onsubmit="saveSystemSettings(event)">
        <div class="card">
          <h2 class="card-title" style="text-align: left;">1. 核心會員機制切換</h2>
          <div class="form-field" style="margin-top: 14px;">
            <label>核心系統模式</label>
            <select id="settingSystemMode" onchange="toggleSettingsUI()">
              <option value="points">積分系統模式 (Point Loyalty System)</option>
              <option value="stamps">集印花模式 (Stamps Loyalty System)</option>
            </select>
          </div>
        </div>

        <!-- Point & Stamp Conversion Rates (NEW) -->
        <div class="card">
          <h2 class="card-title" style="text-align: left; margin-bottom: 8px;">1.5 積分與印花換算比例設定</h2>
          <div class="form-field" style="margin-top: 14px;">
            <label style="font-weight: 600; color: var(--primary);">【積分模式】換算設定 (Points System Rate)</label>
            <div style="display: flex; align-items: center; gap: 10px;">
              <span style="font-size: 13.5px;">每消費 HK$</span>
              <input type="number" id="settingPointsMoneyRate" style="width: 100px; text-align: center;" min="1" required>
              <span style="font-size: 13.5px;">可獲得</span>
              <input type="number" id="settingPointsRewardRate" style="width: 100px; text-align: center;" min="1" required>
              <span style="font-size: 13.5px;">積分 (Points)</span>
            </div>
            <p style="font-size: 11.5px; color: var(--ink-soft); margin-top: 6px;">預設為 HK$ 1 = 1 積分。不可輸入小數點。</p>
          </div>

          <div class="form-field" style="margin-top: 18px;">
            <label style="font-weight: 600; color: var(--primary);">【印花模式】換算設定 (Stamps System Rate)</label>
            <div style="display: flex; align-items: center; gap: 10px;">
              <span style="font-size: 13.5px;">每消費 HK$</span>
              <input type="number" id="settingStampsMoneyRate" style="width: 100px; text-align: center;" min="1" required>
              <span style="font-size: 13.5px;">可獲得</span>
              <input type="number" id="settingStampsRewardRate" style="width: 100px; text-align: center;" min="1" required>
              <span style="font-size: 13.5px;">個印花 (Stamps)</span>
            </div>
            <p style="font-size: 11.5px; color: var(--ink-soft); margin-top: 6px;">預設為 HK$ 100 = 1 印花。不可輸入小數點。</p>
          </div>
        </div>

        <!-- OTP Settings Card (NEW) -->
        <div class="card">
          <h2 class="card-title" style="text-align: left; margin-bottom: 8px;">2. OTP 安全驗證設定</h2>
          <div class="form-field">
            <label style="display:flex; align-items:center; gap:10px;">
              啟用 Phone/Email OTP 註冊與登入驗證
              <div class="switch"><input type="checkbox" id="settingOtpEnabled"><span class="track"></span><span class="thumb"></span></div>
            </label>
          </div>
          <div class="form-field">
            <label>OTP 驗證方式</label>
            <select id="settingOtpMethod">
              <option value="both">手機號碼或電子郵件皆可 (Both)</option>
              <option value="phone">僅限手機號碼 (Phone)</option>
              <option value="email">僅限電子郵件 (Email)</option>
            </select>
          </div>
          <div class="form-field">
            <label>登入過期天數 (超過此天數未登入，系統將要求輸入 OTP)</label>
            <input type="number" id="settingOtpExpiry" placeholder="例如: 30" min="1" value="30">
          </div>
        </div>

        <!-- Biometric Login Control Card (NEW) -->
        <div class="card">
          <h2 class="card-title" style="text-align: left; margin-bottom: 8px;">2.5 生物辨識登入控制 (Biometric Settings)</h2>
          <div class="form-field">
            <label style="display:flex; align-items:center; gap:10px;">
              啟用生物辨識登入功能 (Face ID / Touch ID)
              <div class="switch"><input type="checkbox" id="settingBiometricLoginEnabled"><span class="track"></span><span class="thumb"></span></div>
            </label>
            <p style="font-size: 11.5px; color: var(--ink-soft); margin-top: 6px;">若此功能尚在開發中，關閉此開關將隱藏所有用戶端的 Face ID / 生物辨識登入及設定選項。</p>
          </div>
        </div>

        <!-- Welcome Voucher Settings (NEW) -->
        <div class="card">
          <h2 class="card-title" style="text-align: left; margin-bottom: 8px;">2.8 新註冊迎新優惠券設定 (Welcome Voucher Settings)</h2>
          <div class="form-field">
            <label style="display:flex; align-items:center; gap:10px;">
              啟用註冊贈送迎新優惠券 (Send Welcome Voucher)
              <div class="switch"><input type="checkbox" id="settingWelcomeVoucherEnabled"><span class="track"></span><span class="thumb"></span></div>
            </label>
          </div>
          <div class="form-field">
            <label>迎新優惠券名稱 (Welcome Voucher Name)</label>
            <input type="text" id="settingWelcomeVoucherName" placeholder="例如: 全單 9 折迎新優惠">
          </div>
        </div>

        <div class="card">
          <h2 class="card-title" style="text-align: left;">3. 品牌 LOGO 配置</h2>
          <div class="form-field" style="margin-top: 14px;">
            <label>Logo 呈現類型</label>
            <select id="settingLogoType" onchange="toggleSettingsUI()">
              <option value="text">文字樣式 Logo</option>
              <option value="image">圖片網址 Logo</option>
            </select>
          </div>
          <div class="form-field" id="logoTextGroup">
            <label>Logo 顯示文字</label>
            <input type="text" id="settingLogoText" placeholder="CASA & CO.">
          </div>
          <div class="form-field" id="logoImageGroup" style="display:none;">
            <label>Logo 圖片 URL 網址</label>
            <input type="text" id="settingLogoImage" placeholder="https://example.com/logo.png">
          </div>
        </div>

        <div class="card">
          <h2 class="card-title" style="text-align: left;">4. 動態底部導航欄配置</h2>
          <p style="font-size: 12px; color: var(--ink-soft); margin-bottom: 14px;">自定義用戶端底欄的標籤、圖示及是否可見</p>
          
          <div id="bottomBarSettingsContainer">
            <!-- Dynamic bottom bar config list -->
          </div>
          <button type="button" class="btn btn-outline btn-sm" style="margin-top: 14px; border-radius: 8px;" onclick="addBottomBarTab()">+ 新增導航標籤</button>
        </div>

        <button type="submit" class="btn btn-primary" style="padding: 12px 28px; border-radius: 100px;">儲存系統設定</button>
      </form>
    </div>

  </div>
</div>

<div class="toast" id="toast"><span class="dot"></span><span id="toastText"></span></div>

<!-- ================= MODALS ================= -->

<!-- Member Edit Modal -->
<div class="modal-overlay" id="memberModal">
  <div class="modal-box">
    <button class="modal-close" onclick="closeModal('memberModal')">✕</button>
    <h3 id="memberModalName">會員帳戶調整</h3>
    <div class="modal-sub" id="memberModalMeta">—</div>

    <div class="form-field">
      <label>手動調整積分 (Points)</label>
      <div class="adjust-row">
        <button onclick="adjustField('points', -100)">−100</button>
        <input type="number" id="memberPointsInput" value="0">
        <button onclick="adjustField('points', 100)">+100</button>
      </div>
    </div>
    <div class="form-field">
      <label>手動調整印花 (Stamps, 0-10)</label>
      <div class="adjust-row">
        <button onclick="adjustField('stamps', -1)">−1</button>
        <input type="number" id="memberStampsInput" min="0" max="10" value="0">
        <button onclick="adjustField('stamps', 1)">+1</button>
      </div>
    </div>
    <div class="form-field">
      <label>手動調整抽獎次數 (Spins)</label>
      <div class="adjust-row">
        <button onclick="adjustField('spins', -1)">−1</button>
        <input type="number" id="memberSpinsInput" min="0" value="0">
        <button onclick="adjustField('spins', 1)">+1</button>
      </div>
    </div>
    <div class="form-field">
      <label>該會員持有優惠券</label>
      <div class="voucher-mini-list" id="memberVoucherMiniList"></div>
    </div>

    <button class="btn btn-primary btn-block" style="border-radius:100px; margin-top: 10px;" onclick="saveMemberAdjustment()">儲存帳戶修改</button>
  </div>
</div>

<!-- Task Review Modal -->
<div class="modal-overlay" id="taskModal">
  <div class="modal-box">
    <button class="modal-close" onclick="closeModal('taskModal')">✕</button>
    <h3>任務審核與發放</h3>
    <div class="modal-sub" id="taskModalDesc">—</div>
    
    <div class="form-field">
      <label id="taskRewardLabel">預計發放的獎勵數量</label>
      <input type="number" id="taskGrantedInput" value="2" min="0">
    </div>

    <div style="display: flex; gap: 10px;">
      <button class="btn btn-primary" style="flex: 1; border-radius: 100px;" onclick="submitTaskReview('approved')">✅ 核准發放</button>
      <button class="btn btn-danger" style="flex: 1; border-radius: 100px;" onclick="submitTaskReview('rejected')">❌ 駁回拒絕</button>
    </div>
  </div>
</div>

<!-- Edit Voucher Modal -->
<div class="modal-overlay" id="editVoucherModal">
  <div class="modal-box">
    <button class="modal-close" onclick="closeModal('editVoucherModal')">✕</button>
    <h3>修改優惠券資料</h3>
    <div class="modal-sub" id="editVoucherName">—</div>
    
    <div class="form-field">
      <label>優惠券代碼 (Code)</label>
      <input type="text" id="editVoucherCodeInput" placeholder="請輸入新代碼">
    </div>

    <div class="form-field">
      <label>有效日期 / 到期日 (Expiry Date)</label>
      <input type="date" id="editVoucherExpiryInput" style="padding: 10px; border-radius: 8px; border: 1px solid var(--line); font-size: 14px; width: 100%;">
      <div style="display: flex; gap: 6px; margin-top: 8px; flex-wrap: wrap;">
        <button type="button" class="btn btn-outline btn-sm" onclick="setExpiryPreset(1)">1個月</button>
        <button type="button" class="btn btn-outline btn-sm" onclick="setExpiryPreset(3)">3個月</button>
        <button type="button" class="btn btn-outline btn-sm" onclick="setExpiryPreset(6)">6個月</button>
        <button type="button" class="btn btn-outline btn-sm" onclick="setExpiryPreset(12)">12個月</button>
      </div>
    </div>

    <button class="btn btn-primary btn-block" style="border-radius:100px; margin-top: 14px;" onclick="saveVoucherCode()">儲存變更</button>
  </div>
</div>

<!-- Staff Modal -->
<div class="modal-overlay" id="staffModal">
  <div class="modal-box">
    <button class="modal-close" onclick="closeModal('staffModal')">✕</button>
    <h3 id="staffModalTitle">新增店員</h3>
    <div class="modal-sub">設定門市店員的手機核銷登入帳號</div>
    
    <div class="form-field">
      <label>店員名稱 (Name)</label>
      <input type="text" id="staffNameInput" placeholder="例如: 門市 A 區店員">
    </div>
    <div class="form-field">
      <label>登入帳號 (Username)</label>
      <input type="text" id="staffUsernameInput" placeholder="例如: staff01">
    </div>
    <div class="form-field">
      <label>登入密碼 (Password)</label>
      <input type="password" id="staffPasswordInput" placeholder="輸入新密碼 (編輯時留空代表不修改)">
    </div>

    <button class="btn btn-primary btn-block" style="border-radius:100px; margin-top: 10px;" onclick="saveStaff()">儲存店員資料</button>
  </div>
</div>


<!-- Manual issue voucher modal -->
<div class="modal-overlay" id="issueModal">
  <div class="modal-box">
    <button class="modal-close" onclick="closeModal('issueModal')">✕</button>
    <h3>手動發券給會員</h3>
    <div class="modal-sub" id="issueModalMemberName">—</div>

    <div class="form-field">
      <label>優惠券/卡券名稱</label>
      <input type="text" id="issueVoucherName" placeholder="例如: HK$50 聖誕特惠購物金">
    </div>
    <div class="form-field">
      <label>有效天數</label>
      <input type="number" id="issueVoucherDays" value="30" min="1">
    </div>
    <button class="btn btn-primary btn-block" style="border-radius:100px;" onclick="saveIssuedVoucher()">確認發送卡券</button>
  </div>
</div>

<!-- ================= JS LOGIC ================= -->
<script>
  let fullData = null;
  let editingMemberId = null;
  let issuingMemberId = null;
  let editingTaskId = 0;
  let editingVoucherId = 0;
  let editingStaffId = 0;

  function toggleMenu() {
    const sidebar = document.getElementById('adminSidebar');
    const overlay = document.querySelector('.sidebar-overlay');
    sidebar.classList.toggle('open');
    overlay.classList.toggle('open');
  }

  function showToast(msg) {
    const t = document.getElementById('toast');
    document.getElementById('toastText').textContent = msg;
    t.classList.add('show');
    clearTimeout(showToast._t);
    showToast._t = setTimeout(() => t.classList.remove('show'), 2600);
  }

  function openModal(id) {
    document.getElementById(id).classList.add('show');
  }
  function closeModal(id) {
    document.getElementById(id).classList.remove('show');
  }

  function switchPage(pageId) {
    document.querySelectorAll('.page-section').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.sidebar-nav button').forEach(b => b.classList.remove('active'));
    
    document.getElementById('page-' + pageId).classList.add('active');
    document.querySelector(`[data-page="${pageId}"]`).classList.add('active');
    window.scrollTo(0, 0);
  }

  async function loadAdminData() {
    try {
      const res = await fetch('api.php?action=admin_get_data');
      const text = await res.text();
      let data;
      try {
        data = JSON.parse(text);
      } catch(err) {
        console.error("API Error Response:", text);
        showToast('API 回傳格式錯誤，請按 F12 查看 Console');
        return;
      }
      
      if (data.success) {
        fullData = data;
        
        // Mock default configuration data if missing to ensure UI functions visually.
        if (!fullData.rewards) {
          fullData.rewards = [
            { id: 1, icon: '🛍️', name_zh: '純棉環保收納袋', cost: 700 },
            { id: 2, icon: '🍵', name_zh: '日式陶瓷馬克杯', cost: 900 },
            { id: 3, icon: '💳', name_zh: 'HK$50 家居購物金', cost: 1300 },
            { id: 4, icon: '🏷️', name_zh: '單件家居擺設 7 折', cost: 2000 }
          ];
        }
        if (!fullData.wheel_prizes) {
          fullData.wheel_prizes = [
            { id: 1, name_zh: '200 積分', type: 'points', weight: 20 },
            { id: 2, name_zh: '精緻藤編杯墊', type: 'voucher', weight: 12 },
            { id: 3, name_zh: '500 積分', type: 'points', weight: 15 },
            { id: 4, name_zh: 'HK$20 折扣', type: 'voucher', weight: 12 },
            { id: 5, name_zh: '再接再厲', type: 'none', weight: 20 },
            { id: 6, name_zh: '香薰蠟燭', type: 'voucher', weight: 10 }
          ];
        }

        renderDashboard();
        renderMembers();
        renderTasks();
        renderVouchers();
        renderStaff();
        populateSettingsForm();
        renderRewardsAndWheel();
      } else {
        showToast(data.message || '資料載入失敗');
      }
    } catch(e) {
      console.error("Admin Load Error:", e);
      showToast('載入管理台資料失敗: 請確認資料庫連線');
    }
  }

  function renderDashboard() {
    const stats = fullData.stats || { totalMembers: 0, totalVouchers: 0, usedVouchers: 0, totalSpins: 0 };
    const mode = fullData.settings.system_mode || 'points';
    
    document.getElementById('dashSystemModeLabel').textContent = mode === 'stamps' ? '印花模式' : '積分模式';

    document.getElementById('statGrid').innerHTML = `
      <div class="stat-card">
        <div class="label">註冊會員數</div>
        <div class="value">${stats.totalMembers}</div>
        <div class="sub">累計會員人數</div>
      </div>
      <div class="stat-card">
        <div class="label">已發放優惠券</div>
        <div class="value">${stats.totalVouchers}</div>
        <div class="sub">累計發出張數</div>
      </div>
      <div class="stat-card">
        <div class="label">已使用/核銷券</div>
        <div class="value">${stats.usedVouchers} <span style="font-size:15px; color:var(--ink-faint);">/ ${stats.totalVouchers}</span></div>
        <div class="sub">核銷率 ${stats.totalVouchers ? Math.round((stats.usedVouchers/stats.totalVouchers)*100) : 0}%</div>
      </div>
      <div class="stat-card">
        <div class="label">累積轉盤次數</div>
        <div class="value">${stats.totalSpins}</div>
        <div class="sub">幸運輪盤參與次數</div>
      </div>
    `;

    // Tier distribution
    const total = fullData.members ? fullData.members.length : 0;
    const tiers = ['yellow', 'red', 'black'];
    const tierLabels = {yellow:'亞麻會員', red:'橡木會員', black:'羊絨會員'};
    const tierColor = {yellow:'var(--tier-yellow)', red:'var(--tier-red)', black:'var(--tier-black)'};
    
    document.getElementById('tierBreakdown').innerHTML = tiers.map(t => {
      const count = fullData.members ? fullData.members.filter(m => {
        const pts = m.points;
        if(pts >= 5000) return t === 'black';
        if(pts >= 2000) return t === 'red';
        return t === 'yellow';
      }).length : 0;
      const pct = total ? Math.round((count/total)*100) : 0;
      return `
        <div class="tier-mini">
          <div><span class="dot" style="background:${tierColor[t]};"></span><span class="tname">${tierLabels[t]}</span></div>
          <div class="tcount">${count}</div>
          <div class="tpct">佔會員總數 ${pct}%</div>
        </div>`;
    }).join('');

    // Recent logs
    if (fullData.logs) {
      document.getElementById('activityList').innerHTML = fullData.logs.map(log => `
        <div class="activity-row">
          <div class="a-ic">${log.icon || '📝'}</div>
          <div>
            <div class="a-text">${log.text_zh}</div>
            <div class="a-time">${log.created_at}</div>
          </div>
        </div>
      `).join('');
    }
  }

  function renderMembers() {
    if (!fullData.members) return;
    const q = document.getElementById('memberSearch').value.trim().toLowerCase();
    const filtered = fullData.members.filter(m => m.name.toLowerCase().includes(q) || m.username.toLowerCase().includes(q));
    
    document.getElementById('memberCountLabel').textContent = `共 ${filtered.length} 位會員`;
    const tbody = document.getElementById('membersTableBody');
    tbody.innerHTML = '';

    if (filtered.length === 0) {
      tbody.innerHTML = `<tr><td colspan="8"><div class="empty-state">找不到符合的會員</div></td></tr>`;
      return;
    }

    const tierLabels = {yellow:'亞麻會員', red:'橡木會員', black:'羊絨會員'};
    const tierColor = {yellow:'var(--tier-yellow)', red:'var(--tier-red)', black:'var(--tier-black)'};

    filtered.forEach(m => {
      const pts = m.points;
      let t = 'yellow';
      if(pts >= 5000) t = 'black';
      else if(pts >= 2000) t = 'red';

      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td class="member-name" data-label="姓名">${m.name}</td>
        <td class="mono muted" data-label="用戶名">${m.username}</td>
        <td data-label="等級"><span class="tier-badge" style="color:${tierColor[t]}; border-color:${tierColor[t]};">${tierLabels[t]}</span></td>
        <td data-label="積分">${m.points.toLocaleString()}</td>
        <td data-label="印花">${m.stamps} / 10</td>
        <td data-label="發出卡券">${m.vouchers ? m.vouchers.length : 0}</td>
        <td class="muted" data-label="入會日期">${m.joined_date ? m.joined_date.slice(0, 10) : ''}</td>
        <td data-label="操作" style="display: flex; gap: 6px; justify-content: flex-end;">
          <button class="btn btn-outline btn-sm" onclick="openMemberEdit('${m.id}')">調整</button>
          <button class="btn btn-outline btn-sm" onclick="openIssueVoucher('${m.id}')">發券</button>
        </td>
      `;
      tbody.appendChild(tr);
    });
  }

  function openMemberEdit(id) {
    const m = fullData.members.find(x => x.id == id);
    if (!m) return;
    editingMemberId = id;
    
    document.getElementById('memberModalName').textContent = `${m.name} (${m.username})`;
    document.getElementById('memberModalMeta').textContent = `入會日期：${m.joined_date}`;
    document.getElementById('memberPointsInput').value = m.points;
    document.getElementById('memberStampsInput').value = m.stamps;
    document.getElementById('memberSpinsInput').value = m.spins !== undefined ? m.spins : 0;

    const voucherListEl = document.getElementById('memberVoucherMiniList');
    voucherListEl.innerHTML = (m.vouchers && m.vouchers.length) ? m.vouchers.map(v => `
      <div class="voucher-mini-row">
        <span>${v.name}</span>
        <span class="status-chip ${v.used == 1 ? 'used' : 'unused'}">${v.used == 1 ? '已使用' : '未使用'}</span>
      </div>
    `).join('') : `<div style="padding:10px 2px; font-size:12.5px; color:var(--ink-faint);">該會員尚無卡券</div>`;

    openModal('memberModal');
  }

  function adjustField(field, delta) {
    const input = document.getElementById(`member${field.charAt(0).toUpperCase() + field.slice(1)}Input`);
    let val = parseInt(input.value, 10) || 0;
    val += delta;
    if (field === 'stamps') val = Math.max(0, Math.min(10, val));
    else val = Math.max(0, val);
    input.value = val;
  }

  async function saveMemberAdjustment() {
    const pts = parseInt(document.getElementById('memberPointsInput').value, 10) || 0;
    const stamps = parseInt(document.getElementById('memberStampsInput').value, 10) || 0;
    const spins = parseInt(document.getElementById('memberSpinsInput').value, 10) || 0;

    const form = new FormData();
    form.append('member_id', editingMemberId);
    form.append('points', pts);
    form.append('stamps', stamps);
    form.append('spins', spins);

    try {
      const res = await fetch('api.php?action=admin_adjust_member', {
        method: 'POST',
        body: form
      });
      const data = await res.json();
      if (data.success) {
        showToast(data.message);
        closeModal('memberModal');
        loadAdminData();
      }
    } catch(e) {}
  }

  function openIssueVoucher(id) {
    const m = fullData.members.find(x => x.id == id);
    if (!m) return;
    issuingMemberId = id;
    document.getElementById('issueModalMemberName').textContent = `發券對象：${m.name} (${m.username})`;
    document.getElementById('issueVoucherName').value = '';
    document.getElementById('issueVoucherDays').value = 30;
    openModal('issueModal');
  }

  async function saveIssuedVoucher() {
    const name = document.getElementById('issueVoucherName').value.trim();
    const days = parseInt(document.getElementById('issueVoucherDays').value, 10) || 30;

    if (!name) {
      showToast('請輸入卡券名稱');
      return;
    }

    const form = new FormData();
    form.append('member_id', issuingMemberId);
    form.append('voucher_name', name);
    form.append('days', days);

    try {
      const res = await fetch('api.php?action=admin_issue_voucher', {
        method: 'POST',
        body: form
      });
      const data = await res.json();
      if (data.success) {
        showToast(data.message);
        closeModal('issueModal');
        loadAdminData();
      }
    } catch(e) {}
  }
  
  // Tasks process listing
  function renderTasks() {
    const tbody = document.getElementById('tasksTableBody');
    tbody.innerHTML = '';
    
    const tasks = fullData.tasks || [];
    if (tasks.length === 0) {
      tbody.innerHTML = `<tr><td colspan="6"><div class="empty-state">目前無任務審核記錄</div></td></tr>`;
      return;
    }

    const statusLabels = {pending: '待審核', approved: '已核准', rejected: '已駁回'};
    const mode = fullData.settings.system_mode || 'points';
    const currencyStr = mode === 'stamps' ? '印花' : '積分';

    tasks.forEach(t => {
      const tr = document.createElement('tr');
      const expectedReward = t.reward_amount || 0;
      const taskName = t.task_name || t.task_type;
      const taskCurrencyStr = t.reward_type === 'stamps' ? '印花' : (t.reward_type === 'spins' ? '次抽獎' : '積分');

      tr.innerHTML = `
        <td class="muted" data-label="申請時間">${t.created_at}</td>
        <td class="member-name" data-label="會員">${t.member_name}</td>
        <td data-label="任務類型">${taskName}</td>
        <td data-label="狀態"><span class="status-chip ${t.status}">${statusLabels[t.status] || t.status}</span></td>
        <td data-label="預計發放獎勵">+${expectedReward} ${taskCurrencyStr}</td>
        <td data-label="操作" style="display:flex; gap: 6px; justify-content: flex-end;">
          ${t.status === 'pending' ? `
            <button class="btn btn-outline btn-sm" style="color:var(--ok); border-color:var(--ok);" onclick="openTaskModal(${t.id}, '${taskName}', '${t.member_name}', ${expectedReward}, '${taskCurrencyStr}')">審核</button>
          ` : '—'}
        </td>
      `;
      tbody.appendChild(tr);
    });
  }

  function openTaskModal(id, taskName, memberName, expectedReward, currencyStr) {
    editingTaskId = id;
    document.getElementById('taskModalDesc').innerHTML = `審核會員 <b>${memberName}</b> 的「${taskName}」任務`;
    document.getElementById('taskRewardLabel').textContent = `預計發放的${currencyStr}數量`;
    document.getElementById('taskGrantedInput').value = expectedReward;
    openModal('taskModal');
  }

  async function submitTaskReview(status) {
    if (!confirm(status === 'approved' ? '確定要核准這個任務並發放獎勵嗎？' : '確定要駁回這個任務申請嗎？')) return;
    
    const granted = parseInt(document.getElementById('taskGrantedInput').value) || 0;
    const form = new FormData();
    form.append('task_id', editingTaskId);
    form.append('status', status);
    form.append('reward_granted', granted);

    try {
      const res = await fetch('api.php?action=admin_process_task', {
        method: 'POST',
        body: form
      });
      const data = await res.json();
      if (data.success) {
        showToast(data.message);
        closeModal('taskModal');
        loadAdminData();
      } else {
        showToast(data.message);
      }
    } catch(e) {}
  }



  // Issued vouchers list view
  function renderVouchers() {
    if (!fullData.vouchers) return;
    const q = document.getElementById('voucherSearch').value.trim().toLowerCase();
    const filtered = fullData.vouchers.filter(v => v.name.toLowerCase().includes(q) || v.code.toLowerCase().includes(q) || v.member_name.toLowerCase().includes(q));
    
    document.getElementById('voucherCountLabel').textContent = `共 ${filtered.length} 張優惠券`;
    const tbody = document.getElementById('vouchersTableBody');
    tbody.innerHTML = '';

    if (filtered.length === 0) {
      tbody.innerHTML = `<tr><td colspan="6"><div class="empty-state">無符合卡券記錄</div></td></tr>`;
      return;
    }

    filtered.forEach(v => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td class="member-name" data-label="會員">${v.member_name}</td>
        <td data-label="卡券名稱">${v.name}</td>
        <td class="mono muted" data-label="代碼">${v.code}</td>
        <td class="muted" data-label="到期日">${v.expiry_date}</td>
        <td data-label="狀態"><span class="status-chip ${v.used == 1 ? 'used' : 'unused'}">${v.used == 1 ? '已使用' : '未使用'}</span></td>
        <td data-label="操作" style="display:flex; gap:6px; justify-content:flex-end;">
          <button class="btn btn-outline btn-sm" onclick="openEditVoucher(${v.id}, '${v.name.replace(/'/g, "\\'")}', '${v.code}', '${v.expiry_date}')">編輯</button>
          ${v.used == 0 ? `<button class="btn btn-outline btn-sm" style="color:var(--ok); border-color:var(--ok);" onclick="redeemVoucherCode('${v.code}')">核銷</button>` : ''}
        </td>
      `;
      tbody.appendChild(tr);
    });
  }

  function openEditVoucher(id, name, code, expiryDate) {
    editingVoucherId = id;
    document.getElementById('editVoucherName').textContent = `卡券名稱: ${name}`;
    document.getElementById('editVoucherCodeInput').value = code;
    document.getElementById('editVoucherExpiryInput').value = expiryDate;
    openModal('editVoucherModal');
  }

  function setExpiryPreset(months) {
    const d = new Date();
    d.setMonth(d.getMonth() + months);
    const yyyy = d.getFullYear();
    const mm = String(d.getMonth() + 1).padStart(2, '0');
    const dd = String(d.getDate()).padStart(2, '0');
    document.getElementById('editVoucherExpiryInput').value = `${yyyy}-${mm}-${dd}`;
  }

  async function saveVoucherCode() {
    const code = document.getElementById('editVoucherCodeInput').value.trim();
    const expiry = document.getElementById('editVoucherExpiryInput').value;
    if(!code) { showToast('代碼不能為空'); return; }
    if(!expiry) { showToast('到期日不能為空'); return; }
    
    const form = new FormData();
    form.append('voucher_id', editingVoucherId);
    form.append('code', code);
    form.append('expiry_date', expiry);
    
    try {
      const res = await fetch('api.php?action=admin_update_voucher_code', { method:'POST', body:form });
      const data = await res.json();
      if(data.success) { 
          showToast('代碼已更新'); 
          closeModal('editVoucherModal'); 
          loadAdminData(); 
      } else { 
          showToast(data.message); 
      }
    } catch(e) {}
  }

  async function redeemVoucherCode(code) {
    if (!confirm(`確認要作廢/核銷優惠券碼「${code}」嗎？`)) return;
    
    const form = new FormData();
    form.append('voucher_code', code);
    
    try {
      const res = await fetch('api.php?action=use_voucher', {
        method: 'POST',
        body: form
      });
      const data = await res.json();
      showToast('操作成功！');
      loadAdminData();
    } catch(e) {}
  }

  // Staff Management view
  function renderStaff() {
    const tbody = document.getElementById('staffTableBody');
    tbody.innerHTML = '';
    const staff = fullData.staff || [];
    
    if (staff.length === 0) {
      tbody.innerHTML = `<tr><td colspan="4"><div class="empty-state">尚未新增任何店員帳號</div></td></tr>`;
      return;
    }
    
    staff.forEach(s => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td data-label="店員名稱" class="member-name">${s.name}</td>
        <td data-label="登入帳號" class="mono">${s.username}</td>
        <td data-label="建立時間" class="muted">${s.created_at || '—'}</td>
        <td data-label="操作" style="display:flex; gap: 6px; justify-content: flex-end;">
          <button class="btn btn-outline btn-sm" onclick="openStaffModal(${s.id})">編輯</button>
          <button class="btn btn-outline btn-sm" style="color:var(--warn); border-color:var(--warn);" onclick="deleteStaff(${s.id})">刪除</button>
        </td>
      `;
      tbody.appendChild(tr);
    });
  }

  function openStaffModal(id = 0) {
    editingStaffId = id;
    if (id > 0) {
      const s = fullData.staff.find(x => x.id == id);
      document.getElementById('staffModalTitle').textContent = '編輯店員資料';
      document.getElementById('staffNameInput').value = s.name;
      document.getElementById('staffUsernameInput').value = s.username;
      document.getElementById('staffPasswordInput').value = '';
    } else {
      document.getElementById('staffModalTitle').textContent = '新增店員';
      document.getElementById('staffNameInput').value = '';
      document.getElementById('staffUsernameInput').value = '';
      document.getElementById('staffPasswordInput').value = '';
    }
    openModal('staffModal');
  }

  async function saveStaff() {
    const name = document.getElementById('staffNameInput').value.trim();
    const username = document.getElementById('staffUsernameInput').value.trim();
    const password = document.getElementById('staffPasswordInput').value.trim();
    
    if(!name || !username) { showToast('名稱與帳號為必填'); return; }
    
    const form = new FormData();
    form.append('staff_id', editingStaffId);
    form.append('name', name);
    form.append('username', username);
    if (password) form.append('password', password);

    try {
      const res = await fetch('api.php?action=admin_save_staff', {method: 'POST', body: form});
      const data = await res.json();
      if(data.success) { 
          showToast(data.message); 
          closeModal('staffModal'); 
          loadAdminData(); 
      } else { 
          showToast(data.message); 
      }
    } catch(e) { showToast('系統錯誤'); }
  }

  async function deleteStaff(id) {
    if(!confirm('確定要永久刪除此店員帳號嗎？')) return;
    const form = new FormData(); 
    form.append('staff_id', id);
    try {
      const res = await fetch('api.php?action=admin_delete_staff', {method:'POST', body:form});
      const data = await res.json();
      if(data.success) { showToast('已刪除'); loadAdminData(); }
    } catch(e) {}
  }


  // ================= REWARDS AND WHEEL LOGIC =================

  // Switch input type and block alphabets for points/stamps
  function handleWheelTypeChange(type, valueInputId) {
      const valInput = document.getElementById(valueInputId);
      if (['points', 'stamps'].includes(type)) {
          valInput.type = 'number';
          valInput.placeholder = '數值';
          valInput.value = valInput.value.replace(/[^0-9]/g, '');
      } else {
          valInput.type = 'text';
          valInput.placeholder = '券代碼/無';
      }
  }

  function enforceNumber(inputElement, typeSelectId) {
      const type = document.getElementById(typeSelectId).value;
      if (['points', 'stamps'].includes(type)) {
          inputElement.value = inputElement.value.replace(/[^0-9]/g, ''); // Strip alphabets and non-digits
      }
  }

  function renderRewardsAndWheel() {
    const mode = fullData.settings.system_mode || 'points';
    const currencyLabel = mode === 'stamps' ? '印花' : '積分';
    const currencyType = mode === 'stamps' ? 'stamps' : 'points';

    document.getElementById('rewardCostLabel').textContent = `所需${currencyLabel}`;

    // 1. Render Point Rewards
    const rList = document.getElementById('rewardsAdminList');
    if (fullData.rewards && fullData.rewards.length > 0) {
      rList.innerHTML = fullData.rewards.map(r => `
        <div class="edit-row">
          <div class="r-icon">${r.icon}</div>
          <input type="text" class="r-name-input" value="${r.name_zh}" onchange="updateReward(${r.id}, 'name_zh', this.value)">
          <input type="number" class="num-input" value="${r.cost}" onchange="updateReward(${r.id}, 'cost', this.value)">
          <span style="font-size: 13.5px; color: var(--ink-soft); margin: 0 10px 0 8px;">${currencyLabel}</span>
          <button class="del-btn" onclick="deleteReward(${r.id})" title="刪除項目">🗑️</button>
        </div>
      `).join('');
    } else {
      rList.innerHTML = `<div class="empty-state" style="padding: 20px 0; text-align: left;">目前尚未配置${currencyLabel}兌換獎賞</div>`;
    }

    // Populate Add Wheel Type dropdown based on system mode
    document.getElementById('addWheelType').innerHTML = `
      <option value="${currencyType}">${currencyLabel}</option>
      <option value="voucher">優惠券</option>
      <option value="none">不中獎</option>
    `;

    // 2. Render Wheel Prizes & Calculate Probabilities
    const wList = document.getElementById('wheelAdminList');
    if (fullData.wheel_prizes && fullData.wheel_prizes.length > 0) {
      const totalWeight = fullData.wheel_prizes.reduce((sum, p) => sum + parseInt(p.weight || 0, 10), 0);
      
      wList.innerHTML = fullData.wheel_prizes.map(p => {
        const pct = totalWeight > 0 ? Math.round((parseInt(p.weight, 10) / totalWeight) * 100) : 0;
        const isNum = ['points', 'stamps'].includes(p.type);

        return `
        <div class="edit-row">
          <input type="text" class="r-name-input" value="${p.name_zh}" onchange="updateWheelPrize(${p.id}, 'name_zh', this.value)">
          <select id="wheelType_${p.id}" onchange="updateWheelPrize(${p.id}, 'type', this.value); handleWheelTypeChange(this.value, 'wheelVal_${p.id}');">
            <option value="${currencyType}" ${p.type === currencyType ? 'selected' : ''}>${currencyLabel}</option>
            <option value="voucher" ${p.type === 'voucher' ? 'selected' : ''}>優惠券</option>
            <option value="none" ${p.type === 'none' ? 'selected' : ''}>不中獎</option>
          </select>
          <input type="${isNum ? 'number' : 'text'}" id="wheelVal_${p.id}" class="num-input" value="${p.value || ''}" style="width:80px; margin-left:8px;" placeholder="數值" oninput="enforceNumber(this, 'wheelType_${p.id}')" onchange="updateWheelPrize(${p.id}, 'value', this.value)">
          <input type="number" class="num-input" value="${p.weight}" style="width:60px; margin-left:8px;" title="權重" onchange="updateWheelPrize(${p.id}, 'weight', this.value)">
          <div class="pct-display">${pct}%</div>
          <button class="del-btn" onclick="deleteWheelPrize(${p.id})" title="刪除項目" style="margin-left:4px;">🗑️</button>
        </div>
        `;
      }).join('');
    } else {
      wList.innerHTML = '<div class="empty-state" style="padding: 20px 0; text-align: left;">目前尚未配置幸運轉盤獎品</div>';
    }

    // 3. Render Tasks Configuration
    const tList = document.getElementById('tasksConfigAdminList');
    if (fullData.tasks_config && fullData.tasks_config.length > 0) {
      tList.innerHTML = fullData.tasks_config.map(t => {
        const typeStr = t.reward_type === 'stamps' ? '印花' : (t.reward_type === 'spins' ? '次抽獎' : '積分');
        
        let triggerLabel = '手動審核';
        if (t.task_type === 'checkin') {
          triggerLabel = t.target_value > 0 ? `每日簽到 (需消費滿 HK$ ${t.target_value})` : '每日簽到';
        }
        if (t.task_type === 'spend_money') triggerLabel = `今日消費滿 HK$ ${t.target_value}`;

        return `
        <div class="edit-row" style="flex-wrap: wrap; gap: 8px;">
          <input type="text" class="r-name-input" value="${t.name_zh}" style="flex:1; min-width: 120px;" onchange="updateTaskConfig(${t.id}, 'name_zh', this.value)" title="任務名稱">
          <input type="text" class="r-name-input" value="${t.desc_zh}" style="flex:2; min-width: 150px; font-size:12px;" onchange="updateTaskConfig(${t.id}, 'desc_zh', this.value)" title="任務說明">
          <div style="font-size:12px; background:var(--bg-faint); padding: 4px 8px; border-radius: 4px; color:var(--ink-soft); min-width:80px; text-align:center;">${triggerLabel}</div>
          <div style="font-size:13px; color:var(--ink-soft); min-width:30px; text-align:right;">${typeStr}</div>
          <input type="number" class="num-input" value="${t.reward_amount}" style="width:70px; margin-left:8px;" title="獎勵數量" onchange="updateTaskConfig(${t.id}, 'reward_amount', this.value)">
          <button class="del-btn" onclick="deleteTaskConfig(${t.id})" title="刪除項目" style="margin-left:4px;">🗑️</button>
        </div>
        `;
      }).join('');
    } else {
      tList.innerHTML = '<div class="empty-state" style="padding: 20px 0; text-align: left;">目前尚未配置任何任務</div>';
    }
  }

  // Helpers to trigger API and Debug Server Errors
  async function adminAction(action, data) {
    const form = new FormData();
    for (let key in data) form.append(key, data[key]);
    try {
      const res = await fetch(`api.php?action=${action}`, { method: 'POST', body: form });
      const text = await res.text();
      try {
          const json = JSON.parse(text);
          if(!json.success) showToast('操作失敗: ' + json.message);
          return json;
      } catch(e) {
          console.error("Backend Error Response:", text);
          // THIS IS THE DEBUG ALERT: It shows the exact SQL/PHP error from the database
          alert("後台資料庫錯誤 Backend Error (" + action + "):\n\n" + text.substring(0, 400) + "\n\n請檢查您的 api.php 和資料庫設定。");
          return { success: false, message: "Server returned non-JSON data" };
      }
    } catch(e) { 
      showToast('網路連線失敗 Network Error');
      return { success: false }; 
    }
  }

  async function updateReward(id, field, value) {
    const res = await adminAction('admin_update_reward', { id, field, value });
    if(res && res.success) {
      showToast('儲存變更成功');
      loadAdminData();
    }
  }

  async function deleteReward(id) {
    if (!confirm('確定要刪除這項獎賞嗎？')) return;
    const res = await adminAction('admin_delete_reward', { id });
    if(res && res.success) {
      showToast('獎賞已刪除');
      loadAdminData();
    }
  }

  async function addReward(e) {
    e.preventDefault();
    const newReward = {
      icon: document.getElementById('addRewardIcon').value,
      name_zh: document.getElementById('addRewardName').value,
      name_en: '', // Prevent DB insertion errors if column is required
      cost: parseInt(document.getElementById('addRewardCost').value, 10) || 0
    };
    const res = await adminAction('admin_add_reward', newReward);
    if(res && res.success) {
      e.target.reset();
      showToast('成功新增積分獎賞！');
      loadAdminData();
    }
  }

  async function updateWheelPrize(id, field, value) {
    const res = await adminAction('admin_update_wheel', { id, field, value });
    if(res && res.success) {
      showToast('儲存轉盤設定成功');
      loadAdminData();
    }
  }

  async function deleteWheelPrize(id) {
    if (!confirm('確定要刪除這項轉盤獎品嗎？')) return;
    const res = await adminAction('admin_delete_wheel', { id });
    if(res && res.success) {
      showToast('獎品已刪除');
      loadAdminData();
    }
  }

  async function addWheelPrize(e) {
    e.preventDefault();
    const newPrize = {
      name_zh: document.getElementById('addWheelName').value,
      name_en: '', // Prevent DB insertion errors if column is required
      type: document.getElementById('addWheelType').value,
      value: document.getElementById('addWheelValue').value,
      weight: parseInt(document.getElementById('addWheelWeight').value, 10) || 1,
      color: '' // Prevent DB insertion errors if column is required
    };
    const res = await adminAction('admin_add_wheel', newPrize);
    if(res && res.success) {
      e.target.reset();
      handleWheelTypeChange(document.getElementById('addWheelType').value, 'addWheelValue');
      showToast('成功新增轉盤獎品！');
      loadAdminData();
    }
  }

  async function updateTaskConfig(id, field, value) {
    const res = await adminAction('admin_update_task_config', { id, field, value });
    if(res && res.success) {
      showToast('儲存任務設定成功');
      loadAdminData();
    }
  }

  async function deleteTaskConfig(id) {
    if (!confirm('確定要刪除這項任務嗎？')) return;
    const res = await adminAction('admin_delete_task_config', { id });
    if(res && res.success) {
      showToast('任務已刪除');
      loadAdminData();
    }
  }

  function toggleAddTaskTargetField() {
    const triggerType = document.getElementById('addTaskTriggerType').value;
    const targetGroup = document.getElementById('addTaskTargetValueGroup');
    if (triggerType === 'spend_money' || triggerType === 'checkin') {
      targetGroup.style.display = 'flex';
      document.getElementById('addTaskTargetValue').required = true;
      if (triggerType === 'checkin' && !document.getElementById('addTaskTargetValue').value) {
        document.getElementById('addTaskTargetValue').value = 0;
      }
    } else {
      targetGroup.style.display = 'none';
      document.getElementById('addTaskTargetValue').required = false;
    }
  }

  async function addTaskConfig(e) {
    e.preventDefault();
    const triggerType = document.getElementById('addTaskTriggerType').value;
    const targetVal = (triggerType === 'spend_money' || triggerType === 'checkin') ? parseInt(document.getElementById('addTaskTargetValue').value, 10) : 0;

    const newTask = {
      name_zh: document.getElementById('addTaskName').value,
      desc_zh: document.getElementById('addTaskDesc').value,
      reward_type: document.getElementById('addTaskRewardType').value,
      reward_amount: parseInt(document.getElementById('addTaskReward').value, 10) || 0,
      task_type: triggerType,
      target_value: targetVal
    };
    const res = await adminAction('admin_add_task_config', newTask);
    if(res && res.success) {
      e.target.reset();
      toggleAddTaskTargetField();
      showToast('成功新增任務！');
      loadAdminData();
    }
  }

  // ================= SYSTEM SETTINGS =================
  function populateSettingsForm() {
    const settings = fullData.settings || {};
    
    document.getElementById('settingSystemMode').value = settings.system_mode || 'points';
    document.getElementById('settingLogoType').value = settings.logo_type || 'text';
    document.getElementById('settingLogoText').value = settings.logo_text || 'CASA & CO.';
    document.getElementById('settingLogoImage').value = settings.logo_image_url || '';

    // Conversion rates
    document.getElementById('settingPointsMoneyRate').value = settings.points_money_rate || '1';
    document.getElementById('settingPointsRewardRate').value = settings.points_reward_rate || '1';
    document.getElementById('settingStampsMoneyRate').value = settings.stamps_money_rate || '100';
    document.getElementById('settingStampsRewardRate').value = settings.stamps_reward_rate || '1';

    // Biometric Login enabled setting
    document.getElementById('settingBiometricLoginEnabled').checked = settings.biometric_login_enabled == '1';

    // Add missing OTP population
    document.getElementById('settingOtpEnabled').checked = (settings.otp_enabled !== undefined ? settings.otp_enabled == '1' : true);
    document.getElementById('settingOtpMethod').value = settings.otp_method || 'both';
    document.getElementById('settingOtpExpiry').value = settings.otp_expiry_days || '30';

    // Welcome voucher settings
    document.getElementById('settingWelcomeVoucherEnabled').checked = settings.welcome_voucher_enabled !== '0';
    document.getElementById('settingWelcomeVoucherName').value = settings.welcome_voucher_name || '全單 9 折迎新優惠';

    // Render Bottom bar setup rows
    let barList = [];
    try {
      barList = JSON.parse(settings.bottom_bar);
    } catch(e) {
      barList = [];
    }

    const container = document.getElementById('bottomBarSettingsContainer');
    container.innerHTML = '';

    barList.forEach((tab, index) => {
      const row = document.createElement('div');
      row.className = 'bar-setting-row';
      row.innerHTML = `
        <div style="display: flex; align-items: center; gap: 8px; flex: 1; flex-wrap: wrap;">
          <input type="text" id="barTabIcon-${index}" value="${tab.icon || ''}" placeholder="Icon" style="width: 50px;">
          <input type="text" id="barTabId-${index}" value="${tab.id}" placeholder="ID" style="width: 80px;">
          <input type="text" id="barTabZh-${index}" value="${tab.labelZh}" placeholder="中文名稱" style="flex:1; min-width:80px;">
          <input type="text" id="barTabEn-${index}" value="${tab.labelEn}" placeholder="English" style="flex:1; min-width:80px;">
        </div>
        <div style="display: flex; align-items: center; gap: 12px; margin-left: 10px;">
          <label class="switch">
            <input type="checkbox" id="barTabVisible-${index}" ${tab.visible ? 'checked' : ''}>
            <span class="track"></span><span class="thumb"></span>
          </label>
          <button type="button" class="del-btn" onclick="removeBottomBarTab(${index})">🗑️</button>
        </div>
      `;
      container.appendChild(row);
    });

    toggleSettingsUI();
  }

  function addBottomBarTab() {
    const settings = fullData.settings;
    let barList = [];
    try { barList = JSON.parse(settings.bottom_bar); } catch(e) { barList = []; }
    barList.push({ id: 'new_tab', labelZh: '新標籤', labelEn: 'New Tab', icon: '✨', visible: true });
    settings.bottom_bar = JSON.stringify(barList);
    populateSettingsForm();
  }

  function removeBottomBarTab(index) {
    if (!confirm('確定要移除此導航標籤嗎？')) return;
    const settings = fullData.settings;
    let barList = [];
    try { barList = JSON.parse(settings.bottom_bar); } catch(e) { return; }
    barList.splice(index, 1);
    settings.bottom_bar = JSON.stringify(barList);
    populateSettingsForm();
  }

  function toggleSettingsUI() {
    const logoType = document.getElementById('settingLogoType').value;
    document.getElementById('logoTextGroup').style.display = logoType === 'text' ? 'block' : 'none';
    document.getElementById('logoImageGroup').style.display = logoType === 'image' ? 'block' : 'none';
  }

  // Save Settings configuration
  async function saveSystemSettings(e) {
    e.preventDefault();
    
    const settings = fullData.settings;
    let barList = [];
    try {
      barList = JSON.parse(settings.bottom_bar);
    } catch(err) {}

    // Pull bottom bar edits
    barList.forEach((tab, index) => {
      tab.id = document.getElementById(`barTabId-${index}`).value.trim();
      tab.icon = document.getElementById(`barTabIcon-${index}`).value.trim();
      tab.labelZh = document.getElementById(`barTabZh-${index}`).value.trim();
      tab.labelEn = document.getElementById(`barTabEn-${index}`).value.trim();
      tab.visible = document.getElementById(`barTabVisible-${index}`).checked;
    });

    const pointsMoney = parseInt(document.getElementById('settingPointsMoneyRate').value, 10) || 1;
    const pointsReward = parseInt(document.getElementById('settingPointsRewardRate').value, 10) || 1;
    const stampsMoney = parseInt(document.getElementById('settingStampsMoneyRate').value, 10) || 100;
    const stampsReward = parseInt(document.getElementById('settingStampsRewardRate').value, 10) || 1;

    const form = new FormData();
    form.append('system_mode', document.getElementById('settingSystemMode').value);
    form.append('logo_type', document.getElementById('settingLogoType').value);
    form.append('logo_text', document.getElementById('settingLogoText').value.trim());
    form.append('logo_image_url', document.getElementById('settingLogoImage').value.trim());
    
    form.append('points_money_rate', pointsMoney);
    form.append('points_reward_rate', pointsReward);
    form.append('stamps_money_rate', stampsMoney);
    form.append('stamps_reward_rate', stampsReward);
    form.append('biometric_login_enabled', document.getElementById('settingBiometricLoginEnabled').checked ? '1' : '0');

    form.append('otp_enabled', document.getElementById('settingOtpEnabled').checked ? '1' : '0');
    form.append('otp_method', document.getElementById('settingOtpMethod').value);
    form.append('otp_expiry_days', document.getElementById('settingOtpExpiry').value);
    form.append('welcome_voucher_enabled', document.getElementById('settingWelcomeVoucherEnabled').checked ? '1' : '0');
    form.append('welcome_voucher_name', document.getElementById('settingWelcomeVoucherName').value.trim());
    form.append('bottom_bar', JSON.stringify(barList));

    try {
      const res = await fetch('api.php?action=admin_update_settings', {
        method: 'POST',
        body: form
      });
      const data = await res.json();
      if (data.success) {
        showToast(data.message);
        loadAdminData();
      }
    } catch(err) {
      showToast('儲存失敗');
    }
  }

  // Startup initialization
  window.addEventListener('load', () => {
    loadAdminData();
  });
</script>
</body>
</html>