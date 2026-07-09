<?php
// index.php - Member Portal for CASA & CO. Membership System
session_start();
?>
<!DOCTYPE html>
<html lang="zh-HK">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CASA & CO. — 家居會員系統</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,500;0,600;1,400&family=Noto+Sans+TC:wght@400;500;700&family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
<!-- Real Barcode & QR Code Generators -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.0/dist/JsBarcode.all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<style>
  :root {
    --page: #F4F1EA;       /* 溫暖的燕麥底色 */
    --paper: #FFFFFF;      /* 乾淨的純白 */
    --card: #FFFFFF;
    --ink: #3A4036;        /* 深灰綠色，代替死黑，更柔和 */
    --ink-soft: rgba(58, 64, 54, 0.6); 
    --line: rgba(58, 64, 54, 0.12); 
    --line-strong: rgba(58, 64, 54, 0.25);
    
    --primary: #788A6E;    /* 核心主色：鼠尾草綠 */
    --primary-dark: #5C6B54;
    --accent: #C4A47C;     /* 點綴色：溫暖原木/藤編色 */
    --accent-dark: #A38561;
    --blue: #8C9CA6;       /* 柔和霧霾藍 */
    --ok: #6E8A7B;
    
    --radius-sm: 8px;
    --radius-md: 16px;
    --radius-lg: 24px;
    --shadow-float: 0 12px 32px -8px rgba(58, 64, 54, 0.12); /* 3D懸浮陰影 */
  }
  
  * { margin:0; padding:0; box-sizing:border-box; }
  body {
    background: var(--page); 
    color: var(--ink); 
    font-family: 'Noto Sans TC', 'Outfit', sans-serif;
    -webkit-font-smoothing: antialiased; 
    min-height: 100vh;
  }
  
  h1, h2, h3 { 
    font-family: 'Lora', serif; 
    font-weight: 500; 
    letter-spacing: 0.02em; 
  }
  .mono { font-family: 'Outfit', sans-serif; }
  button { font-family: inherit; cursor: pointer; }
  ::-webkit-scrollbar { width:0; height:0; }

  .page-wrap { max-width: 420px; margin: 0 auto; padding: 26px 18px 70px; position: relative; }

  .btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px;
    padding: 12px 18px; border-radius: var(--radius-sm); font-size: 13px; font-weight: 500; letter-spacing: 0.02em;
    border: 1px solid transparent; background: none; transition: transform .2s ease, box-shadow .2s ease, background .2s ease;
  }
  .btn-primary { background: var(--primary); color: #fff; box-shadow: 0 4px 12px rgba(120, 138, 110, 0.3); }
  .btn-primary:hover { background: var(--primary-dark); transform: translateY(-2px); box-shadow: 0 6px 16px rgba(120, 138, 110, 0.4); }
  .btn-outline { border-color: var(--line-strong); color: var(--ink); }
  .btn-outline:hover { background: var(--page); transform: translateY(-1px); }
  .btn-block { width: 100%; }
  .btn:disabled { opacity: 0.4; cursor: not-allowed; transform: none; box-shadow: none; }

  /* ---------- Auth View ---------- */
  .auth-card {
    background: var(--card);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-float);
    padding: 32px 24px;
    text-align: center;
    border: 1px solid rgba(255,255,255,0.6);
  }
  .auth-card h2 { font-size: 24px; margin-bottom: 8px; color: var(--ink); }
  .auth-card p { font-size: 13.5px; color: var(--ink-soft); margin-bottom: 24px; }
  .auth-tabs { display: flex; background: var(--page); padding: 4px; border-radius: var(--radius-sm); margin-bottom: 20px; }
  .auth-tabs button { flex: 1; border: none; background: none; padding: 8px; font-size: 13px; font-weight: 600; color: var(--ink); opacity: 0.5; border-radius: 6px; transition: all 0.3s; }
  .auth-tabs button.active { background: #fff; opacity: 1; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }

  /* ---------- phone shell ---------- */
  .phone-shell {
    width: 100%; max-width: 390px; margin: 0 auto; background: #E8E5DF;
    border-radius: 46px; padding: 12px; box-shadow: 0 40px 90px -20px rgba(58, 64, 54, 0.25);
  }
  .phone-screen {
    position: relative; background: var(--page); border-radius: 36px; overflow: hidden;
    height: 780px; display: flex; flex-direction: column;
  }

  /* 頂部 Hero 區塊 */
  .app-hero { background: var(--paper); flex-shrink: 0; position: relative; border-bottom: 1px solid var(--line); }
  .hero-body { text-align: center; padding: 18px 24px; color: var(--ink); position: relative; }
  .hero-icon {
    width: 48px; height: 48px; border-radius: 50%; background: var(--page); color: var(--primary);
    margin: 0 auto 8px; display: flex; align-items: center; justify-content: center; font-size: 22px;
    box-shadow: inset 0 2px 5px rgba(0,0,0,0.05);
  }
  .hero-brand-img { max-height: 28px; object-fit: contain; margin: 0 auto; display: block; }
  .hero-brand { font-family: 'Lora', serif; font-size: 20px; letter-spacing: 0.05em; font-weight: 500; }
  .hero-member { font-size: 12.5px; opacity: 0.85; margin-top: 6px; display: flex; align-items: center; justify-content: center; gap: 8px; }
  
  .tier-chip {
    font-size: 10px; font-weight: 600; padding: 3px 10px; border-radius: 100px;
    background: var(--page); display: inline-block; border: 1px solid currentColor;
  }

  /* Bottom Bar */
  .bottom-bar {
    display: flex; border-top: 1px solid var(--line); background: var(--paper);
    padding: 6px 12px 14px; justify-content: space-around; align-items: center;
    flex-shrink: 0; z-index: 100;
  }
  .bottom-bar button {
    flex: 1; background: none; border: none; color: var(--ink-soft);
    font-size: 10.5px; font-weight: 500; padding: 6px 4px;
    display: flex; flex-direction: column; align-items: center; gap: 4px; transition: all 0.3s ease;
  }
  .bottom-bar button .ic { font-size: 20px; transition: transform 0.3s ease; }
  .bottom-bar button.active { color: var(--primary); }
  .bottom-bar button.active .ic { transform: translateY(-3px) scale(1.1); }
  .tab-badge {
    position: absolute; top: -4px; right: -8px; min-width: 14px; height: 14px; border-radius: 100px;
    background: #C45A5A; color: #fff; font-size: 9px; font-weight: 700; line-height: 14px; text-align: center;
    padding: 0 3px; font-family: 'Outfit', sans-serif; box-shadow: 0 0 0 2px var(--paper);
  }

  .app-content { 
    flex: 1; overflow-y: auto; padding: 16px 16px 20px; 
    scrollbar-width: none; -ms-overflow-style: none;
    min-height: 0; /* CRITICAL for flexbox scroll */
  }
  .app-content::-webkit-scrollbar { display: none; width: 0; height: 0; }

  .app-tab { display: none; }
  .app-tab.active { display: block; animation: fadeIn 0.4s ease forwards; }
  @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

  .card {
    background: var(--card); padding: 22px 20px; margin-bottom: 16px;
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-float);
    border: 1px solid rgba(255,255,255,0.6);
    transition: transform 0.3s ease;
  }
  .card:hover { transform: translateY(-2px); }
  
  .card h2.card-title {
    font-size: 15px; text-align: center; color: var(--ink); margin-bottom: 18px; position: relative;
  }
  .card h2.card-title::after {
    content: ''; display: block; width: 24px; height: 2px; background: var(--accent);
    margin: 8px auto 0; border-radius: 2px; opacity: 0.5;
  }

  /* points display */
  .points-value-row { text-align: center; margin-bottom: 4px; }
  .points-value { font-family: 'Outfit'; font-size: 48px; font-weight: 300; line-height: 1; color: var(--primary); }
  .points-label { font-size: 12px; color: var(--ink-soft); margin-top: 4px; letter-spacing: 0.05em; }
  .progress-track { height: 6px; border-radius: 10px; background: var(--page); overflow: hidden; margin: 18px 0 10px; }
  .progress-fill { height: 100%; background: var(--primary); border-radius: 10px; transition: width .8s cubic-bezier(0.2, 0.8, 0.2, 1); }
  .progress-caption { text-align: center; font-size: 12.5px; color: var(--ink-soft); }
  .progress-caption b { color: var(--primary); font-weight: 600; }

  /* stamps display */
  .stamp-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 10px; margin-bottom: 14px; }
  .stamp-slot {
    aspect-ratio: 1; border-radius: 50%; border: 1.5px solid var(--line-strong);
    display: flex; align-items: center; justify-content: center; font-size: 14px; color: var(--line-strong);
    background: var(--page); transition: all 0.3s ease;
  }
  .stamp-slot.filled { 
    border-color: var(--primary); background: var(--primary); color: #fff; 
    box-shadow: 0 4px 10px rgba(120, 138, 110, 0.3);
  }
  .stamp-slot.filled svg { width: 18px; height: 18px; }
  .stamp-slot.gift { border-color: var(--accent); border-style: dashed; background: transparent; }
  .stamp-slot.gift.filled { background: var(--accent); border-style: solid; color: #fff; box-shadow: 0 4px 10px rgba(196, 164, 124, 0.3); }
  .stamp-caption { text-align: center; font-size: 13px; color: var(--ink-soft); margin-top: 8px; }

  /* reward grid */
  .reward-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px; }
  .reward-card {
    background: var(--page); padding: 18px 14px; border-radius: var(--radius-sm);
    display: flex; flex-direction: column; align-items: center; text-align: center; gap: 10px;
    border: 1px solid var(--line); transition: transform 0.3s ease, box-shadow 0.3s ease;
  }
  .reward-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-float); background: #fff; border-color: transparent; }
  .reward-card .r-icon {
    width: 64px; height: 64px; border-radius: 50%; background: #fff; 
    display: flex; align-items: center; justify-content: center; font-size: 28px;
    box-shadow: 0 4px 12px rgba(58, 64, 54, 0.08); margin-bottom: 4px;
  }
  .reward-card .r-name { font-size: 13px; font-weight: 500; line-height: 1.4; min-height: 36px; display: flex; align-items: center; justify-content: center; }
  .reward-card .r-cost { font-size: 11px; color: var(--ink-soft); font-family: 'Outfit'; }
  .reward-card .r-action { width: 100%; margin-top: 4px; }
  .reward-card .claim-btn { width: 100%; border-radius: 100px; }

  .claim-btn {
    padding: 8px 16px; font-size: 12px; font-weight: 500; letter-spacing: 0.02em;
    border: 1px solid var(--primary); background: var(--primary); color: #fff; cursor: pointer; transition: all 0.2s ease;
  }
  .claim-btn:hover:not(:disabled) { background: var(--primary-dark); box-shadow: 0 4px 10px rgba(120,138,110,0.3); }
  .claim-btn:disabled { border-color: var(--line-strong); background: transparent; color: var(--ink-soft); cursor: not-allowed; }

  /* CTA card */
  .cta-card { background: var(--ink); color: var(--paper); border: none; }
  .cta-card h3 { font-size: 18px; margin-bottom: 8px; color: var(--paper); text-align: center; }
  .cta-card p { font-size: 13px; opacity: 0.8; line-height: 1.6; text-align: center; margin-bottom: 16px; }
  .cta-card .btn-primary { background: var(--paper); color: var(--ink); width: 100%; box-shadow: none; }
  .cta-card .btn-primary:hover { background: var(--page); transform: translateY(-2px); }

  /* ---------- membership tab ---------- */
  .member-card {
    background: linear-gradient(135deg, #788A6E 0%, #5C6B54 100%); 
    color: #FFF; padding: 24px; border-radius: var(--radius-md);
    position: relative; overflow: hidden; box-shadow: 0 16px 32px -10px rgba(92, 107, 84, 0.4);
    margin-bottom: 16px;
  }
  .member-card::before {
    content:''; position:absolute; top:-40px; right:-40px; width:120px; height:120px;
    border-radius:50%; background:rgba(255,255,255,0.1);
  }
  .member-card-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 32px; position: relative; }
  .member-card-top .label { font-size: 11px; opacity: 0.8; letter-spacing: 0.05em; font-family: 'Outfit'; font-weight: 600; text-transform: uppercase; }
  .member-card-top .name { font-family: 'Lora', serif; font-size: 28px; margin-top: 4px; font-weight: 500; }
  .member-card-top .tier-chip { border-color: rgba(255,255,255,0.4); color: #FFF; background: rgba(255,255,255,0.1); backdrop-filter: blur(4px); }
  .member-card-foot { display: flex; justify-content: space-between; align-items: flex-end; position: relative; }
  .member-card-foot .barcode { flex: 1; margin-right: 16px; opacity: 1; }
  .member-card-foot .barcode svg { width: 100%; max-width: 170px; height: 38px; display: block; }
  .member-card-foot .since { font-size: 11px; opacity: 0.8; text-align: right; font-family: 'Outfit'; font-weight: 500; }

  .tier-progress-caption { text-align: center; font-size: 13px; color: var(--ink-soft); margin-bottom: 24px; }
  .tier-progress-caption b { color: var(--primary); font-weight: 600; }

  .tier-list-item { display: flex; gap: 14px; padding: 16px 0; border-bottom: 1px solid var(--line); align-items: flex-start; }
  .tier-list-item:last-child { border-bottom: none; }
  .tier-dot { width: 10px; height: 10px; border-radius: 50%; background: var(--line-strong); margin-top: 6px; flex-shrink: 0; transition: background 0.3s; }
  .tier-list-item.current .tier-dot { background: var(--accent); box-shadow: 0 0 0 4px rgba(196, 164, 124, 0.2); }
  .tier-list-item .tname { font-size: 15px; font-weight: 600; display: flex; align-items: center; gap: 8px; font-family: 'Lora', serif; color: var(--ink); }
  .tier-list-item .trange { font-size: 12px; color: var(--ink-soft); margin: 4px 0 8px; font-family: 'Outfit'; }
  .tier-list-item .tperks { font-size: 13px; color: var(--ink-soft); line-height: 1.6; }
  .current-chip { font-size: 10px; font-weight: 600; background: var(--accent); color: #fff; padding: 2px 8px; border-radius: 100px; font-family: 'Noto Sans TC', sans-serif;}

  /* ---------- prize wheel tab ---------- */
  .spins-banner {
    display: flex; align-items: center; justify-content: space-between;
    background: var(--page); border-radius: var(--radius-sm); padding: 16px 20px; margin-bottom: 24px;
  }
  .spins-banner .lbl { font-size: 14px; font-weight: 500; }
  .spins-banner .val { font-size: 22px; font-weight: 600; color: var(--primary); font-family: 'Outfit'; }
  
  .wheel-wrap { position: relative; width: 260px; height: 260px; margin: 0 auto 30px; filter: drop-shadow(0 12px 24px rgba(0,0,0,0.06)); }
  .wheel-pointer {
    position: absolute; top: -8px; left: 50%; transform: translateX(-50%); z-index: 5;
    width: 0; height: 0; border-left: 14px solid transparent; border-right: 14px solid transparent;
    border-top: 24px solid var(--ink); filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
  }
  .wheel {
    width: 100%; height: 100%; border-radius: 50%; border: 6px solid #FFF; box-shadow: inset 0 0 0 4px var(--line);
    position: relative; transition: transform 4.2s cubic-bezier(0.15,0.68,0.14,0.99); overflow: hidden;
  }
  .wheel-labels { position: absolute; inset: 0; }
  .wheel-label {
    position: absolute; top: 50%; left: 50%; width: 80px; margin-left: -40px; margin-top: -10px;
    text-align: center; font-size: 12px; font-weight: 600; color: #fff; font-family: 'Noto Sans TC';
    text-shadow: 0 1px 3px rgba(0,0,0,0.2); pointer-events: none; line-height: 1.3;
  }
  .wheel-hub {
    position: absolute; top: 50%; left: 50%; width: 50px; height: 50px; margin: -25px 0 0 -25px;
    border-radius: 50%; background: #FFF; border: 4px solid var(--page); box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    display: flex; align-items: center; justify-content: center; font-size: 20px; z-index: 4;
  }
  .spin-btn-row { display: flex; justify-content: center; }
  .spin-btn-row button { padding: 14px 32px; border-radius: 100px; font-size: 15px; }
  
  .prize-list { margin-top: 30px; padding-top: 20px; border-top: 1px dashed var(--line); }
  .prize-list-title { font-size: 13px; color: var(--ink-soft); margin-bottom: 14px; text-align: center; }
  .prize-chip-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; }
  .prize-chip {
    background: var(--page); border-radius: var(--radius-sm); padding: 10px 8px; 
    text-align: center; font-size: 12px; color: var(--ink);
  }

  /* ---------- coupons tab ---------- */
  .voucher-card {
    display: flex; align-items: stretch; background: var(--page); border-radius: var(--radius-md); overflow: hidden;
    margin-bottom: 14px; border: 1px solid var(--line); transition: transform 0.3s ease;
  }
  .voucher-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-float); }
  .voucher-card.used { opacity: 0.5; filter: grayscale(1); }
  .voucher-stub {
    width: 60px; flex-shrink: 0; background: var(--accent); color: #fff;
    display: flex; align-items: center; justify-content: center; font-size: 24px;
    border-right: 2px dashed rgba(255,255,255,0.5);
  }
  .voucher-body { flex: 1; padding: 16px; background: #FFF; }
  .voucher-body .vname { font-size: 15px; font-weight: 500; margin-bottom: 4px; color: var(--ink); }
  .voucher-body .vmeta { font-size: 12px; color: var(--ink-soft); margin-bottom: 12px; }
  .voucher-body .vcode { font-size: 12px; font-weight: 500; letter-spacing: 0.05em; color: var(--primary); margin-bottom: 10px; font-family: 'Outfit'; background: var(--page); padding: 4px 8px; border-radius: 4px; display: inline-block; }
  
  .voucher-empty { text-align: center; padding: 40px 20px; color: var(--ink-soft); }
  .voucher-empty .ic { font-size: 36px; margin-bottom: 16px; opacity: 0.8; }
  .voucher-empty p { font-size: 14px; line-height: 1.6; margin-bottom: 24px; }

  /* ---------- profile tab ---------- */
  .profile-head { text-align: center; padding: 10px 0 22px; }
  .profile-avatar {
    width: 84px; height: 84px; border-radius: 50%; margin: 0 auto 16px; background: var(--primary);
    border: 3px solid #fff; box-shadow: var(--shadow-float);
    display: flex; align-items: center; justify-content: center; font-size: 38px; color: #fff;
    font-weight: 600; font-family: 'Outfit', sans-serif;
  }
  .profile-name { font-family: 'Lora', serif; font-size: 22px; font-weight: 500; margin-bottom: 10px; }
  .profile-tier-chip {
    display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600;
    padding: 6px 16px; border-radius: 100px; background: rgba(120,138,110,0.12); color: var(--primary-dark);
  }
  .profile-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 18px; }
  .profile-stat-card {
    background: var(--card); border-radius: var(--radius-md); box-shadow: var(--shadow-float);
    padding: 16px 8px; text-align: center;
  }
  .profile-stat-card .v { font-family: 'Outfit'; font-size: 20px; font-weight: 600; color: var(--primary-dark); }
  .profile-stat-card .l { font-size: 10.5px; color: var(--ink-soft); margin-top: 4px; line-height: 1.3; }

  .settings-row {
    display: flex; align-items: center; gap: 14px; padding: 14px 0; border-bottom: 1px solid var(--line);
  }
  .settings-row:last-child { border-bottom: none; }
  .settings-row .s-ic {
    width: 34px; height: 34px; border-radius: 50%; background: var(--page); flex-shrink: 0;
    display: flex; align-items: center; justify-content: center; font-size: 15px;
  }
  .settings-row .s-label { flex: 1; font-size: 14px; font-weight: 500; }

  .switch { position: relative; width: 46px; height: 27px; flex-shrink: 0; }
  .switch input { display: none; }
  .switch .track {
    position: absolute; inset: 0; background: var(--line-strong); border-radius: 100px; transition: background 0.25s ease;
  }
  .switch .thumb {
    position: absolute; top: 2.5px; left: 3px; width: 22px; height: 22px; border-radius: 50%; background: #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2); transition: transform 0.25s ease;
  }
  .switch input:checked + .track { background: var(--primary); }
  .switch input:checked + .track + .thumb { transform: translateX(19px); }

  .account-row {
    display: flex; align-items: center; gap: 14px; padding: 15px 0; border-bottom: 1px solid var(--line);
    background: none; border-left: none; border-right: none; border-top: none; width: 100%; text-align: left;
  }
  .account-row:last-child { border-bottom: none; }
  .account-row .a-ic { font-size: 17px; width: 24px; text-align: center; flex-shrink: 0; color: var(--ink-soft); }
  .account-row .a-label { font-size: 14px; font-weight: 500; color: var(--ink); }
  .account-row .a-sub { display: block; font-size: 11.5px; color: var(--ink-soft); margin-top: 2px; }
  .account-row .a-chev { color: var(--ink-soft); opacity: 0.6; }

  .form-field { text-align: left; margin-bottom: 16px; }
  .form-field label { display: block; font-size: 12px; color: var(--ink-soft); margin-bottom: 6px; font-weight: 500; }
  .form-field input, .form-field select {
    width: 100%; padding: 12px 14px; border-radius: var(--radius-sm); border: 1px solid var(--line-strong);
    background: #fff; font-family: 'Outfit', sans-serif; font-size: 14px; color: var(--ink); outline: none;
  }
  .form-field input:focus, .form-field select:focus { border-color: var(--primary); }

  /* floating language toggle button in bottom-right corner */
  .floating-lang {
    position: fixed; bottom: 84px; right: 20px; z-index: 800;
    width: 44px; height: 44px; border-radius: 50%; background: var(--paper);
    box-shadow: 0 4px 16px rgba(58, 64, 54, 0.2); border: 1.5px solid var(--line-strong);
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 700; color: var(--ink);
    transition: all 0.3s cubic-bezier(0.2, 0.8, 0.2, 1);
  }
  .floating-lang:active { transform: scale(0.9); background: var(--page); }

  /* toast */
  .toast {
    position: fixed; bottom: 30px; left: 50%; transform: translateX(-50%) translateY(20px);
    background: var(--ink); color: #fff; padding: 14px 24px; border-radius: 100px;
    font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 10px;
    opacity: 0; pointer-events: none; transition: all .4s cubic-bezier(0.2, 0.8, 0.2, 1); z-index: 999;
    box-shadow: 0 10px 30px -10px rgba(0,0,0,0.3); white-space: nowrap; max-width: 90vw;
  }
  .toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }
  .toast .dot { width: 8px; height: 8px; border-radius: 50%; background: var(--accent); flex-shrink: 0; }

  /* modal & overlays */
  .modal-overlay {
    position: fixed; inset: 0; z-index: 900; background: rgba(58, 64, 54, 0.6); backdrop-filter: blur(4px);
    display: flex; align-items: center; justify-content: center; padding: 24px;
    opacity: 0; pointer-events: none; transition: opacity .3s ease;
  }
  .modal-overlay.show { opacity: 1; pointer-events: auto; }
  .redeem-modal {
    background: var(--card); width: 100%; max-width: 340px; border-radius: var(--radius-lg);
    padding: 36px 28px 28px; text-align: center; position: relative;
    transform: scale(0.95) translateY(10px); transition: transform .3s cubic-bezier(0.2, 0.8, 0.2, 1);
    box-shadow: 0 30px 60px -20px rgba(0,0,0,0.3);
  }
  .modal-overlay.show .redeem-modal { transform: scale(1) translateY(0); }
  .modal-close {
    position: absolute; top: 16px; right: 16px; width: 32px; height: 32px; border-radius: 50%;
    background: var(--page); border: none; display: flex; align-items: center; justify-content: center; color: var(--ink-soft); transition: all 0.2s;
  }
  .modal-close:hover { background: var(--line-strong); color: var(--ink); }
  
  .modal-eyebrow { font-size: 12px; font-weight: 500; letter-spacing: 0.05em; color: var(--primary); margin-bottom: 8px; }
  .redeem-modal h3 { font-size: 22px; margin-bottom: 8px; }
  .redeem-modal .sub { font-size: 13.5px; color: var(--ink-soft); line-height: 1.5; margin-bottom: 24px; }
  .redeem-code-box { background: var(--page); border-radius: var(--radius-md); padding: 24px 16px; margin-bottom: 20px; display: flex; flex-direction: column; align-items: center; }
  .real-qr { width: 140px; height: 140px; margin: 0 auto 16px; }

  .scan-viewfinder {
    position: relative; width: 190px; height: 130px; margin: 0 auto 18px; background: var(--page);
    border-radius: 14px; display: flex; align-items: center; justify-content: center;
  }
  .scan-corner { position: absolute; width: 22px; height: 22px; border-color: var(--primary); }
  .scan-corner.tl { top: 10px; left: 10px; border-top: 3px solid; border-left: 3px solid; border-radius: 6px 0 0 0; }
  .scan-corner.tr { top: 10px; right: 10px; border-top: 3px solid; border-right: 3px solid; border-radius: 0 6px 0 0; }
  .scan-corner.bl { bottom: 10px; left: 10px; border-bottom: 3px solid; border-left: 3px solid; border-radius: 0 0 0 6px; }
  .scan-corner.br { bottom: 10px; right: 10px; border-bottom: 3px solid; border-right: 3px solid; border-radius: 0 0 6px 0; }
  .scan-icon { font-size: 40px; opacity: 0.4; }

  /* Biometric verification overlay */
  .bio-overlay {
    position: absolute; inset: 0; z-index: 850; background: rgba(244, 241, 234, 0.95);
    display: none; flex-direction: column; align-items: center; justify-content: center;
    padding: 24px; text-align: center;
  }
  .bio-overlay.show { display: flex; }
  .bio-ring {
    position: relative; width: 100px; height: 100px; margin-bottom: 24px;
    display: flex; align-items: center; justify-content: center;
  }
  .bio-circle {
    position: absolute; inset: 0; border: 4px solid var(--line); border-radius: 50%;
  }
  .bio-circle-active {
    position: absolute; inset: 0; border: 4px solid var(--primary); border-radius: 50%;
    border-top-color: transparent; animation: spin 1.2s linear infinite;
  }
  .bio-fingerprint { font-size: 48px; color: var(--primary); z-index: 5; }
  .bio-msg { font-size: 16px; font-weight: 500; color: var(--ink); margin-bottom: 8px; }
  .bio-sub { font-size: 12px; color: var(--ink-soft); }

  @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

  @media (max-width: 460px) {
    body, html { height: 100%; overflow: hidden; margin: 0; padding: 0; }
    .page-wrap { padding: 0; height: 100%; display: flex; flex-direction: column; }
    .phone-shell { border-radius: 0; padding: 0; box-shadow: none; max-width: 100%; height: 100%; display: flex; flex-direction: column; flex: 1; }
    .phone-screen { border-radius: 0; height: 100%; min-height: 0; flex: 1; }
  }

  .reveal { opacity: 0; transform: translateY(16px); transition: opacity .6s ease, transform .6s cubic-bezier(0.2, 0.8, 0.2, 1); }
  .reveal.in { opacity: 1; transform: translateY(0); }
</style>
</head>
<body>

<div class="page-wrap">

  <!-- Main Shell -->
  <div class="phone-shell reveal in">
    <div class="phone-screen" id="appScreen">
      
      <!-- Session authentication wrapper (dynamic register/login) -->
      <div id="authContainer" class="app-content" style="display:none; justify-content: center; align-items: center;">
        <div class="auth-card" style="width: 100%;">
          <div class="hero-icon">🛋️</div>
          <h2>CASA & CO.</h2>
          <p data-i18n-zh="家居會員系統" data-i18n-en="Home Membership System">家居會員系統</p>
          
          <div class="auth-tabs">
            <button id="tabBtnLogin" class="active" onclick="switchAuthTab('login')" data-i18n-zh="會員登入" data-i18n-en="Login">會員登入</button>
            <button id="tabBtnRegister" onclick="switchAuthTab('register')" data-i18n-zh="免費註冊" data-i18n-en="Register">免費註冊</button>
          </div>

          <!-- Login Form -->
          <form id="loginForm" onsubmit="handleAuthSubmit(event, 'login')">
            <div id="loginStepBasic">
                <div class="form-field">
                  <label data-i18n-zh="用戶名 / 手機 / 電郵" data-i18n-en="Username / Phone / Email">用戶名 / 手機 / 電郵</label>
                  <input type="text" id="loginUsername" required placeholder="zayn" autocomplete="username">
                </div>
                <div class="form-field">
                  <label data-i18n-zh="密碼" data-i18n-en="Password">密碼</label>
                  <input type="password" id="loginPassword" required placeholder="••••••••" autocomplete="current-password">
                </div>
                <button type="submit" class="btn btn-primary btn-block" style="border-radius: 100px; margin-top: 8px;" id="loginBtnMain" data-i18n-zh="密碼登入" data-i18n-en="Sign In with Password">密碼登入</button>
                <button type="button" class="btn btn-outline btn-block" id="bioLoginBtn" style="border-radius: 100px; margin-top: 10px;" onclick="triggerBiometricAuth('login')" data-i18n-zh="⚡ 生物辨識 Face ID 登入" data-i18n-en="⚡ Biometric FaceID Login">⚡ 生物辨識 Face ID 登入</button>
            </div>
            
            <div id="loginStepOtp" style="display:none; text-align:left; margin-top:10px;">
                <div class="form-field">
                    <label style="color: var(--primary); font-weight:600;" data-i18n-zh="安全驗證 (已超過設定天數未登入)" data-i18n-en="Security Verification (Login expired)">安全驗證 (已超過設定天數未登入)</label>
                    <p style="font-size: 11px; color: var(--ink-soft); margin-bottom: 8px;" data-i18n-zh="系統已發送 6 位數 OTP 至您的綁定手機/信箱。" data-i18n-en="System has sent a 6-digit OTP to your phone/email.">系統已發送 6 位數 OTP 至您的綁定手機/信箱。</p>
                    <div style="display: flex; gap: 8px;">
                        <input type="text" id="loginOtpInput" placeholder="請輸入 OTP" style="flex:1;">
                        <button type="button" class="btn btn-primary" onclick="verifyLoginOtp()" data-i18n-zh="驗證登入" data-i18n-en="Verify Login" style="width: auto; padding: 0 20px; border-radius: 8px; margin: 0;">驗證登入</button>
                    </div>
                    <div style="margin-top: 8px; text-align: right;">
                        <button type="button" id="btnLoginResendOtp" class="btn btn-outline" onclick="resendLoginOtp()" style="width: auto; padding: 6px 12px; border-radius: 8px; margin: 0; font-size: 11px;" data-i18n-zh="重新發送 OTP" data-i18n-en="Resend OTP">重新發送 OTP</button>
                    </div>
                </div>
                <button type="button" class="btn btn-outline btn-block" style="border-radius: 100px; margin-top: 10px; font-size:12px;" onclick="cancelLoginOtp()" data-i18n-zh="返回" data-i18n-en="Back">返回</button>
            </div>
          </form>

          <!-- Register Form -->
          <form id="registerForm" style="display:none;" onsubmit="handleAuthSubmit(event, 'register')">
            <div class="form-field">
              <label data-i18n-zh="用戶名 (登入帳號)" data-i18n-en="Username">用戶名 (登入帳號)</label>
              <input type="text" id="regUsername" required placeholder="例如: alex">
            </div>
            <div class="form-field">
              <label data-i18n-zh="顯示姓名" data-i18n-en="Display Name">顯示姓名</label>
              <input type="text" id="regName" required placeholder="例如: Alex Tan">
            </div>
            <div class="form-field">
              <label data-i18n-zh="性別" data-i18n-en="Gender">性別</label>
              <select id="regGender">
                <option value="Prefer not to say" data-i18n-zh="不透露" data-i18n-en="Prefer not to say">不透露</option>
                <option value="女" data-i18n-zh="女" data-i18n-en="Female">女</option>
                <option value="男" data-i18n-zh="男" data-i18n-en="Male">男</option>
              </select>
            </div>
            <div class="form-field" id="regContactFieldWrapper">
              <label id="regContactLabel" data-i18n-zh="手機號碼 / 電子郵件" data-i18n-en="Phone / Email">手機號碼 / 電子郵件</label>
              <div style="display:flex; gap: 8px;">
                <input type="text" id="regContact" required placeholder="0123456789 或 email@.com" style="flex: 1;">
                <button type="button" id="btnRegSendOtp" class="btn btn-outline" onclick="sendRegOtp()" style="padding: 0 12px; white-space:nowrap; display:none; margin: 0; border-radius: 8px; width: auto;" data-i18n-zh="發送 OTP" data-i18n-en="Send OTP">發送 OTP</button>
              </div>
            </div>
            
            <div class="form-field" id="regOtpField" style="display:none;">
              <label data-i18n-zh="輸入 OTP" data-i18n-en="Enter OTP">輸入 OTP</label>
              <div style="display:flex; gap: 8px;">
                <input type="text" id="regOtpInput" placeholder="6位數代碼" style="flex: 1;">
                <button type="button" id="btnRegVerifyOtp" class="btn btn-primary" onclick="verifyRegOtp()" style="padding: 0 12px; white-space:nowrap; margin: 0; border-radius: 8px; width: auto;" data-i18n-zh="驗證" data-i18n-en="Verify">驗證</button>
              </div>
            </div>

            <div id="regPasswordSection" style="display:block;">
              <div class="form-field">
                <label data-i18n-zh="設定密碼" data-i18n-en="Password">設定密碼</label>
                <input type="password" id="regPassword" required placeholder="最少 6 位字元">
              </div>
              <div class="form-field">
                <label data-i18n-zh="確認密碼" data-i18n-en="Confirm Password">確認密碼</label>
                <input type="password" id="regConfirmPassword" required placeholder="再次輸入密碼">
              </div>
              <div id="regBiometricRow" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; padding: 0 4px;">
                <span style="font-size: 13px;" data-i18n-zh="啟用生物辨識登入" data-i18n-en="Enable Biometric Login">啟用生物辨識登入</span>
                <label class="switch"><input type="checkbox" id="regBiometric" checked><span class="track"></span><span class="thumb"></span></label>
              </div>
              <button type="submit" id="regSubmitBtn" class="btn btn-primary btn-block" style="border-radius: 100px;" data-i18n-zh="註冊並登入" data-i18n-en="Register & Sign In">註冊並登入</button>
            </div>
          </form>
        </div>
      </div>

      <!-- App Portal (Visible when logged in) -->
      <div id="portalContainer" style="display:none; flex-direction: column; flex: 1; min-height: 0; width: 100%;">
        <div class="app-hero">
          <div class="hero-body">
            <!-- Dynamic Logo -->
            <div id="dynamicLogoContainer">
              <div class="hero-icon">🛋️</div>
              <div class="hero-brand" id="brandText">CASA & CO.</div>
            </div>
            <div class="hero-member">
              <span id="heroMemberName">—</span>
              <span class="tier-chip" id="memberTierChip" style="border-color: var(--accent); color: var(--accent-dark);">—</span>
            </div>
          </div>
        </div>

        <div class="app-content" id="appMainContent">

          <!-- LOYALTY POINT SYSTEM TAB -->
          <div class="app-tab" id="tab-loyalty">
            <div class="card">
              <div class="points-value-row">
                <div class="points-value" id="pointsVal">0</div>
                <div class="points-label" data-i18n-zh="現有積分" data-i18n-en="Current Points">現有積分</div>
              </div>
              <div class="progress-track"><div class="progress-fill" id="pointsProgressFill" style="width:0%;"></div></div>
              <div class="progress-caption" id="pointsProgressCaption">—</div>
            </div>

            <div class="card">
              <h2 class="card-title" data-i18n-zh="積分兌換專區" data-i18n-en="Redeem With Points">積分兌換專區</h2>
              <div class="reward-grid" id="rewardsContainer">
                <!-- Dynamic rewards -->
              </div>
            </div>

            <div class="card cta-card">
              <h3 data-i18n-zh="出示會員二維碼" data-i18n-en="Show Member QR">出示會員二維碼</h3>
              <p data-i18n-zh="請在門市付款結帳前向店員出示您的會員專屬QR碼，掃描後可發放積分。" data-i18n-en="Show your member QR code to staff at checkout to log your purchase points.">請在門市付款結帳前向店員出示您的會員專屬QR碼，掃描後可發放積分。</p>
              <button class="btn btn-primary" onclick="openCheckoutModal()" data-i18n-zh="📱 顯示會員二維碼" data-i18n-en="📱 Show Member QR">📱 顯示會員二維碼</button>
            </div>
            
            <div id="earnExtraContainerLoyalty"></div>
          </div>

          <!-- STAMP SYSTEM TAB -->
          <div class="app-tab" id="tab-stamps">
            <div class="card">
              <h2 class="card-title" data-i18n-zh="我的集印卡" data-i18n-en="My Stamp Card">我的集印卡</h2>
              <div class="stamp-grid" id="stampGridContainer">
                <!-- 10 stamp slots -->
              </div>
              <div class="stamp-caption" id="stampsCaption">—</div>
            </div>

            <div class="card cta-card">
              <h3 data-i18n-zh="門市集章" data-i18n-en="Earn Stamps">門市集章</h3>
              <p data-i18n-zh="結帳付款時出示此專屬二維碼給店員掃描，即可為您的集卡蓋上印花章。" data-i18n-en="Present this QR code to staff during checkout to add a stamp to your card.">結帳付款時出示此專屬二維碼給店員掃描，即可為您的集卡蓋上印花章。</p>
              <button class="btn btn-primary" onclick="openCheckoutModal()" data-i18n-zh="📱 顯示專屬碼" data-i18n-en="📱 Show My QR">📱 顯示專屬碼</button>
            </div>

            <div id="earnExtraContainerStamps"></div>
          </div>

          <!-- MEMBERSHIP TAB -->
          <div class="app-tab" id="tab-membership">
            <div class="card" style="background:none; box-shadow:none; padding:0; border:none;">
              <div class="member-card">
                <div class="member-card-top">
                  <div>
                    <div class="label">CASA & CO. MEMBERSHIP</div>
                    <div class="name" id="memberCardName"></div>
                  </div>
                  <span class="tier-chip" id="memberCardTierChip"></span>
                </div>
                <div class="member-card-foot">
                  <div class="barcode">
                      <svg id="memberBarcodeSvg"></svg>
                  </div>
                  <div class="since" id="memberSinceLabel"></div>
                </div>
              </div>
              <div class="tier-progress-caption" id="tierProgressCaption"></div>
            </div>
            
            <div class="card">
              <h2 class="card-title" data-i18n-zh="會員等級專享" data-i18n-en="Membership Tier Perks">會員等級專享</h2>
              <div id="tierList" style="margin-top:10px; font-size:13.5px; color:var(--ink-soft); line-height:1.6;">
                <!-- Dynamic Tier List injected here -->
              </div>
            </div>
          </div>

          <!-- PRIZE WHEEL (MINIGAME) TAB -->
          <div class="app-tab" id="tab-draw">
            <div class="card">
              <div class="spins-banner">
                <span class="lbl" data-i18n-zh="可用抽獎次數" data-i18n-en="Available Spins">可用抽獎次數</span>
                <span class="val" id="spinsCount">0</span>
              </div>
              <div class="wheel-wrap">
                <div class="wheel-pointer"></div>
                <div class="wheel" id="wheel" style="transform: rotate(0deg);">
                  <div class="wheel-labels" id="wheelLabels"></div>
                </div>
                <div class="wheel-hub">🪴</div>
              </div>
              <div class="spin-btn-row">
                <button class="btn btn-primary" id="spinBtn" onclick="spinWheel()" data-i18n-zh="🎡 開始抽獎" data-i18n-en="🎡 Start Spin">🎡 開始抽獎</button>
              </div>
              <div class="prize-list">
                <div class="prize-list-title" data-i18n-zh="獎品一覽" data-i18n-en="Prize List">獎品一覽</div>
                <div class="prize-chip-grid" id="prizeChipGrid"></div>
              </div>
            </div>
          </div>

          <!-- COUPONS TAB -->
          <div class="app-tab" id="tab-vouchers">
            <div class="card">
              <h2 class="card-title" data-i18n-zh="我的優惠券" data-i18n-en="My Vouchers">我的優惠券</h2>
              <div id="voucherList">
                <!-- Dynamic list -->
              </div>
            </div>
          </div>

          <!-- SCAN RECEIPT TAB -->
          <div class="app-tab" id="tab-scan">
            <div class="card">
              <h2 class="card-title" data-i18n-zh="拍下購物收據" data-i18n-en="Upload Receipt">拍下購物收據</h2>
              <p style="font-size: 13px; color: var(--ink-soft); line-height: 1.5; text-align: center; margin-bottom: 20px;" data-i18n-zh="上傳或拍攝您的紙本發票收據，系統後台核實消費金額後將自動發放對應的積分或印花。" data-i18n-en="Upload or snap a picture of your paper receipt. After admin verification, rewards will be issued automatically.">上傳或拍攝您的紙本發票收據，系統後台核實消費金額後將自動發放對應的積分或印花。</p>
              
              <div class="scan-viewfinder">
                <div class="scan-corner tl"></div><div class="scan-corner tr"></div>
                <div class="scan-corner bl"></div><div class="scan-corner br"></div>
                <div class="scan-icon">🧾</div>
              </div>

              <form id="receiptUploadForm" onsubmit="handleReceiptUpload(event)">
                <div class="form-field" style="text-align: center;">
                  <input type="file" id="receiptFileInput" accept="image/*" capture="environment" style="display:none;" onchange="updateSelectedFileLabel()">
                  <button type="button" class="btn btn-outline btn-block" onclick="document.getElementById('receiptFileInput').click()" data-i18n-zh="📸 拍攝/選擇收據圖片" data-i18n-en="📸 Take / Choose Image">📸 拍攝/選擇收據圖片</button>
                  <div id="fileSelectedLabel" style="font-size:12px; color: var(--primary); margin-top: 8px; font-weight: 500;"></div>
                </div>
                <button type="submit" class="btn btn-primary btn-block" style="border-radius:100px; margin-top: 8px;" id="receiptSubmitBtn" disabled data-i18n-zh="📤 上傳收據審核" data-i18n-en="📤 Upload for Review">📤 上傳收據審核</button>
              </form>
            </div>
          </div>

          <!-- PROFILE TAB -->
          <div class="app-tab" id="tab-profile">
            <div class="profile-head">
              <div class="profile-avatar" id="userAvatar">A</div>
              <div class="profile-name" id="profileNameDisplay">—</div>
              <div class="profile-tier-chip" id="profileTierChipDetails">🌾 <span>—</span></div>
            </div>

            <div class="profile-stats">
              <div class="profile-stat-card">
                <div class="v" id="statPts">0</div>
                <div class="l" id="statPtsLabel" data-i18n-zh="可用積分" data-i18n-en="Available Pts">可用積分</div>
              </div>
              <div class="profile-stat-card">
                <div class="v" id="statVouchersCount">0</div>
                <div class="l" data-i18n-zh="現有卡券" data-i18n-en="Active Vouchers">現有卡券</div>
              </div>
              <div class="profile-stat-card">
                <div class="v" id="statSpins">0</div>
                <div class="l" data-i18n-zh="抽獎次數" data-i18n-en="Spins Left">抽獎次數</div>
              </div>
            </div>

            <div class="card">
              <div class="settings-row" id="profileBiometricRow">
                <div class="s-ic">🙂</div>
                <div class="s-label" data-i18n-zh="生物辨識登入" data-i18n-en="Biometric Login">生物辨識登入</div>
                <label class="switch"><input type="checkbox" id="toggleBiometric" onchange="toggleBiometricSetting()"><span class="track"></span><span class="thumb"></span></label>
              </div>
              <div class="settings-row">
                <div class="s-ic">🔔</div>
                <div class="s-label" data-i18n-zh="通知設定" data-i18n-en="Notifications">通知設定</div>
                <label class="switch"><input type="checkbox" checked><span class="track"></span><span class="thumb"></span></label>
              </div>
            </div>

            <!-- Verification Status Card -->
            <div class="card" id="profileVerificationCard" style="padding: 16px 20px; display: none;">
              <h3 style="font-size: 14px; margin-bottom: 12px; font-weight:600; text-align: left;" data-i18n-zh="安全與帳戶驗證" data-i18n-en="Account Verification">安全與帳戶驗證</h3>
              <div id="profileVerifyContainer" style="display:flex; flex-direction:column; gap:12px; text-align: left;">
                <!-- Dynamic verification items -->
              </div>
            </div>

            <div class="card" style="padding:4px 20px;">
              <button class="account-row" onclick="openEditProfile()">
                <span class="a-ic">✏️</span>
                <span style="flex:1; min-width:0;">
                  <span class="a-label" style="display:block;" data-i18n-zh="編輯個人資料" data-i18n-en="Edit Profile">編輯個人資料</span>
                  <span class="a-sub" id="profileEditSummary">—</span>
                </span>
                <span class="a-chev">›</span>
              </button>
            </div>

            <button class="btn btn-outline btn-block" style="border-radius:100px; margin-top:10px; border-color: rgba(196,90,90,0.3); color: #B04A4A;" onclick="handleLogout()" data-i18n-zh="🚪 登出帳戶" data-i18n-en="🚪 Sign Out">🚪 登出帳戶</button>
          </div>

        </div>

        <!-- Dynamic Bottom Navigation Bar -->
        <div class="bottom-bar" id="appBottomBar">
          <!-- Rendered dynamically from backend settings -->
        </div>

      </div>

      <!-- Biometric Simulation Scanning Overlay -->
      <div class="bio-overlay" id="bioScanOverlay">
        <div class="bio-ring">
          <div class="bio-circle"></div>
          <div class="bio-circle-active"></div>
          <div class="bio-fingerprint" id="bioScanIcon">👤</div>
        </div>
        <div class="bio-msg" id="bioScanMsg" data-i18n-zh="正在辨識..." data-i18n-en="Scanning...">正在辨識...</div>
        <div class="bio-sub" data-i18n-zh="請對準鏡頭或觸摸指紋感應器" data-i18n-en="Position your face or touch the sensor">請對準鏡頭或觸摸指紋感應器</div>
      </div>

    </div>
  </div>

</div>

<!-- Floating Language Button -->
<button class="floating-lang" id="langFloatingBtn" onclick="toggleLanguage()">EN</button>

<div class="toast" id="toast"><span class="dot"></span><span id="toastText"></span></div>

<!-- ================= MODALS ================= -->

<!-- Member QR Checkin Modal -->
<div class="modal-overlay" id="checkinOverlay">
  <div class="redeem-modal">
    <button class="modal-close" onclick="closeModal('checkinOverlay')"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" width="16" height="16"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
    <div class="modal-eyebrow" data-i18n-zh="門市付款專用" data-i18n-en="Store Checkout">門市付款專用</div>
    <h3 data-i18n-zh="您的會員專屬碼" data-i18n-en="Your Member QR">您的會員專屬碼</h3>
    <div class="sub" data-i18n-zh="請在付款前向店員出示此畫面，掃描後即可記錄本次消費積分/印花。" data-i18n-en="Please show this screen to staff before paying.">請在付款前向店員出示此畫面，掃描後即可記錄本次消費積分/印花。</div>
    
    <div class="redeem-code-box">
      <div class="real-qr" id="checkinQr"></div>
      <div class="redeem-code" id="checkinCode"></div>
      <div class="redeem-code-label" data-i18n-zh="即時動態會員碼" data-i18n-en="Live Membership Code">即時動態會員碼</div>
    </div>

    <div class="redeem-note" style="margin-top: 15px;" data-i18n-zh="店員操作：請使用店員系統掃描此二維碼，並輸入消費金額以發放獎勵。" data-i18n-en="Staff: scan this code using staff portal to issue reward.">店員操作：請使用店員系統掃描此二維碼，並輸入消費金額以發放獎勵。</div>
  </div>
</div>

<!-- Voucher Use Modal -->
<div class="modal-overlay" id="redeemOverlay">
  <div class="redeem-modal">
    <button class="modal-close" onclick="closeModal('redeemOverlay')"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" width="16" height="16"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
    <div class="modal-eyebrow" data-i18n-zh="優惠券核銷" data-i18n-en="Voucher Redemption">優惠券核銷</div>
    <h3 id="redeemName">—</h3>
    <div class="sub" data-i18n-zh="請在結帳時出示此畫面給店員" data-i18n-en="Show this code to staff at checkout">請在結帳時出示此畫面給店員</div>
    <div class="redeem-code-box">
      <div class="real-qr" id="redeemQr"></div>
      <div class="redeem-code" id="redeemCode"></div>
      <div class="redeem-code-label" data-i18n-zh="單次核銷碼" data-i18n-en="One-Time Redeem Code">單次核銷碼</div>
    </div>
    <div class="redeem-note" data-i18n-zh="店員操作：核對無誤後在收銀系統或管理台進行兌換核銷。" data-i18n-en="Staff: scan this QR to redeem and invalidate.">店員操作：核對無誤後在收銀系統或管理台進行兌換核銷。</div>
  </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal-overlay" id="editProfileOverlay">
  <div class="redeem-modal" style="text-align:left;">
    <button class="modal-close" onclick="closeModal('editProfileOverlay')"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" width="16" height="16"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
    <div class="modal-eyebrow" style="text-align:center;" data-i18n-zh="個人資料" data-i18n-en="Profile">個人資料</div>
    <h3 style="text-align:center; margin-bottom:20px;" data-i18n-zh="編輯個人資料" data-i18n-en="Edit Profile">編輯個人資料</h3>
    
    <div class="form-field">
      <label data-i18n-zh="姓名" data-i18n-en="Name">姓名</label>
      <input type="text" id="editNameInput" placeholder="請輸入姓名" maxlength="30">
    </div>
    <div class="form-field">
      <label data-i18n-zh="性別" data-i18n-en="Gender">性別</label>
      <select id="editGenderInput">
        <option value="Prefer not to say" data-i18n-zh="不透露" data-i18n-en="Prefer not to say">不透露</option>
        <option value="女" data-i18n-zh="女" data-i18n-en="Female">女</option>
        <option value="男" data-i18n-zh="男" data-i18n-en="Male">男</option>
      </select>
    </div>
    <div class="form-field">
      <label data-i18n-zh="手機號碼" data-i18n-en="Phone">手機號碼</label>
      <input type="text" id="editPhoneInput" placeholder="例如: 0912345678">
    </div>
    <div class="form-field" style="margin-bottom:22px;">
      <label data-i18n-zh="電郵" data-i18n-en="Email">電郵</label>
      <input type="email" id="editEmailInput" placeholder="you@example.com">
    </div>
    <button class="btn btn-primary btn-block" style="border-radius:100px;" onclick="saveProfile()" data-i18n-zh="儲存變更" data-i18n-en="Save Changes">儲存變更</button>
  </div>
</div>

<!-- Lucky Draw Winnings Modal -->
<div class="modal-overlay" id="prizeOverlay">
  <div class="redeem-modal" style="padding-top: 24px;">
    <button class="modal-close" onclick="closeModal('prizeOverlay')"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" width="16" height="16"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
    <div style="font-size: 56px; margin-bottom: 12px;" id="prizeEmoji">🎉</div>
    <div class="modal-eyebrow" data-i18n-zh="幸運轉盤" data-i18n-en="Lucky Draw">幸運轉盤</div>
    <h3 id="prizeTitle">—</h3>
    <div class="sub" id="prizeSub">—</div>
    <button class="btn btn-primary btn-block" style="border-radius:100px;" onclick="closeModal('prizeOverlay')" data-i18n-zh="太棒了" data-i18n-en="Awesome!">太棒了</button>
  </div>
</div>

<!-- ================= JS LOGIC ================= -->
<script>
  let publicSettings = {};
  let fullData = null; // Re-declare to hold settings across scope
  let userData = null;
  let currentLang = 'zh';
  let systemMode = 'points'; // 'points' or 'stamps'
  let currentTab = 'loyalty';
  let wheelPrizes = [];
  let isSpinning = false;

  // ================= NEW OTP LOGIC VARIABLES =================
  let isOtpRequiredForReg = true; // Will be fetched from public settings later
  let regOtpVerified = false;
  let otpTimers = {};

  // Translation mapping
  const i18n = {
    zh: {
      points: '積分',
      stamps: '印花',
      available: '現有',
      accumulated: '累積',
      success: '成功',
      fail: '失敗'
    },
    en: {
      points: 'Points',
      stamps: 'Stamps',
      available: 'Available',
      accumulated: 'Lifetime',
      success: 'Success',
      fail: 'Fail'
    }
  };

  // Toast message
  function showToast(msg) {
    const t = document.getElementById('toast');
    document.getElementById('toastText').textContent = msg;
    t.classList.add('show');
    clearTimeout(showToast._t);
    showToast._t = setTimeout(() => t.classList.remove('show'), 2800);
  }

  function startOtpCooldown(btnId) {
      const btn = document.getElementById(btnId);
      if (!btn) return;
      
      btn.disabled = true;
      let timeLeft = 60;
      
      if(!btn.dataset.originalText) {
          btn.dataset.originalText = currentLang === 'zh' ? '重新發送' : 'Resend OTP';
      }

      if(otpTimers[btnId]) clearInterval(otpTimers[btnId]);

      btn.textContent = `${timeLeft}s`;
      otpTimers[btnId] = setInterval(() => {
          timeLeft--;
          if (timeLeft <= 0) {
              clearInterval(otpTimers[btnId]);
              btn.disabled = false;
              btn.textContent = btn.dataset.originalText;
          } else {
              btn.textContent = `${timeLeft}s`;
          }
      }, 1000);
  }

  function applyBiometricSettings() {
      const settings = (fullData && fullData.settings) || publicSettings || {};
      const bioEnabled = parseInt(settings.biometric_login_enabled ?? 0) === 1;
      
      const bioLoginBtn = document.getElementById('bioLoginBtn');
      if (bioLoginBtn) {
          bioLoginBtn.style.display = bioEnabled ? 'block' : 'none';
      }
      
      const regBiometricRow = document.getElementById('regBiometricRow');
      if (regBiometricRow) {
          regBiometricRow.style.display = bioEnabled ? 'flex' : 'none';
      }
      
      const profileBiometricRow = document.getElementById('profileBiometricRow');
      if (profileBiometricRow) {
          profileBiometricRow.style.display = bioEnabled ? 'flex' : 'none';
      }
  }

  async function sendProfileVerifyOtp(type) {
    const inputId = type === 'phone' ? 'verifyPhoneInput' : 'verifyEmailInput';
    const value = document.getElementById(inputId).value.trim();
    const btnId = type === 'phone' ? 'btnSendPhoneVerify' : 'btnSendEmailVerify';
    
    if (!value) {
      showToast(type === 'phone' ? '請輸入手機號碼' : '請輸入電子信箱');
      return;
    }

    const form = new FormData();
    form.append('type', type);
    form.append('value', value);

    const btn = document.getElementById(btnId);
    btn.disabled = true;

    try {
      const res = await fetch('api.php?action=send_profile_verify_otp', {
        method: 'POST',
        body: form
      });
      const data = await res.json();
      showToast(data.message);
      if (data.success) {
        const otpRowId = type === 'phone' ? 'verifyPhoneOtpRow' : 'verifyEmailOtpRow';
        document.getElementById(otpRowId).style.display = 'flex';
        startOtpCooldown(btnId);
      } else {
        btn.disabled = false;
      }
    } catch (e) {
      showToast('發送失敗，請重試 / Failed to send OTP');
      btn.disabled = false;
    }
  }

  async function submitProfileVerify(type) {
    const otpInputId = type === 'phone' ? 'verifyPhoneOtpInput' : 'verifyEmailOtpInput';
    const otp = document.getElementById(otpInputId).value.trim();
    if (!otp) {
      showToast('請輸入驗證碼');
      return;
    }

    const form = new FormData();
    form.append('otp', otp);

    try {
      const res = await fetch('api.php?action=verify_profile_contact', {
        method: 'POST',
        body: form
      });
      const data = await res.json();
      showToast(data.message);
      if (data.success) {
        loadUserData(); // Reload user data and refresh UI verification statuses!
      }
    } catch (e) {
      showToast('驗證失敗，請重試 / Verification failed');
    }
  }

  function renderVerificationPerks() {
    const card = document.getElementById('profileVerificationCard');
    const container = document.getElementById('profileVerifyContainer');
    if (!card || !container) return;
    
    container.innerHTML = '';
    
    const settings = (fullData && fullData.settings) || publicSettings || {};
    const otpEnabled = settings.otp_enabled == '1';
    const method = settings.otp_method || 'both';

    // Verification is available if:
    // 1. OTP is enabled in admin settings
    // 2. OR the user has at least one verified contact details (we show their verified status)
    const hasVerified = (userData.phone_verified == 1 || userData.email_verified == 1);
    if (!otpEnabled && !hasVerified) {
      card.style.display = 'none';
      return;
    }

    card.style.display = 'block';

    // Show Phone Row if phone is verified, or if OTP is enabled and (method is 'phone' or 'both' or the user has verified email)
    const showPhone = (userData.phone_verified == 1) || (otpEnabled && (method === 'phone' || method === 'both' || userData.email_verified == 1));

    // Show Email Row if email is verified, or if OTP is enabled and (method is 'email' or 'both' or the user has verified phone)
    const showEmail = (userData.email_verified == 1) || (otpEnabled && (method === 'email' || method === 'both' || userData.phone_verified == 1));

    if (showPhone) {
      const phoneRow = document.createElement('div');
      phoneRow.style.borderBottom = showEmail ? '1px solid var(--line)' : 'none';
      phoneRow.style.paddingBottom = showEmail ? '12px' : '0';
      phoneRow.style.marginBottom = showEmail ? '12px' : '0';
      
      if (userData.phone_verified == 1) {
        phoneRow.innerHTML = `
          <div style="display:flex; align-items:center; justify-content:space-between; font-size:13px;">
            <div>
              <span style="font-weight:600;">手機號碼 (Phone)</span>
              <div style="color:var(--ink-soft); margin-top:2px;">${userData.phone || ''}</div>
            </div>
            <span style="color:var(--ok); font-weight:600; font-size:12px;">🟢 已驗證</span>
          </div>
        `;
      } else {
        phoneRow.innerHTML = `
          <div style="font-size:13px;">
            <div style="display:flex; align-items:center; justify-content:space-between;">
              <span style="font-weight:600;">手機號碼 (Phone)</span>
              <span style="color:var(--warn); font-size:11px; font-weight:500;">⚠️ 未驗證</span>
            </div>
            <div style="display:flex; gap:8px; margin-top:8px;">
              <input type="text" id="verifyPhoneInput" value="${userData.phone || ''}" placeholder="輸入手機號碼" style="flex:1; padding:8px 12px; font-size:12.5px; border:1px solid var(--line); border-radius:100px; background:var(--page);">
              <button class="btn btn-outline btn-sm" onclick="sendProfileVerifyOtp('phone')" id="btnSendPhoneVerify" style="padding:0 14px; font-size:11.5px; border-radius:100px; white-space:nowrap; margin:0;">發送驗證碼</button>
            </div>
            <div id="verifyPhoneOtpRow" style="display:none; gap:8px; margin-top:8px;">
              <input type="text" id="verifyPhoneOtpInput" placeholder="輸入6位數驗證碼" style="flex:1; padding:8px 12px; font-size:12.5px; border:1px solid var(--line); border-radius:100px; background:var(--page);">
              <button class="btn btn-primary btn-sm" onclick="submitProfileVerify('phone')" style="padding:0 16px; font-size:11.5px; background:var(--ok); border-radius:100px; white-space:nowrap; margin:0;">驗證</button>
            </div>
          </div>
        `;
      }
      container.appendChild(phoneRow);
    }

    if (showEmail) {
      const emailRow = document.createElement('div');
      
      if (userData.email_verified == 1) {
        emailRow.innerHTML = `
          <div style="display:flex; align-items:center; justify-content:space-between; font-size:13px;">
            <div>
              <span style="font-weight:600;">電子信箱 (Email)</span>
              <div style="color:var(--ink-soft); margin-top:2px;">${userData.email || ''}</div>
            </div>
            <span style="color:var(--ok); font-weight:600; font-size:12px;">🟢 已驗證</span>
          </div>
        `;
      } else {
        emailRow.innerHTML = `
          <div style="font-size:13px;">
            <div style="display:flex; align-items:center; justify-content:space-between;">
              <span style="font-weight:600;">電子信箱 (Email)</span>
              <span style="color:var(--warn); font-size:11px; font-weight:500;">⚠️ 未驗證</span>
            </div>
            <div style="display:flex; gap:8px; margin-top:8px;">
              <input type="text" id="verifyEmailInput" value="${userData.email || ''}" placeholder="輸入電子信箱" style="flex:1; padding:8px 12px; font-size:12.5px; border:1px solid var(--line); border-radius:100px; background:var(--page);">
              <button class="btn btn-outline btn-sm" onclick="sendProfileVerifyOtp('email')" id="btnSendEmailVerify" style="padding:0 14px; font-size:11.5px; border-radius:100px; white-space:nowrap; margin:0;">發送驗證碼</button>
            </div>
            <div id="verifyEmailOtpRow" style="display:none; gap:8px; margin-top:8px;">
              <input type="text" id="verifyEmailOtpInput" placeholder="輸入6位數驗證碼" style="flex:1; padding:8px 12px; font-size:12.5px; border:1px solid var(--line); border-radius:100px; background:var(--page);">
              <button class="btn btn-primary btn-sm" onclick="submitProfileVerify('email')" style="padding:0 16px; font-size:11.5px; background:var(--ok); border-radius:100px; white-space:nowrap; margin:0;">驗證</button>
            </div>
          </div>
        `;
      }
      container.appendChild(emailRow);
    }

    if (container.children.length === 0) {
      card.style.display = 'none';
    }
  }

  // Set up auth UI based on OTP setting
  function setupAuthUI() {
      applyBiometricSettings();
      isOtpRequiredForReg = (publicSettings.otp_enabled == '1');
      const method = publicSettings.otp_method || 'both';
      
      const label = document.getElementById('regContactLabel');
      const input = document.getElementById('regContact');
      
      // Clear previous type restrictions
      input.removeAttribute('type');
      input.removeAttribute('pattern');

      if (method === 'phone') { 
          label.textContent = currentLang === 'zh' ? '手機號碼' : 'Phone Number'; 
          input.placeholder = currentLang === 'zh' ? '例如: 0123456789' : 'e.g. 0123456789'; 
          input.type = 'tel';
      }
      else if (method === 'email') { 
          label.textContent = currentLang === 'zh' ? '電子郵件' : 'Email Address'; 
          input.placeholder = currentLang === 'zh' ? '例如: email@example.com' : 'e.g. email@example.com'; 
          input.type = 'email';
      }
      else { 
          label.textContent = currentLang === 'zh' ? '手機號碼 / 電子郵件' : 'Phone / Email'; 
          input.placeholder = currentLang === 'zh' ? '手機 或 Email' : 'Phone or Email'; 
          input.type = 'text';
      }

      if (isOtpRequiredForReg) {
          document.getElementById('btnRegSendOtp').style.display = 'block';
          document.getElementById('regPasswordSection').style.display = 'none';
          document.getElementById('regPassword').removeAttribute('required');
          document.getElementById('regConfirmPassword').removeAttribute('required');
      } else {
          document.getElementById('btnRegSendOtp').style.display = 'none';
          document.getElementById('regOtpField').style.display = 'none';
          document.getElementById('regPasswordSection').style.display = 'block';
          document.getElementById('regPassword').setAttribute('required', 'true');
          document.getElementById('regConfirmPassword').setAttribute('required', 'true');
      }
  }

  // Switch Auth view tabs
  function switchAuthTab(type) {
    document.getElementById('tabBtnLogin').classList.toggle('active', type === 'login');
    document.getElementById('tabBtnRegister').classList.toggle('active', type === 'register');
    document.getElementById('loginForm').style.display = type === 'login' ? 'block' : 'none';
    document.getElementById('registerForm').style.display = type === 'register' ? 'block' : 'none';

    if (type === 'register') setupAuthUI();
  }

  // OTP Functions
  async function sendRegOtp() {
      const contact = document.getElementById('regContact').value.trim();
      if(!contact) { showToast(currentLang === 'zh' ? '請先輸入聯絡方式' : 'Enter phone/email first'); return; }
      
      const method = publicSettings.otp_method || 'both';
      if (method === 'phone') {
          if (!/^\+?\d+$/.test(contact)) {
              showToast(currentLang === 'zh' ? '請輸入有效的手機號碼 (僅限數字)' : 'Please enter a valid phone number');
              return;
          }
      } else if (method === 'email') {
          if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(contact)) {
              showToast(currentLang === 'zh' ? '請輸入有效的電子郵件' : 'Please enter a valid email address');
              return;
          }
      }

      const form = new FormData(); 
      form.append('contact', contact);
      form.append('action_type', 'register'); // 標記為註冊請求，讓後端去檢查重複
      const res = await fetch('api.php?action=send_otp', { method: 'POST', body: form });
      const data = await res.json();
      showToast(data.message);
      if(data.success) {
          document.getElementById('regOtpField').style.display = 'flex';
          startOtpCooldown('btnRegSendOtp');
      }
  }

  async function verifyRegOtp() {
      const otp = document.getElementById('regOtpInput').value.trim();
      if(!otp) { showToast(currentLang === 'zh' ? '請輸入OTP' : 'Enter OTP'); return; }
      
      const form = new FormData(); form.append('otp', otp);
      const res = await fetch('api.php?action=verify_otp', { method: 'POST', body: form });
      const data = await res.json();
      
      if(data.success) {
          showToast(currentLang === 'zh' ? 'OTP 驗證成功！請設定密碼' : 'OTP Verified! Set password');
          regOtpVerified = true;
          document.getElementById('regOtpField').style.display = 'none';
          document.getElementById('btnRegSendOtp').style.display = 'none';
          document.getElementById('regContact').readOnly = true; 
          
          document.getElementById('regPasswordSection').style.display = 'block';
          document.getElementById('regPassword').setAttribute('required', 'true');
          document.getElementById('regConfirmPassword').setAttribute('required', 'true');
      } else {
          showToast(data.message);
      }
  }

  async function verifyLoginOtp() {
      const otp = document.getElementById('loginOtpInput').value.trim();
      if(!otp) { showToast(currentLang === 'zh' ? '請輸入OTP' : 'Enter OTP'); return; }
      
      const form = new FormData(); 
      form.append('otp', otp);
      const verifyRes = await fetch('api.php?action=verify_otp', { method: 'POST', body: form });
      const verifyData = await verifyRes.json();

      if (verifyData.success) {
          const username = document.getElementById('loginOtpInput').dataset.user;
          const loginForm = new FormData(); loginForm.append('username', username);
          const res = await fetch('api.php?action=login_with_otp', { method: 'POST', body: loginForm });
          const data = await res.json();
          if (data.success) {
              showToast(data.message);
              loadUserData();
          }
      } else {
          showToast(verifyData.message);
      }
  }

  async function resendLoginOtp() {
      const user = document.getElementById('loginOtpInput').dataset.user;
      if (!user) return;
      const otpForm = new FormData(); otpForm.append('contact', user);
      const res = await fetch('api.php?action=send_otp', { method: 'POST', body: otpForm });
      const data = await res.json();
      showToast(data.message);
      if(data.success) {
          startOtpCooldown('btnLoginResendOtp');
      }
  }

  function cancelLoginOtp() {
      document.getElementById('loginStepOtp').style.display = 'none';
      document.getElementById('loginStepBasic').style.display = 'block';
  }

  function openCheckoutModal() {
    // Create actual scannable QR Code using pure user QR string
    createRealQRCode('checkinQr', userData.qr_code);
    document.getElementById('checkinCode').textContent = userData.qr_code.replace('USER_QR_', '');
    
    openModal('checkinOverlay');
  }

  function createRealQRCode(elementId, text) {
      const container = document.getElementById(elementId);
      container.innerHTML = '';
      new QRCode(container, {
          text: text,
          width: 140,
          height: 140,
          colorDark : "#3A4036",
          colorLight : "#ffffff",
          correctLevel : QRCode.CorrectLevel.H
      });
  }

  // Modal logic
  function openModal(id) {
    document.getElementById(id).classList.add('show');
  }
  function closeModal(id) {
    document.getElementById(id).classList.remove('show');
  }

  // Language management
  function toggleLanguage() {
    currentLang = currentLang === 'zh' ? 'en' : 'zh';
    document.getElementById('langFloatingBtn').textContent = currentLang === 'zh' ? 'EN' : '中';
    applyTranslations(true);
  }

  function applyTranslations(updateDyn = false) {
    document.querySelectorAll('[data-i18n-zh]').forEach(el => {
      el.textContent = currentLang === 'zh' ? el.dataset.i18nZh : el.dataset.i18nEn;
    });
    
    // Placeholder translation
    document.querySelectorAll('[data-i18n-placeholder-zh]').forEach(el => {
      el.setAttribute('placeholder', currentLang === 'zh' ? el.dataset.i18nPlaceholderZh : el.dataset.i18nPlaceholderEn);
    });

    if (updateDyn && userData) {
      updateUI();
    } else {
      setupAuthUI();
    }
  }

  // Submit extra task function
  async function submitTask(taskId) {
    if (!confirm(currentLang === 'zh' ? '確定要送出審核申請嗎？' : 'Submit task for review?')) return;
    
    const form = new FormData();
    form.append('task_id', taskId);
    
    try {
      const res = await fetch('api.php?action=submit_task', {
        method: 'POST',
        body: form
      });
      const data = await res.json();
      if (data.success) {
        showToast(data.message);
      } else {
        showToast(data.message);
      }
    } catch (e) {
      showToast('提交失敗 / Submission failed');
    }
  }

  function renderEarnExtra() {
    const tasks = fullData.tasks_config ? fullData.tasks_config.filter(t => t.reward_type === systemMode) : [];
    const modeStr = systemMode === 'stamps' ? (currentLang === 'zh' ? '印花' : 'Stamps') : (currentLang === 'zh' ? '積分' : 'Points');
    
    if(tasks.length === 0) {
      document.getElementById('earnExtraContainerLoyalty').innerHTML = '';
      document.getElementById('earnExtraContainerStamps').innerHTML = '';
      return;
    }

    const title = currentLang === 'zh' ? '賺取額外獎賞' : 'Earn Extra Rewards';
    let html = `
    <div class="card">
      <h2 class="card-title" style="text-align: left; border-bottom: 2px solid #E8E5DF; padding-bottom: 12px; margin-bottom: 20px; margin-left: 0; margin-right: 0;">${title}</h2>`;

    tasks.forEach((t, index) => {
      const taskTitle = currentLang === 'zh' ? t.name_zh : (t.name_en || t.name_zh);
      const taskDesc = currentLang === 'zh' ? t.desc_zh : (t.desc_en || t.desc_zh);
      const borderBottom = index < tasks.length - 1 ? 'border-bottom: 1px solid var(--line); padding-bottom: 16px; margin-bottom: 16px;' : '';
      
      html += `
      <div style="display: flex; align-items: center; justify-content: space-between; ${borderBottom}">
        <div>
          <h3 style="font-size: 15px; margin-bottom: 4px;">${taskTitle}</h3>
          <p style="font-size: 12px; color: var(--ink-soft);">${taskDesc}</p>
        </div>
        <div style="text-align: right; flex-shrink:0; margin-left: 10px;">
          <div style="font-weight: 700; color: var(--accent-dark); font-size: 13px; margin-bottom: 6px;">+${t.reward_amount} ${modeStr}</div>
          <button class="btn btn-primary" onclick="submitTask(${t.id})" style="width: 36px; height: 36px; padding: 0; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;"><svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg></button>
        </div>
      </div>`;
    });

    html += `</div>`;

    document.getElementById('earnExtraContainerLoyalty').innerHTML = html;
    document.getElementById('earnExtraContainerStamps').innerHTML = html;
  }

  // Load user data on startup
  async function loadUserData() {
    try {
      const res = await fetch('api.php?action=get_user_data');
      const text = await res.text();
      let data;
      try {
        data = JSON.parse(text);
      } catch(err) {
        // Not logged in or parsing error, fetch public settings instead for auth view
        fetch('api.php?action=get_public_data')
          .then(r => r.json())
          .then(d => {
             if (d.success) { publicSettings = d.settings; setupAuthUI(); }
          });
        return;
      }
      
      if (data.success) {
        fullData = data;
        userData = data.user;
        userData.vouchers = data.vouchers;
        systemMode = data.settings.system_mode || 'points';
        wheelPrizes = data.wheel_prizes;
        
        // Handle Dynamic Logo
        const brandText = document.getElementById('brandText');
        if (data.settings.logo_type === 'image' && data.settings.logo_image_url) {
          document.getElementById('dynamicLogoContainer').innerHTML = `<img src="${data.settings.logo_image_url}" class="hero-brand-img" alt="Logo">`;
        } else {
          document.getElementById('dynamicLogoContainer').innerHTML = `
            <div class="hero-icon">🛋️</div>
            <div class="hero-brand" id="brandText">${data.settings.logo_text || 'CASA & CO.'}</div>
          `;
        }

        let bottomBarList;
        try {
          bottomBarList = JSON.parse(data.settings.bottom_bar);
          // Check if bottomBarList has membership tab. If not, trigger fallback to get the 5 tabs
          const hasMembership = bottomBarList.some(tab => tab.id === 'membership');
          if (!hasMembership) {
            throw new Error("Missing membership tab");
          }
        } catch(e) {
          bottomBarList = [
            {id: 'loyalty', labelZh: '獎賞', labelEn: 'Rewards', icon: '🎁', visible: true},
            {id: 'membership', labelZh: '會員', labelEn: 'Membership', icon: '💳', visible: true},
            {id: 'draw', labelZh: '抽獎', labelEn: 'Lucky Draw', icon: '🎡', visible: true},
            {id: 'vouchers', labelZh: '優惠券', labelEn: 'Vouchers', icon: '🏷️', visible: true},
            {id: 'profile', labelZh: '個人', labelEn: 'Profile', icon: '👤', visible: true}
          ];
        }

        renderBottomBar(bottomBarList);

        document.getElementById('authContainer').style.display = 'none';
        document.getElementById('portalContainer').style.display = 'flex';
        
        updateUI();
      } else {
        document.getElementById('authContainer').style.display = 'flex';
        document.getElementById('portalContainer').style.display = 'none';
        if (data && data.message) showToast(data.message);
        
        // Fetch public settings for unauthenticated state
        fetch('api.php?action=get_public_data')
          .then(r => r.json())
          .then(d => {
             if (d.success) { publicSettings = d.settings; setupAuthUI(); }
          });
      }
    } catch (e) {
      console.error("Index UI Error:", e);
      showToast('載入資料出錯: ' + e.message);
    }
  }

  function renderBottomBar(barList) {
    const bottomBar = document.getElementById('appBottomBar');
    bottomBar.innerHTML = '';
    
    // Dynamically check system_mode to replace 'loyalty' with either points or stamp system tab id
    barList.forEach(tab => {
      if (!tab.visible) return;
      if (tab.id === 'scan') return;
      
      let tabId = tab.id;
      // Map loyalty to either stamps or loyalty points mode
      if (tabId === 'loyalty') {
        tabId = systemMode === 'stamps' ? 'stamps' : 'loyalty';
      }

      const button = document.createElement('button');
      button.id = 'bottomTab-' + tabId;
      button.onclick = () => switchTab(tabId);
      
      const label = currentLang === 'zh' ? tab.labelZh : tab.labelEn;
      
      // Update icon dynamically
      let icon = tab.icon;
      if (tabId === 'stamps' && tab.icon === '🎁') {
        icon = '🎫'; // stamp system gets stamp icon
      }

      button.innerHTML = `
        <span class="ic">${icon}</span>
        <span>${label}</span>
      `;
      bottomBar.appendChild(button);
    });

    // Make default active
    setTimeout(() => {
      let defaultTab = systemMode === 'stamps' ? 'stamps' : 'loyalty';
      switchTab(defaultTab);
    }, 50);
  }

  function switchTab(tabId) {
    currentTab = tabId;
    
    // Deactivate all tabs
    document.querySelectorAll('.app-tab').forEach(tab => tab.classList.remove('active'));
    document.querySelectorAll('.bottom-bar button').forEach(btn => btn.classList.remove('active'));

    // Activate selected tab
    const selectedTab = document.getElementById('tab-' + tabId);
    if (selectedTab) {
      selectedTab.classList.add('active');
    }
    
    const activeBtn = document.getElementById('bottomTab-' + tabId);
    if (activeBtn) {
      activeBtn.classList.add('active');
    }
  }

  function getTier(pts, stamps) {
    if (systemMode === 'stamps') {
        if (stamps >= 150) return 'black';
        if (stamps >= 50) return 'red';
        return 'yellow';
    } else {
        if (pts >= 5000) return 'black';
        if (pts >= 2000) return 'red';
        return 'yellow';
    }
  }

  // Update UI Elements based on User data
  function updateUI() {
    if (!userData) return;
    
    // User detail top bar
    document.getElementById('heroMemberName').textContent = userData.name;
    const tier = getTier(userData.points, userData.stamps);
    const tierLabels = { yellow: {zh:'亞麻會員', en:'Linen Member'}, red: {zh:'橡木會員', en:'Oak Member'}, black: {zh:'羊絨會員', en:'Cashmere Member'} };
    const tierText = currentLang === 'zh' ? tierLabels[tier].zh : tierLabels[tier].en;
    
    document.getElementById('memberTierChip').textContent = tierText;
    
    // Update Membership Card Tab
    document.getElementById('memberCardName').textContent = userData.name;
    document.getElementById('memberCardTierChip').textContent = tierText;
    
    const joinedDate = new Date(userData.joined_date);
    const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    document.getElementById('memberSinceLabel').innerHTML = `Member since<br>${monthNames[joinedDate.getMonth()]} ${joinedDate.getFullYear()}`;
    
    // Generate Actual Scannable Barcode for the membership card
    JsBarcode("#memberBarcodeSvg", userData.qr_code, {
        format: "CODE128",
        lineColor: "#ffffff",
        width: 2,
        height: 40,
        displayValue: false,
        background: "transparent",
        margin: 0
    });
    
    // Avatar dynamic username letter
    const firstChar = userData.name.trim().charAt(0).toUpperCase();
    const userAvatar = document.getElementById('userAvatar');
    userAvatar.textContent = firstChar;
    
    // Generate simple dynamic HSL color based on character code
    const charCode = firstChar.charCodeAt(0);
    const hue = (charCode * 23) % 360;
    userAvatar.style.background = `hsl(${hue}, 45%, 45%)`;

    // Check System Mode
    if (systemMode === 'stamps') {
      renderStampsCard();
    } else {
      renderPointsCard();
    }

    renderTierList(tier);
    renderEarnExtra();

    // Wheel Spins count
    document.getElementById('spinsCount').textContent = userData.spins;

    // Profile Details
    document.getElementById('profileNameDisplay').textContent = userData.name;
    document.getElementById('profileTierChipDetails').innerHTML = `🌾 <span>${tierText}</span>`;
    document.getElementById('statPts').textContent = systemMode === 'stamps' ? userData.stamps : userData.points.toLocaleString();
    document.getElementById('statPtsLabel').textContent = currentLang === 'zh' ? (systemMode === 'stamps' ? '可用印花' : '可用積分') : (systemMode === 'stamps' ? 'Stamps Left' : 'Available Pts');
    document.getElementById('statVouchersCount').textContent = userData.vouchers.filter(v => v.used == 0).length;
    document.getElementById('statSpins').textContent = userData.spins;
    document.getElementById('profileEditSummary').textContent = `${userData.name} · ${userData.gender}`;

    // Render Vouchers tab
    renderVouchersTab();
    
    // Render Rewards redemption grid (points only)
    if (systemMode === 'points') {
      renderPointsCatalog();
    }

    // Setup input fields for Edit Profile
    document.getElementById('editNameInput').value = userData.name;
    document.getElementById('editGenderInput').value = userData.gender;
    
    const editPhone = document.getElementById('editPhoneInput');
    if (editPhone) {
      editPhone.value = userData.phone || '';
      if (userData.phone_verified == 1) {
        editPhone.readOnly = true;
        editPhone.style.background = '#f4f1ea';
        editPhone.style.color = '#8e8a82';
        editPhone.style.cursor = 'not-allowed';
      } else {
        editPhone.readOnly = false;
        editPhone.style.background = '';
        editPhone.style.color = '';
        editPhone.style.cursor = '';
      }
    }

    const editEmail = document.getElementById('editEmailInput');
    if (editEmail) {
      editEmail.value = userData.email || '';
      if (userData.email_verified == 1) {
        editEmail.readOnly = true;
        editEmail.style.background = '#f4f1ea';
        editEmail.style.color = '#8e8a82';
        editEmail.style.cursor = 'not-allowed';
      } else {
        editEmail.readOnly = false;
        editEmail.style.background = '';
        editEmail.style.color = '';
        editEmail.style.cursor = '';
      }
    }
    
    document.getElementById('toggleBiometric').checked = userData.biometric_enabled == 1;

    // Render wheel prizes list inside draw tab
    renderWheelPrizesList();

    // Render account verification cards
    renderVerificationPerks();
  }

  function renderTierList(currentTier) {
      const listEl = document.getElementById('tierList');
      listEl.innerHTML = '';
      
      let tiersData = [];
      if (systemMode === 'stamps') {
          tiersData = [
              { id: 'yellow', name: currentLang === 'zh' ? '亞麻會員' : 'Linen Member', range: currentLang === 'zh' ? '0-49 累積印花' : '0-49 Stamps', perks: currentLang === 'zh' ? '迎新贈送 3 個印花 • 每次消費集 1 個印花 • 生日專屬香薰禮' : 'Welcome 3 stamps • 1 stamp per purchase • Birthday gift' },
              { id: 'red', name: currentLang === 'zh' ? '橡木會員' : 'Oak Member', range: currentLang === 'zh' ? '50-149 累積印花' : '50-149 Stamps', perks: currentLang === 'zh' ? '享有亞麻會員所有權益 • 優先預購家居新品 • 每月會員日雙倍印花' : 'All Linen perks • Priority new arrivals • Double stamps on member day' },
              { id: 'black', name: currentLang === 'zh' ? '羊絨會員' : 'Cashmere Member', range: currentLang === 'zh' ? '150+ 累積印花' : '150+ Stamps', perks: currentLang === 'zh' ? '享有橡木會員所有權益 • 港澳全單免運費 • 年度私享晚宴邀請' : 'All Oak perks • Free shipping HK/MO • Annual private dinner invite' }
          ];
      } else {
          tiersData = [
              { id: 'yellow', name: currentLang === 'zh' ? '亞麻會員' : 'Linen Member', range: currentLang === 'zh' ? '0 - 1,999 積分' : '0 - 1,999 Pts', perks: currentLang === 'zh' ? '迎新優惠券、生日驚喜禮遇' : 'Welcome voucher, Birthday surprise' },
              { id: 'red', name: currentLang === 'zh' ? '橡木會員' : 'Oak Member', range: currentLang === 'zh' ? '2,000 - 4,999 積分' : '2,000 - 4,999 Pts', perks: currentLang === 'zh' ? '全單 95 折、每月免費幸運抽獎' : '5% off all orders, Monthly lucky draw' },
              { id: 'black', name: currentLang === 'zh' ? '羊絨會員' : 'Cashmere Member', range: currentLang === 'zh' ? '5,000+ 積分' : '5,000+ Pts', perks: currentLang === 'zh' ? '全單 9 折、專屬家居佈置顧問服務、新品優先預覽' : '10% off, Dedicated home consultant, Priority preview' }
          ];
      }

      tiersData.forEach(t => {
          const isCurrent = t.id === currentTier;
          const currentChip = isCurrent ? `<span class="current-chip" data-i18n-zh="目前等級" data-i18n-en="Current">目前等級</span>` : '';
          
          listEl.innerHTML += `
              <div class="tier-list-item ${isCurrent ? 'current' : ''}">
                  <div class="tier-dot"></div>
                  <div style="flex:1;">
                      <div class="tname">${t.name} ${currentChip}</div>
                      <div class="trange">${t.range}</div>
                      <div class="tperks">${t.perks}</div>
                  </div>
              </div>
          `;
      });
  }

  function renderPointsCard() {
    document.getElementById('pointsVal').textContent = userData.points.toLocaleString();
    
    // Progress calculation
    let nextTierName = '橡木會員';
    let nextTierPoints = 2000;
    let progressPct = 0;
    
    const pts = userData.points;
    if (pts >= 5000) {
      nextTierName = '已達最高等级';
      nextTierPoints = 5000;
      progressPct = 100;
      document.getElementById('pointsProgressCaption').innerHTML = currentLang === 'zh' ? '您已是最高等級的 <b>羊絨會員</b>' : 'You are at maximum <b>Cashmere Member</b> tier';
    } else if (pts >= 2000) {
      nextTierName = currentLang === 'zh' ? '羊絨會員' : 'Cashmere Member';
      nextTierPoints = 5000;
      progressPct = ((pts - 2000) / 3000) * 100;
      const left = 5000 - pts;
      document.getElementById('pointsProgressCaption').innerHTML = currentLang === 'zh' ? `距離 <b>${nextTierName}</b> 還有 <b>${left.toLocaleString()} 積分</b>` : `<b>${left.toLocaleString()} Points</b> to <b>${nextTierName}</b>`;
    } else {
      nextTierName = currentLang === 'zh' ? '橡木會員' : 'Oak Member';
      nextTierPoints = 2000;
      progressPct = (pts / 2000) * 100;
      const left = 2000 - pts;
      document.getElementById('pointsProgressCaption').innerHTML = currentLang === 'zh' ? `距離 <b>${nextTierName}</b> 還有 <b>${left.toLocaleString()} 積分</b>` : `<b>${left.toLocaleString()} Points</b> to <b>${nextTierName}</b>`;
    }
    
    document.getElementById('tierProgressCaption').innerHTML = document.getElementById('pointsProgressCaption').innerHTML;
    document.getElementById('pointsProgressFill').style.width = `${progressPct}%`;
  }

  function renderStampsCard() {
    const grid = document.getElementById('stampGridContainer');
    grid.innerHTML = '';
    
    const current = userData.stamps % 10;
    const isCompleted = userData.stamps > 0 && current === 0; // Exactly multiple of 10
    
    const displayCount = isCompleted ? 10 : current;

    for (let i = 1; i <= 10; i++) {
      const isFilled = i <= displayCount;
      const isGift = i === 10;
      
      const slot = document.createElement('div');
      slot.className = `stamp-slot ${isFilled ? 'filled' : ''} ${isGift ? 'gift' : ''}`;
      
      if (isFilled) {
        slot.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>`;
      } else if (isGift) {
        slot.textContent = '🎁';
      } else {
        slot.textContent = i;
      }
      grid.appendChild(slot);
    }

    const leftStamps = 10 - displayCount;
    if (leftStamps === 0 || isCompleted) {
      document.getElementById('stampsCaption').innerHTML = currentLang === 'zh' ? '<b>恭喜！集卡已滿！</b> 已自動為您兌換獎賞卡券' : '<b>Congrats! Card Filled!</b> Rewards issued to your coupons';
    } else {
      document.getElementById('stampsCaption').innerHTML = currentLang === 'zh' ? `再集 <b>${leftStamps} 個印花</b> 即可獲得滿額贈禮` : `<b>${leftStamps} more stamps</b> to claim reward gift`;
    }

    // Tier progress caption for stamps mode based on Lifetime/Total
    let nextTierName, left;
    const s = userData.stamps;
    if (s >= 150) {
        document.getElementById('tierProgressCaption').innerHTML = currentLang === 'zh' ? '您已是最高等級的 <b>羊絨會員</b>' : 'You are at maximum <b>Cashmere Member</b> tier';
    } else if (s >= 50) {
        left = 150 - s;
        nextTierName = currentLang === 'zh' ? '羊絨會員' : 'Cashmere Member';
        document.getElementById('tierProgressCaption').innerHTML = currentLang === 'zh' ? `距離 ${nextTierName} 還有 <b>${left} 個印花</b>` : `<b>${left} Stamps</b> to ${nextTierName}`;
    } else {
        left = 50 - s;
        nextTierName = currentLang === 'zh' ? '橡木會員' : 'Oak Member';
        document.getElementById('tierProgressCaption').innerHTML = currentLang === 'zh' ? `距離 ${nextTierName} 還有 <b>${left} 個印花</b>` : `<b>${left} Stamps</b> to ${nextTierName}`;
    }
  }

  // Render point rewards list
  async function renderPointsCatalog() {
    // We already have reward items fetched in get_user_data
    const container = document.getElementById('rewardsContainer');
    container.innerHTML = '';
    
    try {
      const res = await fetch('api.php?action=get_user_data');
      const data = await res.json();
      const rewards = data.rewards || [];

      if (rewards.length === 0) {
        container.innerHTML = `<div style="grid-column: span 2; text-align:center; color: var(--ink-soft); font-size: 13px; padding: 20px;">暫未配置獎賞</div>`;
        return;
      }

      rewards.forEach(r => {
        const canClaim = userData.points >= r.cost;
        const name = currentLang === 'zh' ? r.name_zh : (r.name_en || r.name_zh);

        const card = document.createElement('div');
        card.className = 'reward-card';
        card.innerHTML = `
          <div class="r-icon">${r.icon}</div>
          <div class="r-name">${name}</div>
          <div class="r-cost">${r.cost} ${currentLang === 'zh' ? '積分' : 'Points'}</div>
          <div class="r-action">
            <button class="claim-btn claim-action-btn" onclick="claimReward(${r.id})" ${canClaim ? '' : 'disabled'}>
              ${currentLang === 'zh' ? '免費兌換' : 'Redeem'}
            </button>
          </div>
        `;
        container.appendChild(card);
      });
    } catch(e) {}
  }

  // Claim Reward API Action
  async function claimReward(id) {
    if (isSpinning) return;
    try {
      const form = new FormData();
      form.append('reward_id', id);
      const res = await fetch('api.php?action=redeem_reward', {
        method: 'POST',
        body: form
      });
      const data = await res.json();
      if (data.success) {
        showToast(currentLang === 'zh' ? '兌換成功！' : 'Redemption successful!');
        loadUserData(); // Refresh data
      } else {
        showToast(data.message);
      }
    } catch (e) {
      showToast('連線失敗');
    }
  }

  // Render Wheel Prizes list
  function renderWheelPrizesList() {
    const listEl = document.getElementById('prizeChipGrid');
    listEl.innerHTML = '';
    
    // Render visual labels inside Canvas/CSS segments
    const labelsContainer = document.getElementById('wheelLabels');
    labelsContainer.innerHTML = '';
    
    if (!wheelPrizes || wheelPrizes.length === 0) {
      listEl.innerHTML = '<div style="grid-column:span 2; text-align:center; color:var(--ink-soft); font-size:13px; padding:10px;">暫無獎品配置</div>';
      const wheel = document.getElementById('wheel');
      wheel.style.background = '#E8E5DF';
      return;
    }

    // Build wheel visual segments proportioned by weights
    const totalWeight = wheelPrizes.reduce((sum, p) => sum + parseInt(p.weight || 0, 10), 0);
    let startAngle = 0;
    let cssGradients = [];
    const defaultColors = ['#788A6E', '#C4A47C', '#8C9CA6', '#5C6B54', '#A38561', '#D4C5B0'];

    wheelPrizes.forEach((p, idx) => {
      const name = currentLang === 'zh' ? p.name_zh : (p.name_en || p.name_zh);
      
      // Chip display list
      const chip = document.createElement('div');
      chip.className = 'prize-chip';
      chip.textContent = name;
      listEl.appendChild(chip);

      const weightVal = parseInt(p.weight || 0, 10);
      const segmentAngle = totalWeight > 0 ? (weightVal / totalWeight) * 360 : (360 / wheelPrizes.length);
      const centerAngle = startAngle + (segmentAngle / 2);

      // Canvas element styling helper or pie slice (using rotation css)
      const labelDiv = document.createElement('div');
      labelDiv.className = 'wheel-label';
      labelDiv.style.transform = `rotate(${centerAngle}deg) translateY(-94px)`;
      labelDiv.textContent = name;
      labelsContainer.appendChild(labelDiv);

      const color = p.color || defaultColors[idx % defaultColors.length];
      cssGradients.push(`${color} ${startAngle}deg ${startAngle + segmentAngle}deg`);

      startAngle += segmentAngle;
    });

    // Apply color pie slice background to the wheel
    const wheel = document.getElementById('wheel');
    wheel.style.background = `conic-gradient(${cssGradients.join(', ')})`;
  }

  // Lucky Draw Spin Logic
  async function spinWheel() {
    if (isSpinning) return;
    if (userData.spins <= 0) {
      showToast(currentLang === 'zh' ? '您的抽獎次數不足' : 'Out of spins');
      return;
    }

    isSpinning = true;
    const spinBtn = document.getElementById('spinBtn');
    spinBtn.disabled = true;

    try {
      const res = await fetch('api.php?action=spin_wheel');
      const data = await res.json();

      if (data.success) {
        const winningIndex = data.winningIndex;
        
        // Find starting and ending angles of the winning segment based on weights
        let accumAngle = 0;
        const totalWeight = wheelPrizes.reduce((sum, p) => sum + parseInt(p.weight || 0, 10), 0);
        let winningStartAngle = 0;
        let winningSegmentAngle = 0;

        wheelPrizes.forEach((p, idx) => {
          const weightVal = parseInt(p.weight || 0, 10);
          const segmentAngle = totalWeight > 0 ? (weightVal / totalWeight) * 360 : (360 / wheelPrizes.length);
          if (idx === winningIndex) {
            winningStartAngle = accumAngle;
            winningSegmentAngle = segmentAngle;
          }
          accumAngle += segmentAngle;
        });

        // To align top pointer (0deg) with center of winning segment
        const centerAngle = winningStartAngle + (winningSegmentAngle / 2);
        const targetSliceDeg = 360 - centerAngle;
        const finalRotation = 1800 + targetSliceDeg; // 5 full spins + slice offset

        const wheel = document.getElementById('wheel');
        wheel.style.transition = 'transform 4s cubic-bezier(0.15, 0.7, 0.15, 0.99)';
        wheel.style.transform = `rotate(${finalRotation}deg)`;

        setTimeout(() => {
          // Reset wheel rotation in background
          wheel.style.transition = 'none';
          const normalizedDeg = finalRotation % 360;
          wheel.style.transform = `rotate(${normalizedDeg}deg)`;

          // Show winning modal
          document.getElementById('prizeTitle').textContent = currentLang === 'zh' ? '恭喜獲得！' : 'Congratulations!';
          document.getElementById('prizeSub').textContent = currentLang === 'zh' ? data.messageZh : data.messageEn;
          
          let emoji = '🎁';
          if (data.prize.type === 'points') emoji = '🪙';
          else if (data.prize.type === 'stamps') emoji = '🎫';
          else if (data.prize.type === 'voucher') emoji = '🏷️';
          else emoji = '🪴';
          document.getElementById('prizeEmoji').textContent = emoji;

          openModal('prizeOverlay');

          // Unlock and refresh
          isSpinning = false;
          spinBtn.disabled = false;
          loadUserData();
        }, 4100);

      } else {
        showToast(data.message);
        isSpinning = false;
        spinBtn.disabled = false;
      }
    } catch (e) {
      showToast('連線失敗');
      isSpinning = false;
      spinBtn.disabled = false;
    }
  }

  // Render Vouchers tab
  function renderVouchersTab() {
    const list = document.getElementById('voucherList');
    list.innerHTML = '';
    
    const activeVouchers = userData.vouchers || [];
    const unused = activeVouchers.filter(v => v.used == 0);
    
    if (unused.length === 0) {
      list.innerHTML = `
        <div class="voucher-empty">
          <div class="ic">🏷️</div>
          <p data-i18n-zh="您目前沒有可用的優惠券" data-i18n-en="You don't have any vouchers yet">您目前沒有可用的優惠券</p>
        </div>
      `;
      applyTranslations();
      return;
    }

    unused.forEach(v => {
      const card = document.createElement('div');
      card.className = 'voucher-card';
      card.onclick = () => showVoucherQR(v.name, v.code);
      card.innerHTML = `
        <div class="voucher-stub">🎫</div>
        <div class="voucher-body">
          <div class="vname">${v.name}</div>
          <div class="vcode">${v.code}</div>
          <div class="vmeta" data-i18n-zh="有效期至: ${v.expiry_date}" data-i18n-en="Expires: ${v.expiry_date}">有效期至: ${v.expiry_date}</div>
        </div>
      `;
      list.appendChild(card);
    });
    applyTranslations();
  }

  function showVoucherQR(name, code) {
    document.getElementById('redeemName').textContent = name;
    document.getElementById('redeemCode').textContent = code;
    createRealQRCode('redeemQr', code);
    openModal('redeemOverlay');
  }

  // Handle receipt selection image label change
  function updateSelectedFileLabel() {
    const input = document.getElementById('receiptFileInput');
    const label = document.getElementById('fileSelectedLabel');
    const btn = document.getElementById('receiptSubmitBtn');
    
    if (input.files && input.files.length > 0) {
      label.textContent = `已選擇收據: ${input.files[0].name}`;
      btn.disabled = false;
    } else {
      label.textContent = '';
      btn.disabled = true;
    }
  }

  // Submit receipt upload to database
  async function handleReceiptUpload(e) {
    e.preventDefault();
    const input = document.getElementById('receiptFileInput');
    if (!input.files || input.files.length === 0) return;

    const btn = document.getElementById('receiptSubmitBtn');
    btn.disabled = true;
    btn.textContent = currentLang === 'zh' ? '正在上傳...' : 'Uploading...';

    const form = new FormData();
    form.append('receipt_image', input.files[0]);

    try {
      const res = await fetch('api.php?action=submit_receipt', {
        method: 'POST',
        body: form
      });
      const data = await res.json();
      if (data.success) {
        showToast(data.message);
        input.value = '';
        updateSelectedFileLabel();
        switchTab('profile'); // Switch to profile tab
      } else {
        showToast(data.message);
      }
    } catch(err) {
      showToast('上傳發生異常錯誤');
    } finally {
      btn.textContent = currentLang === 'zh' ? '📤 上傳收據審核' : '📤 Upload for Review';
    }
  }

  // Toggle Biometric setting
  async function toggleBiometricSetting() {
    const value = document.getElementById('toggleBiometric').checked ? 1 : 0;
    const form = new FormData();
    form.append('setting', 'biometric');
    form.append('value', value);
    
    try {
      const res = await fetch('api.php?action=toggle_setting', {
        method: 'POST',
        body: form
      });
      const data = await res.json();
      if (data.success) {
        showToast(data.message);
        loadUserData();
      }
    } catch (e) {
      showToast('更新失敗');
    }
  }

  // Open edit profile details
  function openEditProfile() {
    openModal('editProfileOverlay');
  }

  async function saveProfile() {
    const name = document.getElementById('editNameInput').value.trim();
    const gender = document.getElementById('editGenderInput').value;
    const phone = document.getElementById('editPhoneInput').value.trim();
    const email = document.getElementById('editEmailInput').value.trim();

    if (!name) {
      showToast('姓名不能為空');
      return;
    }

    const form = new FormData();
    form.append('name', name);
    form.append('gender', gender);
    form.append('phone', phone);
    form.append('email', email);

    try {
      const res = await fetch('api.php?action=update_profile', {
        method: 'POST',
        body: form
      });
      const data = await res.json();
      if (data.success) {
        showToast(currentLang === 'zh' ? '個人資料已更新' : 'Profile updated!');
        closeModal('editProfileOverlay');
        loadUserData();
      } else {
        showToast(data.message);
      }
    } catch (e) {
      showToast('儲存出錯');
    }
  }

  // Sign out
  async function handleLogout() {
    try {
      const res = await fetch('api.php?action=logout');
      const data = await res.json();
      if (data.success) {
        userData = null;
        showToast('已安全登出');
        loadUserData();
      }
    } catch(e) {}
  }

  // Authenticate submission
  async function handleAuthSubmit(e, type) {
    e.preventDefault();
    
    const form = new FormData();
    if (type === 'login') {
      form.append('username', document.getElementById('loginUsername').value);
      form.append('password', document.getElementById('loginPassword').value);
    } else {
      const pwd = document.getElementById('regPassword').value;
      const confirmPwd = document.getElementById('regConfirmPassword').value;
      
      if (pwd !== confirmPwd) {
          showToast(currentLang === 'zh' ? '兩次輸入的密碼不一致' : 'Passwords do not match');
          return;
      }
        
      form.append('username', document.getElementById('regUsername').value);
      form.append('name', document.getElementById('regName').value);
      form.append('contact', document.getElementById('regContact').value); // Save contact as email/phone identifier
      form.append('gender', document.getElementById('regGender').value);
      form.append('password', pwd);
      form.append('biometric_enabled', document.getElementById('regBiometric').checked ? 1 : 0);
    }

    try {
      // Mocking 30 days login expiration check just to demonstrate the logic.
      if (type === 'login' && document.getElementById('loginUsername').value === 'expired_user') {
          document.getElementById('loginStepBasic').style.display = 'none';
          document.getElementById('loginStepOtp').style.display = 'block';
          showToast(currentLang === 'zh' ? '此帳戶超過30天未登入，請完成 OTP 驗證。' : 'Login expired. OTP required.');
          
          // Auto trigger OTP sending for expired login
          const user = document.getElementById('loginUsername').value;
          const otpForm = new FormData(); otpForm.append('contact', user);
          fetch('api.php?action=send_otp', { method: 'POST', body: otpForm });
          
          document.getElementById('loginOtpInput').dataset.user = user;
          startOtpCooldown('btnLoginResendOtp'); // Start cooldown for login resend button
          return;
      }

      const res = await fetch(`api.php?action=${type}`, {
        method: 'POST',
        body: form
      });
      const data = await res.json();
      if (data.success) {
        showToast(data.message);
        // Only reload data after successful login or registration
        if (type === 'login' || type === 'register') {
            document.getElementById('authContainer').style.display = 'none';
            document.getElementById('portalContainer').style.display = 'flex';
            loadUserData();
        }
      } else {
        // If API returns requires_otp, show the OTP screen (backend enforcement)
        if (data.requires_otp) {
            document.getElementById('loginStepBasic').style.display = 'none';
            document.getElementById('loginStepOtp').style.display = 'block';
            showToast(data.message);
            
            const user = document.getElementById('loginUsername').value;
            const otpForm = new FormData(); otpForm.append('contact', user);
            fetch('api.php?action=send_otp', { method: 'POST', body: otpForm });
            document.getElementById('loginOtpInput').dataset.user = user;
            startOtpCooldown('btnLoginResendOtp'); // Cooldown!
        } else {
            showToast(data.message);
        }
      }
    } catch(err) {
      showToast('網路提交失敗');
    }
  }

  // Trigger simulated Biometric sign-in / key register
  function triggerBiometricAuth(mode) {
    const username = document.getElementById('loginUsername').value.trim();
    if (mode === 'login' && !username) {
      showToast('請先輸入用戶名以執行生物辨識匹配 / Please enter username first');
      return;
    }

    const overlay = document.getElementById('bioScanOverlay');
    const msg = document.getElementById('bioScanMsg');
    
    document.getElementById('bioScanIcon').textContent = '👤';
    msg.textContent = currentLang === 'zh' ? '正在驗證面容/指紋...' : 'Scanning FaceID/TouchID...';
    overlay.classList.add('show');

    setTimeout(async () => {
      // Simulate scanning completed successfully
      msg.textContent = currentLang === 'zh' ? '辨識成功，登入中...' : 'Biometric match success!';
      document.getElementById('bioScanIcon').textContent = '✅';
      
      setTimeout(async () => {
        overlay.classList.remove('show');
        
        // Call API
        const form = new FormData();
        form.append('username', username);
        form.append('biometric', 'true');

        try {
          const res = await fetch('api.php?action=login', {
            method: 'POST',
            body: form
          });
          const data = await res.json();
          if (data.success) {
            showToast(data.message);
            document.getElementById('authContainer').style.display = 'none';
            document.getElementById('portalContainer').style.display = 'flex';
            loadUserData();
          } else {
            showToast(data.message);
          }
        } catch(e) {
          showToast('連線驗證失敗');
        }
      }, 800);
    }, 1800);
  }

  // Startup initialization
  window.addEventListener('load', () => {
    // We check API first to see if logged in.
    fetch('api.php?action=get_user_data')
    .then(r => r.json())
    .then(d => {
        if(d.success) {
            // Logged in
            loadUserData();
        } else {
            // Not logged in, get public data for OTP setup
            fetch('api.php?action=get_public_data')
              .then(pr => pr.json())
              .then(pd => {
                 if(pd.success) { publicSettings = pd.settings; setupAuthUI(); }
                 document.getElementById('authContainer').style.display = 'block';
              });
        }
    });
  });
</script>
</body>
</html>