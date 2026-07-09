<?php
// staff.php - Staff POS & Scanner Interface for CASA & CO.
session_start();

// Handle Logout directly here if requested
if (isset($_GET['logout'])) {
    unset($_SESSION['staff_id']);
    header("Location: staff.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="zh-HK">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>CASA & CO. — 門市核銷系統</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@400;500;700&family=Outfit:wght@400;600&display=swap" rel="stylesheet">
<!-- HTML5 QR Code Scanner Library -->
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<style>
  :root {
    --page: #F4F1EA; --paper: #FFFFFF; --card: #FFFFFF;
    --ink: #3A4036; --ink-soft: rgba(58, 64, 54, 0.6); 
    --line: rgba(58, 64, 54, 0.12); --line-strong: rgba(58, 64, 54, 0.25);
    --primary: #788A6E; --primary-dark: #5C6B54;
    --accent: #C4A47C; --radius-md: 16px; --radius-lg: 24px;
    --shadow-float: 0 12px 32px -8px rgba(58, 64, 54, 0.12);
  }
  
  * { margin:0; padding:0; box-sizing:border-box; font-family: 'Noto Sans TC', 'Outfit', sans-serif; }
  body { background: var(--page); color: var(--ink); -webkit-font-smoothing: antialiased; min-height: 100vh; display: flex; flex-direction: column; }
  button { cursor: pointer; border: none; font-family: inherit; }
  
  .container { width: 100%; max-width: 480px; margin: 0 auto; flex: 1; display: flex; flex-direction: column; position: relative; }

  /* Top Header */
  .header { background: var(--paper); padding: 16px 20px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--line); position: sticky; top: 0; z-index: 10; }
  .header-brand { display: flex; align-items: center; gap: 10px; font-weight: 600; font-size: 16px; }
  .header-icon { width: 32px; height: 32px; background: var(--page); color: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 16px; }
  .logout-btn { background: none; color: #C45A5A; font-size: 14px; font-weight: 500; }

  /* General Cards & Forms */
  .view-section { padding: 24px 20px; display: none; flex-direction: column; flex: 1; animation: fadeIn 0.3s ease; }
  .view-section.active { display: flex; }
  @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

  .card { background: var(--card); border-radius: var(--radius-lg); padding: 32px 24px; box-shadow: var(--shadow-float); border: 1px solid rgba(255,255,255,0.6); text-align: center; }
  .card h2 { font-size: 22px; margin-bottom: 8px; color: var(--ink); }
  .card p { font-size: 13.5px; color: var(--ink-soft); margin-bottom: 24px; line-height: 1.5; }

  .input-group { text-align: left; margin-bottom: 16px; }
  .input-group label { display: block; font-size: 12px; color: var(--ink-soft); margin-bottom: 6px; font-weight: 500; }
  .input-group input { width: 100%; padding: 14px 16px; border-radius: 8px; border: 1px solid var(--line-strong); font-size: 15px; outline: none; transition: 0.2s; }
  .input-group input:focus { border-color: var(--primary); }

  .btn { width: 100%; padding: 14px; border-radius: 100px; font-size: 15px; font-weight: 500; transition: 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px; }
  .btn-primary { background: var(--primary); color: #fff; box-shadow: 0 4px 12px rgba(120,138,110,0.3); }
  .btn-primary:hover { background: var(--primary-dark); transform: translateY(-2px); }
  .btn-outline { background: transparent; border: 1px solid var(--line-strong); color: var(--ink); }
  .btn-outline:hover { background: var(--page); }
  .btn-danger { background: #C45A5A; color: #fff; }

  /* Dashboard Action Buttons */
  .action-grid { display: flex; flex-direction: column; gap: 16px; margin-top: 10px; }
  .action-btn { background: var(--card); border: 1px solid var(--line); border-radius: var(--radius-md); padding: 24px 20px; display: flex; align-items: center; gap: 16px; box-shadow: 0 8px 24px -8px rgba(0,0,0,0.06); transition: transform 0.2s; text-align: left; }
  .action-btn:active { transform: scale(0.97); }
  .action-btn .icon { width: 56px; height: 56px; background: rgba(120,138,110,0.1); color: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 26px; flex-shrink: 0; }
  .action-btn .title { font-size: 17px; font-weight: 600; margin-bottom: 4px; color: var(--ink); }
  .action-btn .desc { font-size: 12.5px; color: var(--ink-soft); line-height: 1.4; }

  /* Scanner View */
  #qr-reader { width: 100%; border: none !important; border-radius: var(--radius-md); overflow: hidden; background: #000; }
  #qr-reader video { object-fit: cover; border-radius: var(--radius-md); }
  #qr-reader__scan_region { background: #000; }
  #qr-reader__dashboard_section_csr span { color: #fff !important; }

  /* Overlays / Modals */
  .modal-overlay { position: fixed; inset: 0; z-index: 900; background: rgba(58,64,54,0.6); backdrop-filter: blur(4px); display: none; align-items: center; justify-content: center; padding: 20px; }
  .modal-overlay.show { display: flex; animation: fadeIn 0.2s ease forwards; }
  .modal-box { background: var(--card); width: 100%; max-width: 360px; border-radius: var(--radius-lg); padding: 32px 24px; text-align: center; box-shadow: 0 30px 60px -20px rgba(0,0,0,0.3); }
  .modal-box .eyebrow { font-size: 12px; color: var(--primary); font-weight: 600; letter-spacing: 0.05em; margin-bottom: 8px; }
  .modal-box h3 { font-size: 22px; margin-bottom: 6px; }
  .modal-box .sub { font-size: 13.5px; color: var(--ink-soft); margin-bottom: 24px; line-height: 1.5; }

  .amount-input-wrapper { display: flex; align-items: center; justify-content: center; gap: 8px; font-size: 28px; font-family: 'Outfit'; font-weight: 600; border-bottom: 2px solid var(--ink); padding-bottom: 6px; margin: 0 auto 24px; width: 60%; }
  .amount-input-wrapper span { opacity: 0.5; font-size: 20px; }
  .amount-input-wrapper input { border: none; background: transparent; font-size: 32px; font-weight: 600; width: 100%; text-align: center; outline: none; color: var(--primary-dark); }

  .voucher-detail { background: var(--page); border-radius: 12px; padding: 16px; margin-bottom: 24px; text-align: left; border: 1px dashed var(--line-strong); }
  .voucher-detail .label { font-size: 11px; color: var(--ink-soft); margin-bottom: 4px; }
  .voucher-detail .val { font-size: 16px; font-weight: 600; margin-bottom: 12px; }
  .voucher-detail .val:last-child { margin-bottom: 0; }

  /* Toast */
  .toast { position: fixed; bottom: 30px; left: 50%; transform: translateX(-50%) translateY(20px); background: var(--ink); color: #fff; padding: 14px 24px; border-radius: 100px; font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 10px; opacity: 0; pointer-events: none; transition: all .4s cubic-bezier(0.2, 0.8, 0.2, 1); z-index: 1000; box-shadow: 0 10px 30px -10px rgba(0,0,0,0.3); white-space: nowrap; max-width: 90vw; }
  .toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }
</style>
</head>
<body>

<div class="container">

  <?php if (!isset($_SESSION['staff_id'])): ?>
  <!-- ================= LOGIN VIEW ================= -->
  <div class="view-section active" id="loginView" style="justify-content: center;">
    <div class="card">
      <div style="font-size: 40px; margin-bottom: 10px;">🏪</div>
      <h2>店員登入</h2>
      <p>CASA & CO. 門市核銷系統</p>
      
      <form id="staffLoginForm" onsubmit="handleLogin(event)">
        <div class="input-group">
          <label>店員帳號</label>
          <input type="text" id="loginUser" required placeholder="例如: staff">
        </div>
        <div class="input-group">
          <label>登入密碼</label>
          <input type="password" id="loginPass" required placeholder="••••••••">
        </div>
        <button type="submit" class="btn btn-primary" style="margin-top: 10px;">安全登入</button>
      </form>
    </div>
  </div>

  <?php else: ?>
  <!-- ================= LOGGED IN APP ================= -->
  
  <div class="header">
    <div class="header-brand"><div class="header-icon">🛋️</div> 店員核銷端</div>
    <button class="logout-btn" onclick="handleLogout()">登出 🚪</button>
  </div>

  <!-- Dashboard -->
  <div class="view-section active" id="dashboardView">
    <h2 style="font-size: 20px; margin-bottom: 4px;">準備就緒</h2>
    <p style="font-size: 13px; color: var(--ink-soft); margin-bottom: 20px;">請選擇您要執行的門市操作：</p>
    
    <div class="action-grid">
      <button class="action-btn" onclick="openScanner('member')">
        <div class="icon">📱</div>
        <div>
          <div class="title">掃描會員身分</div>
          <div class="desc">讀取會員專屬 QR，為客人發放消費積分或印花。</div>
        </div>
      </button>

      <button class="action-btn" onclick="openScanner('voucher')">
        <div class="icon">🎫</div>
        <div>
          <div class="title">核銷優惠卡券</div>
          <div class="desc">掃描客人的優惠券 QR 進行折扣核銷與作廢。</div>
        </div>
      </button>
    </div>

    <!-- NEW: Search by Phone/Email -->
    <div class="card" style="padding: 20px; margin-top: 10px; box-shadow: none; border: 1px dashed var(--line-strong);">
        <h3 style="font-size: 15px; margin-bottom: 12px; text-align:left;">手動搜尋會員</h3>
        <div style="display:flex; gap: 8px;">
            <input type="text" id="staffPhoneInput" placeholder="輸入手機號碼或信箱" style="flex:1; padding: 12px; border-radius: 8px; border: 1px solid var(--line-strong); outline: none;">
            <button class="btn btn-primary" style="width: auto; padding: 0 20px;" onclick="searchUserByPhone()">搜尋</button>
        </div>
    </div>
  </div>

  <!-- Scanner View -->
  <div class="view-section" id="scannerView">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
      <h2 id="scannerTitle" style="font-size: 18px;">掃描中...</h2>
      <button class="btn-outline" style="width:auto; padding: 6px 12px; border-radius: 100px; font-size: 12px;" onclick="closeScanner()">取消返回</button>
    </div>
    
    <!-- Camera Target -->
    <div id="qrReaderContainer" style="flex:1; display:flex; flex-direction:column;">
      <div id="qr-reader"></div>
      <p style="text-align:center; font-size:13px; color:var(--ink-soft); margin-top:20px;">請將鏡頭對準客人手機上的二維碼</p>
    </div>
  </div>

  <!-- Member Amount Modal -->
  <div class="modal-overlay" id="memberModal">
    <div class="modal-box">
      <div class="eyebrow">發放會員獎勵</div>
      <h3 id="targetMemberName">—</h3>
      <div class="sub">請確認並輸入客人本次的總消費金額</div>
      
      <div class="amount-input-wrapper">
        <span>HK$</span>
        <input type="number" id="purchaseAmount" placeholder="0" step="0.01">
      </div>
      
      <div style="display:flex; gap:10px;">
        <button class="btn btn-outline" style="flex:1;" onclick="closeModal('memberModal')">取消</button>
        <button class="btn btn-primary" style="flex:1;" onclick="submitMemberReward()">確認發放</button>
      </div>
    </div>
  </div>

  <!-- Voucher Redeem Modal -->
  <div class="modal-overlay" id="voucherModal">
    <div class="modal-box">
      <div class="eyebrow">確認核銷優惠券</div>
      <h3 style="color:var(--primary-dark);">即將核銷折扣</h3>
      
      <div class="voucher-detail">
        <div class="label">持有會員</div>
        <div class="val" id="vTargetMember">—</div>
        <div class="label">優惠券名稱</div>
        <div class="val" id="vTargetName" style="color:var(--primary);">—</div>
        <div class="label">券號代碼</div>
        <div class="val mono" id="vTargetCode" style="font-size:13px;">—</div>
      </div>
      
      <div style="display:flex; gap:10px;">
        <button class="btn btn-outline" style="flex:1;" onclick="closeModal('voucherModal')">取消返回</button>
        <button class="btn btn-primary" style="flex:1;" onclick="submitVoucherRedeem()">確認核銷</button>
      </div>
    </div>
  </div>

  <?php endif; ?>

</div>

<!-- Global Toast -->
<div class="toast" id="toast"><span class="dot"></span><span id="toastText"></span></div>

<script>
  let html5QrcodeScanner = null;
  let currentScanMode = ''; // 'member' or 'voucher'
  let targetUserId = null;
  let targetVoucherId = null;

  function showToast(msg) {
    const t = document.getElementById('toast');
    document.getElementById('toastText').textContent = msg;
    t.classList.add('show');
    clearTimeout(showToast._t);
    showToast._t = setTimeout(() => t.classList.remove('show'), 2800);
  }

  function switchView(id) {
    document.querySelectorAll('.view-section').forEach(el => el.classList.remove('active'));
    const target = document.getElementById(id);
    if(target) target.classList.add('active');
  }

  function openModal(id) { document.getElementById(id).classList.add('show'); }
  function closeModal(id) { document.getElementById(id).classList.remove('show'); }

  // --- API Handlers ---
  async function handleLogin(e) {
    e.preventDefault();
    const form = new FormData();
    form.append('username', document.getElementById('loginUser').value);
    form.append('password', document.getElementById('loginPass').value);
    
    try {
      const res = await fetch('api.php?action=staff_login', { method: 'POST', body: form });
      const data = await res.json();
      if (data.success) {
        window.location.reload(); // Refresh to render the logged-in dashboard
      } else {
        showToast(data.message);
      }
    } catch(err) { showToast('連線失敗'); }
  }

  async function handleLogout() {
    try {
      await fetch('api.php?action=staff_logout');
      window.location.reload();
    } catch(e) {}
  }

  // --- Scanner Logic ---
  function openScanner(mode) {
    currentScanMode = mode;
    document.getElementById('scannerTitle').textContent = mode === 'member' ? '掃描會員 QR' : '掃描優惠券 QR';
    switchView('scannerView');

    html5QrcodeScanner = new Html5QrcodeScanner("qr-reader", { fps: 10, qrbox: {width: 250, height: 250} });
    html5QrcodeScanner.render(onScanSuccess, (err) => {});
  }

  function closeScanner() {
    if (html5QrcodeScanner) {
      html5QrcodeScanner.clear().catch(e => {});
      html5QrcodeScanner = null;
    }
    switchView('dashboardView');
  }

  async function onScanSuccess(decodedText, decodedResult) {
    // Stop scanning to prevent multiple triggers
    if (html5QrcodeScanner) {
      html5QrcodeScanner.clear().catch(e => {});
      html5QrcodeScanner = null;
    }

    if (currentScanMode === 'member') {
        // Expected format: USER_QR_XXXXX OR USER_QR_XXXX|AMOUNT
        let qrStr = decodedText;
        let prefillAmount = '';
        if (decodedText.includes('|')) {
            const parts = decodedText.split('|');
            qrStr = parts[0];
            prefillAmount = parts[1];
        }
        await processMemberFetch('qr_code', qrStr, prefillAmount);

    } else if (currentScanMode === 'voucher') {
        // Expected format: pure voucher code string
        await processVoucherScan(decodedText);
    }
  }

  // Handle Manual Phone Search
  function searchUserByPhone() {
      const phone = document.getElementById('staffPhoneInput').value.trim();
      if(!phone) { showToast('請輸入號碼'); return; }
      processMemberFetch('phone', phone, '');
  }

  // Dynamic fetcher (Works for both QR string or Phone number)
  async function processMemberFetch(key, val, prefillAmount = '') {
    const form = new FormData(); 
    form.append(key, val);
    const endpoint = key === 'phone' ? 'staff_search_phone' : 'staff_get_user';

    try {
      const res = await fetch(`api.php?action=${endpoint}`, { method: 'POST', body: form });
      const data = await res.json();
      
      if (data.success) {
        targetUserId = data.user.id;
        document.getElementById('targetMemberName').textContent = data.user.name;
        document.getElementById('purchaseAmount').value = prefillAmount; // Prefill if passed
        
        switchView('dashboardView');
        openModal('memberModal');
        // Auto focus input
        setTimeout(() => document.getElementById('purchaseAmount').focus(), 300);
      } else {
        showToast(data.message);
        switchView('dashboardView');
      }
    } catch (e) {
      showToast('連線失敗');
      switchView('dashboardView');
    }
  }

  async function processVoucherScan(code) {
    const form = new FormData();
    form.append('code', code);
    
    try {
      const res = await fetch('api.php?action=staff_check_voucher', { method: 'POST', body: form });
      const data = await res.json();
      
      if (data.success) {
        targetVoucherId = data.voucher.id;
        document.getElementById('vTargetMember').textContent = data.voucher.member_name;
        document.getElementById('vTargetName').textContent = data.voucher.name;
        document.getElementById('vTargetCode').textContent = data.voucher.code;
        
        switchView('dashboardView');
        openModal('voucherModal');
      } else {
        showToast(data.message);
        switchView('dashboardView');
      }
    } catch (e) {
      showToast('連線失敗');
      switchView('dashboardView');
    }
  }

  async function submitMemberReward() {
    const amount = parseFloat(document.getElementById('purchaseAmount').value);
    if (!amount || amount <= 0) {
        showToast('請輸入有效金額'); return;
    }

    const form = new FormData();
    form.append('user_id', targetUserId);
    form.append('amount', amount);

    try {
      const res = await fetch('api.php?action=staff_add_reward', { method: 'POST', body: form });
      const data = await res.json();
      if (data.success) {
          showToast(data.message);
          closeModal('memberModal');
          document.getElementById('purchaseAmount').value = '';
      } else {
          showToast(data.message);
      }
    } catch(e) { showToast('系統錯誤'); }
  }

  async function submitVoucherRedeem() {
    const form = new FormData();
    form.append('voucher_id', targetVoucherId);

    try {
      const res = await fetch('api.php?action=staff_redeem_voucher', { method: 'POST', body: form });
      const data = await res.json();
      if (data.success) {
          showToast('✅ ' + data.message);
          closeModal('voucherModal');
      } else {
          showToast('❌ ' + data.message);
      }
    } catch(e) { showToast('系統錯誤'); }
  }

</script>
</body>
</html>