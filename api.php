<?php
// api.php - Backend API for CASA & CO. Membership System
session_start();
require_once 'db.php';
header('Content-Type: application/json');

// Auto-create user_tasks table if not exists
$pdo->exec("CREATE TABLE IF NOT EXISTS user_tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    task_type VARCHAR(50),
    status VARCHAR(20) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Auto-create tasks_config table for dynamic Extra Earn tasks
$pdo->exec("CREATE TABLE IF NOT EXISTS tasks_config (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name_zh VARCHAR(255) NOT NULL,
    name_en VARCHAR(255) NOT NULL,
    desc_zh TEXT,
    desc_en TEXT,
    reward_type VARCHAR(50) NOT NULL,
    reward_amount INT NOT NULL
)");

// Auto-create member_spend table
$pdo->exec("CREATE TABLE IF NOT EXISTS member_spend (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Auto-add new columns to existing tables
try { $pdo->exec("ALTER TABLE tasks_config ADD COLUMN task_type VARCHAR(50) DEFAULT 'manual'"); } catch(Exception $e){}
try { $pdo->exec("ALTER TABLE tasks_config ADD COLUMN target_value INT DEFAULT 0"); } catch(Exception $e){}

// Auto-add new columns to existing users table (Safely catch if already exists)
try { $pdo->exec("ALTER TABLE users ADD COLUMN phone VARCHAR(20) DEFAULT ''"); } catch(Exception $e){}
try { $pdo->exec("ALTER TABLE users ADD COLUMN last_login_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP"); } catch(Exception $e){}
try { $pdo->exec("ALTER TABLE users ADD COLUMN phone_verified TINYINT DEFAULT 0"); } catch(Exception $e){}
try { $pdo->exec("ALTER TABLE users ADD COLUMN email_verified TINYINT DEFAULT 0"); } catch(Exception $e){}

// Insert default tasks if table is empty
try {
    $stmtTaskCount = $pdo->query("SELECT COUNT(*) FROM tasks_config");
    if ($stmtTaskCount && $stmtTaskCount->fetchColumn() == 0) {
        $pdo->prepare("INSERT INTO tasks_config (name_zh, name_en, desc_zh, desc_en, reward_type, reward_amount) VALUES (?, ?, ?, ?, ?, ?)")->execute(['分享家居佈置照片', 'Share Room Photo', '上傳您的房間佈置，審核通過即贈送獎賞。', 'Upload room photo, get rewarded after review.', 'points', 200]);
        $pdo->prepare("INSERT INTO tasks_config (name_zh, name_en, desc_zh, desc_en, reward_type, reward_amount) VALUES (?, ?, ?, ?, ?, ?)")->execute(['推薦好友入會', 'Refer a Friend', '好友完成首單，您與好友皆可獲得額外獎勵。', 'Reward when friend makes first order.', 'points', 500]);
        $pdo->prepare("INSERT INTO tasks_config (name_zh, name_en, desc_zh, desc_en, reward_type, reward_amount) VALUES (?, ?, ?, ?, ?, ?)")->execute(['分享家居佈置照片', 'Share Room Photo', '上傳您的房間佈置，審核通過即贈送獎賞。', 'Upload room photo, get rewarded after review.', 'stamps', 2]);
        $pdo->prepare("INSERT INTO tasks_config (name_zh, name_en, desc_zh, desc_en, reward_type, reward_amount) VALUES (?, ?, ?, ?, ?, ?)")->execute(['推薦好友入會', 'Refer a Friend', '好友完成首單，您與好友皆可獲得額外獎勵。', 'Reward when friend makes first order.', 'stamps', 5]);
    }
} catch (Exception $e) {}

// Auto-create staff_users table for POS/Staff system
$pdo->exec("CREATE TABLE IF NOT EXISTS staff_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Create default staff account if table is empty (username: staff, password: staff)
try {
    $stmtStaffCount = $pdo->query("SELECT COUNT(*) FROM staff_users");
    if ($stmtStaffCount && $stmtStaffCount->fetchColumn() == 0) {
        $defaultStaffPwd = password_hash('staff', PASSWORD_BCRYPT);
        $pdo->prepare("INSERT INTO staff_users (username, password, name) VALUES (?, ?, ?)")->execute(['staff', $defaultStaffPwd, '門市店員']);
    }
} catch (Exception $e) {}

function json_encode_safe($data) {
    $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    if ($json === false) {
        return '{"success":false,"message":"JSON encoding failed: ' . json_last_error_msg() . '"}';
    }
    return $json;
}

// Helper to check user session
function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode_safe(['success' => false, 'message' => 'Unauthorized login required']);
        exit;
    }
    return $_SESSION['user_id'];
}

// Helper to check staff session
function requireStaffLogin() {
    if (!isset($_SESSION['staff_id'])) {
        http_response_code(401);
        echo json_encode_safe(['success' => false, 'message' => 'Staff unauthorized login required']);
        exit;
    }
    return $_SESSION['staff_id'];
}

// Helper to generate a random voucher code
function generateVoucherCode() {
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $code = '';
    for ($i = 0; $i < 6; $i++) {
        $code .= $chars[rand(0, strlen($chars) - 1)];
    }
    return $code;
}

// Helper to log user activities
function addActivityLog($pdo, $icon, $text_zh, $text_en) {
    try {
        $stmt = $pdo->prepare("INSERT INTO activity_logs (icon, text_zh, text_en) VALUES (?, ?, ?)");
        $stmt->execute([$icon, $text_zh, $text_en]);
    } catch (Exception $e) {}
}

// Fetch public settings for login/register page (OTP toggles, logos)
function getPublicSettings($pdo) {
    $settings = [];
    try {
        $stmtS = $pdo->query("SELECT setting_key, setting_value FROM settings");
        if ($stmtS) { foreach ($stmtS->fetchAll() as $row) $settings[$row['setting_key']] = $row['setting_value']; }
    } catch(Exception $e){}
    return $settings;
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

// 加上 Try-Catch 攔截所有的伺服器致命錯誤 (防止發生隱形的 500 Error)
try {
    switch ($action) {
        // ============================================
        // USER ACTIONS
        // ============================================
        case 'get_public_data':
            echo json_encode_safe(['success' => true, 'settings' => getPublicSettings($pdo)]);
            break;

        case 'send_otp':
            // MOCK OTP Sending (In reality trigger SMS or Email API here)
            $contact = $_POST['contact'] ?? '';
            $action_type = trim($_POST['action_type'] ?? '');

            if (empty($contact)) {
                echo json_encode_safe(['success' => false, 'message' => '請提供聯絡方式']);
                exit;
            }

            // 註冊時檢查聯絡方式是否已被註冊
            if ($action_type === 'register') {
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE (phone = ? AND phone != '') OR (email = ? AND email != '')");
                if ($stmt && $stmt->execute([$contact, $contact])) {
                    if ($stmt->fetchColumn() > 0) {
                        echo json_encode_safe(['success' => false, 'message' => '此聯絡方式已被註冊 / Contact already registered']);
                        exit;
                    }
                }
            }

            $_SESSION['mock_otp'] = '123456'; 
            echo json_encode_safe(['success' => true, 'message' => "OTP 已發送至 {$contact} (測試用請輸入: 123456)"]);
            break;

        case 'verify_otp':
            $otp = $_POST['otp'] ?? '';
            if ($otp === '123456' || (isset($_SESSION['mock_otp']) && $otp === $_SESSION['mock_otp'])) {
                echo json_encode_safe(['success' => true, 'message' => '驗證成功']);
            } else {
                echo json_encode_safe(['success' => false, 'message' => 'OTP 代碼錯誤']);
            }
            break;

        case 'send_profile_verify_otp':
            $userId = requireLogin();
            $type = trim($_POST['type'] ?? ''); // 'phone' or 'email'
            $value = trim($_POST['value'] ?? '');
            
            if (empty($type) || empty($value)) {
                echo json_encode_safe(['success' => false, 'message' => '手機號碼或電子信箱不能為空']);
                exit;
            }
            if ($type === 'email' && strpos($value, '@') === false) {
                echo json_encode_safe(['success' => false, 'message' => '電子信箱格式錯誤']);
                exit;
            }

            // Check if another verified account already has this value
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE id != ? AND ($type = ? AND {$type}_verified = 1)");
            $stmt->execute([$userId, $value]);
            if ($stmt->fetchColumn() > 0) {
                echo json_encode_safe(['success' => false, 'message' => '此聯絡資料已被其他已驗證的帳戶綁定']);
                exit;
            }

            $_SESSION['profile_verify_otp'] = [
                'user_id' => $userId,
                'type' => $type,
                'value' => $value,
                'code' => '123456'
            ];
            echo json_encode_safe(['success' => true, 'message' => "驗證碼已發送至 {$value} (測試用請輸入: 123456)"]);
            break;

        case 'verify_profile_contact':
            $userId = requireLogin();
            $otp = trim($_POST['otp'] ?? '');
            
            if (!isset($_SESSION['profile_verify_otp']) || $_SESSION['profile_verify_otp']['user_id'] != $userId) {
                echo json_encode_safe(['success' => false, 'message' => '無效的驗證會話，請重新獲取驗證碼']);
                exit;
            }

            $saved = $_SESSION['profile_verify_otp'];
            if ($otp !== '123456' && $otp !== $saved['code']) {
                echo json_encode_safe(['success' => false, 'message' => '驗證碼錯誤 / Invalid OTP']);
                exit;
            }

            $type = $saved['type'];
            $value = $saved['value'];

            $stmt = $pdo->prepare("UPDATE users SET $type = ?, {$type}_verified = 1 WHERE id = ?");
            if ($stmt && $stmt->execute([$value, $userId])) {
                $stmtUser = $pdo->prepare("SELECT name FROM users WHERE id = ?");
                $stmtUser->execute([$userId]);
                $userName = $stmtUser->fetchColumn();
                
                $logTxtZh = "會員 <b>$userName</b> 驗證綁定了其" . ($type === 'phone' ? '手機號碼' : '電子信箱');
                $logTxtEn = "Member <b>$userName</b> verified and bound their " . ($type === 'phone' ? 'phone' : 'email');
                addActivityLog($pdo, '🟢', $logTxtZh, $logTxtEn);

                unset($_SESSION['profile_verify_otp']);
                echo json_encode_safe(['success' => true, 'message' => '驗證綁定成功！']);
            } else {
                echo json_encode_safe(['success' => false, 'message' => '資料庫更新失敗']);
            }
            break;

        case 'login':
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $biometric = isset($_POST['biometric']) && $_POST['biometric'] === 'true';

            if (empty($username)) {
                echo json_encode_safe(['success' => false, 'message' => '請輸入用戶名 / Username required']);
                exit;
            }

            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR phone = ? OR email = ?");
            if ($stmt && $stmt->execute([$username, $username, $username])) {
                $user = $stmt->fetch();
            } else {
                $user = false;
            }

            if ($biometric) {
                $settings = getPublicSettings($pdo);
                $bio_enabled = intval($settings['biometric_login_enabled'] ?? 0);
                if ($bio_enabled !== 1) {
                    echo json_encode_safe(['success' => false, 'message' => '生物辨識登入功能尚未啟用 / Biometric login is disabled']);
                    exit;
                }
                if ($user && $user['biometric_enabled'] == 1) {
                    // Biometric bypass password
                } else {
                    echo json_encode_safe(['success' => false, 'message' => '尚未啟用生物辨識登入或用戶不存在']);
                    exit;
                }
            } else {
                if (!$user || !password_verify($password, $user['password'])) {
                    echo json_encode_safe(['success' => false, 'message' => '用戶名或密碼不正確']);
                    exit;
                }
            }

            // --- OTP Expiration Check ---
            $settings = getPublicSettings($pdo);
            $otp_enabled = intval($settings['otp_enabled'] ?? 1);
            $expiry_days = intval($settings['otp_expiry_days'] ?? 30);
            
            if ($otp_enabled === 1 && !empty($user['last_login_date'])) {
                $last_login = strtotime($user['last_login_date']);
                $diff_days = floor((time() - $last_login) / (60 * 60 * 24));
                
                if ($diff_days > $expiry_days) {
                    echo json_encode_safe(['success' => true, 'requires_otp' => true, 'message' => '安全防護：超過 ' . $expiry_days . ' 天未登入，請完成 OTP 驗證']);
                    exit;
                }
            }

            // Login Success Update
            $updateStmt = $pdo->prepare("UPDATE users SET last_login_date = CURRENT_TIMESTAMP WHERE id = ?");
            if ($updateStmt) $updateStmt->execute([$user['id']]);
            
            $_SESSION['user_id'] = $user['id'];
            echo json_encode_safe(['success' => true, 'message' => '登入成功', 'user' => ['id' => $user['id'], 'name' => $user['name']]]);
            break;

        case 'login_with_otp':
            // Specifically called when user successfully verified OTP during an expired login
            $username = trim($_POST['username'] ?? '');
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR phone = ? OR email = ?");
            
            if ($stmt && $stmt->execute([$username, $username, $username])) {
                $user = $stmt->fetch();
                if ($user) {
                    $updStmt = $pdo->prepare("UPDATE users SET last_login_date = CURRENT_TIMESTAMP WHERE id = ?");
                    if ($updStmt) $updStmt->execute([$user['id']]);
                    
                    $_SESSION['user_id'] = $user['id'];
                    echo json_encode_safe(['success' => true, 'message' => '驗證成功，正在為您登入...', 'user' => ['id' => $user['id'], 'name' => $user['name']]]);
                } else {
                    echo json_encode_safe(['success' => false, 'message' => '系統錯誤']);
                }
            } else {
                echo json_encode_safe(['success' => false, 'message' => '資料庫錯誤']);
            }
            break;

        case 'register':
            $username = strtolower(trim($_POST['username'] ?? ''));
            $password = trim($_POST['password'] ?? '');
            $name = trim($_POST['name'] ?? '');
            $contact = trim($_POST['contact'] ?? ''); // Phone or Email based on UI setup
            $gender = trim($_POST['gender'] ?? 'Prefer not to say');
            $biometric_enabled = isset($_POST['biometric_enabled']) && $_POST['biometric_enabled'] == 1 ? 1 : 0;

            if (empty($username) || empty($password) || empty($name)) {
                echo json_encode_safe(['success' => false, 'message' => '請填寫所有必填欄位']);
                exit;
            }

            $phone = ''; $email = '';
            if (strpos($contact, '@') !== false) { $email = $contact; } else { $phone = $contact; }

            // Final safety check for duplication
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR (phone = ? AND phone != '') OR (email = ? AND email != '')");
            if ($stmt && $stmt->execute([$username, $phone, $email])) {
                if ($stmt->fetchColumn() > 0) {
                    echo json_encode_safe(['success' => false, 'message' => '用戶名、手機或信箱已被註冊']);
                    exit;
                }
            }

            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $qr_code = 'USER_QR_' . strtoupper(substr(md5($username . time()), 0, 10));

            try {
                $settings = getPublicSettings($pdo);
                $otp_enabled = intval($settings['otp_enabled'] ?? 1);
                
                $phone_verified = 0;
                $email_verified = 0;
                if ($otp_enabled === 1) {
                    if (!empty($email)) {
                        $email_verified = 1;
                    }
                    if (!empty($phone)) {
                        $phone_verified = 1;
                    }
                }

                $stmt = $pdo->prepare("INSERT INTO users (username, password, name, email, phone, gender, points, stamps, spins, biometric_enabled, qr_code, phone_verified, email_verified) VALUES (?, ?, ?, ?, ?, ?, 0, 0, 2, ?, ?, ?, ?)");
                $stmt->execute([$username, $hashed_password, $name, $email, $phone, $gender, $biometric_enabled, $qr_code, $phone_verified, $email_verified]);
                
                $userId = $pdo->lastInsertId();
                $_SESSION['user_id'] = $userId;

                $welcomeEnabled = intval($settings['welcome_voucher_enabled'] ?? 1);
                $welcomeName = $settings['welcome_voucher_name'] ?? '全單 9 折迎新優惠';
                
                if ($welcomeEnabled === 1 && !empty($welcomeName)) {
                    $stmtV = $pdo->prepare("INSERT INTO vouchers (user_id, name, code, expiry_date, used) VALUES (?, ?, ?, ?, 0)");
                    if ($stmtV) {
                        $welcomeCode = generateVoucherCode(); 
                        $stmtV->execute([$userId, $welcomeName, $welcomeCode, date('Y-m-d', strtotime('+30 days'))]);
                    }
                }

                addActivityLog($pdo, '👋', "新會員 <b>$name</b> 完成註冊", "New member <b>$name</b> joined the system");
                echo json_encode_safe(['success' => true, 'message' => '註冊成功']);
            } catch (Exception $e) {
                echo json_encode_safe(['success' => false, 'message' => '註冊發生資料庫錯誤: ' . $e->getMessage()]);
            }
            break;

        case 'logout':
            session_destroy();
            echo json_encode_safe(['success' => true, 'message' => '已登出']);
            break;

        case 'get_user_data':
            $userId = requireLogin();
            
            $stmt = $pdo->prepare("SELECT id, username, name, email, phone, gender, points, stamps, spins, biometric_enabled, joined_date, qr_code, phone_verified, email_verified FROM users WHERE id = ?");
            if ($stmt && $stmt->execute([$userId])) {
                $user = $stmt->fetch();
            } else {
                $user = false;
            }

            if (!$user) {
                session_destroy();
                echo json_encode_safe(['success' => false, 'message' => '找不到用戶資料']);
                exit;
            }

            $vouchers = [];
            $stmtV = $pdo->prepare("SELECT name, code, expiry_date, used FROM vouchers WHERE user_id = ? ORDER BY id DESC");
            if ($stmtV && $stmtV->execute([$userId])) {
                $vouchers = $stmtV->fetchAll();
            }

            $settings = getPublicSettings($pdo);

            $rewards = [];
            try { $stmtR = $pdo->query("SELECT * FROM rewards ORDER BY cost ASC"); if ($stmtR) $rewards = $stmtR->fetchAll(); } catch(Throwable $e){}

            $tasks_config = [];
            try { $stmtT = $pdo->query("SELECT * FROM tasks_config ORDER BY id ASC"); if ($stmtT) $tasks_config = $stmtT->fetchAll(); } catch(Throwable $e){}

            $wheel_prizes = [];
            try { $stmtW = $pdo->query("SELECT * FROM wheel_prizes ORDER BY id ASC"); if ($stmtW) $wheel_prizes = $stmtW->fetchAll(); } catch(Throwable $e){}

            // Fetch today's spend and tasks
            $todaySpend = 0;
            try {
                $stmtSpend = $pdo->prepare("SELECT SUM(amount) FROM member_spend WHERE user_id = ? AND DATE(created_at) = CURRENT_DATE()");
                if ($stmtSpend && $stmtSpend->execute([$userId])) {
                    $todaySpend = floatval($stmtSpend->fetchColumn() ?: 0);
                }
            } catch(Throwable $e){}

            $userTasksToday = [];
            try {
                $stmtUserTasks = $pdo->prepare("SELECT task_type, status FROM user_tasks WHERE user_id = ? AND DATE(created_at) = CURRENT_DATE()");
                if ($stmtUserTasks && $stmtUserTasks->execute([$userId])) {
                    $userTasksToday = $stmtUserTasks->fetchAll(PDO::FETCH_ASSOC);
                }
            } catch(Throwable $e){}

            echo json_encode_safe([
                'success' => true,
                'user' => $user,
                'vouchers' => $vouchers,
                'settings' => $settings,
                'rewards' => $rewards,
                'tasks_config' => $tasks_config,
                'wheel_prizes' => $wheel_prizes,
                'today_spend' => $todaySpend,
                'user_tasks_today' => $userTasksToday
            ]);
            break;

        case 'toggle_setting':
            $userId = requireLogin();
            $setting = $_POST['setting'] ?? '';
            $value = intval($_POST['value'] ?? 0);

            if ($setting === 'biometric') {
                $stmt = $pdo->prepare("UPDATE users SET biometric_enabled = ? WHERE id = ?");
                if ($stmt) $stmt->execute([$value, $userId]);
                echo json_encode_safe(['success' => true, 'message' => '生物辨識設定已更新']);
            } else {
                echo json_encode_safe(['success' => true, 'message' => '設定已更新']);
            }
            break;

        case 'update_profile':
            $userId = requireLogin();
            $name = trim($_POST['name'] ?? '');
            $gender = trim($_POST['gender'] ?? 'Prefer not to say');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');

            if (empty($name)) {
                echo json_encode_safe(['success' => false, 'message' => '姓名不能為空']);
                exit;
            }

            // Retrieve current verified flags and values to prevent modifications
            $stmtUser = $pdo->prepare("SELECT phone, email, phone_verified, email_verified FROM users WHERE id = ?");
            $stmtUser->execute([$userId]);
            $curr = $stmtUser->fetch();
            
            if ($curr) {
                if (intval($curr['phone_verified']) === 1) {
                    $phone = $curr['phone']; // locked
                }
                if (intval($curr['email_verified']) === 1) {
                    $email = $curr['email']; // locked
                }
            }

            $stmt = $pdo->prepare("UPDATE users SET name = ?, gender = ?, email = ?, phone = ? WHERE id = ?");
            if ($stmt) $stmt->execute([$name, $gender, $email, $phone, $userId]);
            
            echo json_encode_safe(['success' => true, 'message' => '個人資料已儲存']);
            break;

        case 'submit_receipt':
            echo json_encode_safe(['success' => false, 'message' => '收據申報功能已關閉 / Receipt upload is disabled']);
            break;

        case 'submit_task':
            $userId = requireLogin();
            $taskId = intval($_POST['task_id'] ?? 0);
            
            $stmtC = $pdo->prepare("SELECT * FROM tasks_config WHERE id = ?");
            if (!$stmtC || !$stmtC->execute([$taskId]) || !($taskDef = $stmtC->fetch())) {
                echo json_encode_safe(['success' => false, 'message' => '無效的任務']); 
                exit; 
            }

            $taskType = $taskDef['task_type'] ?: 'manual';
            $rewardType = $taskDef['reward_type'] ?: 'points';
            $rewardAmount = intval($taskDef['reward_amount'] ?? 0);

            // Check if already completed and approved today
            $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM user_tasks WHERE user_id = ? AND task_type = ? AND DATE(created_at) = CURRENT_DATE() AND status = 'approved'");
            $stmtCheck->execute([$userId, $taskId]);
            if ($stmtCheck->fetchColumn() > 0) {
                echo json_encode_safe(['success' => false, 'message' => '您今天已經完成此任務並領取獎勵了。']);
                exit;
            }

            if ($taskType === 'checkin') {
                $targetVal = floatval($taskDef['target_value'] ?? 0);
                if ($targetVal > 0) {
                    $todaySpend = 0;
                    $stmtSpend = $pdo->prepare("SELECT SUM(amount) FROM member_spend WHERE user_id = ? AND DATE(created_at) = CURRENT_DATE()");
                    if ($stmtSpend && $stmtSpend->execute([$userId])) {
                        $todaySpend = floatval($stmtSpend->fetchColumn() ?: 0);
                    }
                    if ($todaySpend < $targetVal) {
                        echo json_encode_safe(['success' => false, 'message' => "本日簽到需要今日消費滿 HK\$ {$targetVal}。您今日消費金額為 HK\$ {$todaySpend}，尚未達標。"]);
                        exit;
                    }
                }

                $pdo->beginTransaction();
                try {
                    $stmt = $pdo->prepare("INSERT INTO user_tasks (user_id, task_type, status) VALUES (?, ?, 'approved')");
                    $stmt->execute([$userId, $taskId]);

                    if ($rewardType === 'stamps') {
                        $stmtAdd = $pdo->prepare("UPDATE users SET stamps = LEAST(10, stamps + ?) WHERE id = ?");
                        $stmtAdd->execute([$rewardAmount, $userId]);
                    } elseif ($rewardType === 'spins') {
                        $stmtAdd = $pdo->prepare("UPDATE users SET spins = spins + ? WHERE id = ?");
                        $stmtAdd->execute([$rewardAmount, $userId]);
                    } else {
                        $stmtAdd = $pdo->prepare("UPDATE users SET points = points + ? WHERE id = ?");
                        $stmtAdd->execute([$rewardAmount, $userId]);
                    }

                    $stmtUser = $pdo->prepare("SELECT name FROM users WHERE id = ?");
                    $stmtUser->execute([$userId]);
                    $userName = $stmtUser->fetchColumn();
                    $taskName = $taskDef['name_zh'];
                    addActivityLog($pdo, '📌', "<b>$userName</b> 完成了「{$taskName}」每日簽到任務", "");

                    $pdo->commit();
                    echo json_encode_safe(['success' => true, 'message' => '簽到成功！獎勵已發放。']);
                } catch(Exception $e) {
                    $pdo->rollBack();
                    echo json_encode_safe(['success' => false, 'message' => '簽到失敗: ' . $e->getMessage()]);
                }
                exit;

            } else if ($taskType === 'spend_money') {
                // Get today's spend
                $todaySpend = 0;
                $stmtSpend = $pdo->prepare("SELECT SUM(amount) FROM member_spend WHERE user_id = ? AND DATE(created_at) = CURRENT_DATE()");
                if ($stmtSpend && $stmtSpend->execute([$userId])) {
                    $todaySpend = floatval($stmtSpend->fetchColumn() ?: 0);
                }
                $targetVal = floatval($taskDef['target_value'] ?? 0);

                if ($todaySpend < $targetVal) {
                    echo json_encode_safe(['success' => false, 'message' => "今日消費金額 (HK$ {$todaySpend}) 尚未達到任務目標 (HK$ {$targetVal})。"]);
                    exit;
                }

                $pdo->beginTransaction();
                try {
                    $stmt = $pdo->prepare("INSERT INTO user_tasks (user_id, task_type, status) VALUES (?, ?, 'approved')");
                    $stmt->execute([$userId, $taskId]);

                    if ($rewardType === 'stamps') {
                        $stmtAdd = $pdo->prepare("UPDATE users SET stamps = LEAST(10, stamps + ?) WHERE id = ?");
                        $stmtAdd->execute([$rewardAmount, $userId]);
                    } elseif ($rewardType === 'spins') {
                        $stmtAdd = $pdo->prepare("UPDATE users SET spins = spins + ? WHERE id = ?");
                        $stmtAdd->execute([$rewardAmount, $userId]);
                    } else {
                        $stmtAdd = $pdo->prepare("UPDATE users SET points = points + ? WHERE id = ?");
                        $stmtAdd->execute([$rewardAmount, $userId]);
                    }

                    $stmtUser = $pdo->prepare("SELECT name FROM users WHERE id = ?");
                    $stmtUser->execute([$userId]);
                    $userName = $stmtUser->fetchColumn();
                    $taskName = $taskDef['name_zh'];
                    addActivityLog($pdo, '📌', "<b>$userName</b> 完成了「{$taskName}」消費達標任務", "");

                    $pdo->commit();
                    echo json_encode_safe(['success' => true, 'message' => '領取成功！獎勵已發放。']);
                } catch(Exception $e) {
                    $pdo->rollBack();
                    echo json_encode_safe(['success' => false, 'message' => '領取失敗: ' . $e->getMessage()]);
                }
                exit;

            } else {
                // Manual review task
                $stmtCheckPending = $pdo->prepare("SELECT COUNT(*) FROM user_tasks WHERE user_id = ? AND task_type = ? AND status = 'pending'");
                $stmtCheckPending->execute([$userId, $taskId]);
                if ($stmtCheckPending->fetchColumn() > 0) {
                    echo json_encode_safe(['success' => false, 'message' => '此任務已提交審核，請耐心等候。']);
                    exit;
                }

                $stmt = $pdo->prepare("INSERT INTO user_tasks (user_id, task_type, status) VALUES (?, ?, 'pending')");
                if ($stmt) $stmt->execute([$userId, $taskId]);

                $stmtUser = $pdo->prepare("SELECT name FROM users WHERE id = ?");
                if ($stmtUser && $stmtUser->execute([$userId])) {
                    $userName = $stmtUser->fetchColumn();
                    $taskName = $taskDef['name_zh'];
                    addActivityLog($pdo, '📌', "<b>$userName</b> 提交了「{$taskName}」任務審核", "");
                }

                echo json_encode_safe(['success' => true, 'message' => '任務已提交審核。']);
                exit;
            }
            break;

        case 'spin_wheel':
            $userId = requireLogin();
            
            $stmtUser = $pdo->prepare("SELECT name, points, stamps, spins FROM users WHERE id = ?");
            if ($stmtUser && $stmtUser->execute([$userId])) {
                $user = $stmtUser->fetch();
            } else {
                $user = false;
            }

            if (!$user) { echo json_encode_safe(['success' => false, 'message' => '用戶不存在']); exit; }
            if ($user['spins'] <= 0) { echo json_encode_safe(['success' => false, 'message' => '抽獎次數不足 / Out of spins']); exit; }

            $stmtW = $pdo->query("SELECT * FROM wheel_prizes");
            if(!$stmtW) { echo json_encode_safe(['success' => false, 'message' => '資料庫錯誤，無法讀取獎品']); exit; }
            $prizes = $stmtW->fetchAll();

            if (empty($prizes)) { echo json_encode_safe(['success' => false, 'message' => '轉盤尚未配置獎品']); exit; }

            $totalWeight = 0;
            foreach ($prizes as $prize) { $totalWeight += intval($prize['weight']); }

            $rand = rand(1, $totalWeight);
            $currentWeightSum = 0; $winningPrize = null; $winningIndex = 0;

            foreach ($prizes as $index => $prize) {
                $currentWeightSum += intval($prize['weight']);
                if ($rand <= $currentWeightSum) {
                    $winningPrize = $prize;
                    $winningIndex = $index;
                    break;
                }
            }

            if (!$winningPrize) { $winningIndex = 0; $winningPrize = $prizes[0]; }

            $stmtDec = $pdo->prepare("UPDATE users SET spins = spins - 1 WHERE id = ?");
            if ($stmtDec) $stmtDec->execute([$userId]);

            $prizeMsgZh = '';
            $prizeMsgEn = '';

            if ($winningPrize['type'] === 'points') {
                $pts = intval($winningPrize['value']);
                $stmtAdd = $pdo->prepare("UPDATE users SET points = points + ? WHERE id = ?");
                if ($stmtAdd) $stmtAdd->execute([$pts, $userId]);
                $prizeMsgZh = "獲得了 +{$pts} 積分！"; $prizeMsgEn = "won +{$pts} Points!";
            } elseif ($winningPrize['type'] === 'stamps') {
                $stps = intval($winningPrize['value']);
                $newStamps = min(10, $user['stamps'] + $stps);
                $stmtAdd = $pdo->prepare("UPDATE users SET stamps = ? WHERE id = ?");
                if ($stmtAdd) $stmtAdd->execute([$newStamps, $userId]);
                $prizeMsgZh = "獲得了 +{$stps} 印花！"; $prizeMsgEn = "won +{$stps} Stamps!";
            } elseif ($winningPrize['type'] === 'voucher') {
                $voucherName = $winningPrize['value'];
                $code = generateVoucherCode();
                $expiry = date('Y-m-d', strtotime('+30 days'));
                $stmtAdd = $pdo->prepare("INSERT INTO vouchers (user_id, name, code, expiry_date, used) VALUES (?, ?, ?, ?, 0)");
                if ($stmtAdd) $stmtAdd->execute([$userId, $voucherName, $code, $expiry]);
                $prizeMsgZh = "抽中了「{$voucherName}」優惠券！"; $prizeMsgEn = "won a \"{$voucherName}\" Voucher!";
            } else {
                $prizeMsgZh = "很遺憾這次沒中獎，再接再厲！"; $prizeMsgEn = "didn't win this time, try again next time!";
            }

            addActivityLog($pdo, '🎡', "<b>{$user['name']}</b> 參與幸運輪盤，{$prizeMsgZh}", "");

            echo json_encode_safe(['success' => true, 'winningIndex' => $winningIndex, 'prize' => $winningPrize, 'messageZh' => $prizeMsgZh, 'messageEn' => $prizeMsgEn]);
            break;

        case 'redeem_reward':
            $userId = requireLogin();
            $rewardId = intval($_POST['reward_id'] ?? 0);

            $stmtR = $pdo->prepare("SELECT * FROM rewards WHERE id = ?");
            if ($stmtR && $stmtR->execute([$rewardId])) {
                $reward = $stmtR->fetch();
            } else { $reward = false; }
            
            if (!$reward) { echo json_encode_safe(['success' => false, 'message' => '無效的獎賞項目']); exit; }

            $stmtU = $pdo->prepare("SELECT name, points FROM users WHERE id = ?");
            if ($stmtU && $stmtU->execute([$userId])) {
                $user = $stmtU->fetch();
            } else { $user = false; }

            if (!$user || $user['points'] < $reward['cost']) { echo json_encode_safe(['success' => false, 'message' => '積分不足']); exit; }

            $stmtDec = $pdo->prepare("UPDATE users SET points = points - ? WHERE id = ?");
            if ($stmtDec) $stmtDec->execute([$reward['cost'], $userId]);

            $stmtV = $pdo->prepare("INSERT INTO vouchers (user_id, name, code, expiry_date, used) VALUES (?, ?, ?, ?, 0)");
            if ($stmtV) $stmtV->execute([$userId, $reward['name_zh'], generateVoucherCode(), date('Y-m-d', strtotime('+30 days'))]);

            addActivityLog($pdo, '🎁', "<b>{$user['name']}</b> 扣除 <b>{$reward['cost']}</b> 積分兌換了「{$reward['name_zh']}」", "");
            echo json_encode_safe(['success' => true, 'message' => '兌換成功！已發送優惠券到您的帳戶。']);
            break;

        // ============================================
        // STAFF / POS ACTIONS
        // ============================================
        case 'staff_login':
            $username = trim($_POST['username'] ?? ''); $password = trim($_POST['password'] ?? '');
            $stmt = $pdo->prepare("SELECT * FROM staff_users WHERE username = ?"); 
            if ($stmt && $stmt->execute([$username])) {
                $staff = $stmt->fetch();
            } else { $staff = false; }
            
            if ($staff && password_verify($password, $staff['password'])) {
                $_SESSION['staff_id'] = $staff['id'];
                echo json_encode_safe(['success' => true, 'message' => '店員登入成功', 'name' => $staff['name']]);
            } else { echo json_encode_safe(['success' => false, 'message' => '帳號或密碼錯誤']); }
            break;

        case 'staff_logout':
            unset($_SESSION['staff_id']); echo json_encode_safe(['success' => true]); break;

        case 'staff_get_user':
            requireStaffLogin();
            $qrCode = trim($_POST['qr_code'] ?? '');
            $stmt = $pdo->prepare("SELECT id, name, points, stamps FROM users WHERE qr_code = ?"); 
            if ($stmt && $stmt->execute([$qrCode])) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
            } else { $user = false; }

            if ($user) echo json_encode_safe(['success' => true, 'user' => $user]);
            else echo json_encode_safe(['success' => false, 'message' => '無效的會員條碼']);
            break;

        case 'staff_search_phone':
            requireStaffLogin();
            $phone = trim($_POST['phone'] ?? '');
            $stmt = $pdo->prepare("SELECT id, name, points, stamps FROM users WHERE phone = ? OR email = ?");
            if ($stmt && $stmt->execute([$phone, $phone])) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
            } else { $user = false; }

            if ($user) echo json_encode_safe(['success' => true, 'user' => $user]);
            else echo json_encode_safe(['success' => false, 'message' => '找不到該手機或電子郵件對應的會員']);
            break;

        case 'staff_add_reward':
            requireStaffLogin();
            $userId = intval($_POST['user_id'] ?? 0);
            $amount = floatval($_POST['amount'] ?? 0);
            
            $stmtU = $pdo->prepare("SELECT name, stamps FROM users WHERE id = ?"); 
            if ($stmtU && $stmtU->execute([$userId])) {
                $user = $stmtU->fetch();
            } else { $user = false; }

            if (!$user) { echo json_encode_safe(['success' => false, 'message' => '找不到該會員']); exit; }

            $settings = getPublicSettings($pdo);
            $mode = $settings['system_mode'] ?? 'points';
            
            // Log this spend in member_spend
            try {
                $stmtSpend = $pdo->prepare("INSERT INTO member_spend (user_id, amount) VALUES (?, ?)");
                if ($stmtSpend) $stmtSpend->execute([$userId, $amount]);
            } catch(Throwable $e){}

            if ($mode === 'stamps') {
                $stamps_money_rate = max(1, intval($settings['stamps_money_rate'] ?? 100));
                $stamps_reward_rate = max(1, intval($settings['stamps_reward_rate'] ?? 1));
                $rewardGranted = floor($amount / $stamps_money_rate) * $stamps_reward_rate;

                $stmt = $pdo->prepare("UPDATE users SET stamps = LEAST(10, stamps + ?) WHERE id = ?");
                if ($stmt) $stmt->execute([$rewardGranted, $userId]);

                addActivityLog($pdo, '🛍️', "<b>{$user['name']}</b> 門市消費 HK\$ {$amount}，發放 {$rewardGranted} 個印花！", "");
                echo json_encode_safe(['success' => true, 'message' => "已為 {$user['name']} 發放 {$rewardGranted} 個印花！"]);
            } else {
                $points_money_rate = max(1, intval($settings['points_money_rate'] ?? 1));
                $points_reward_rate = max(1, intval($settings['points_reward_rate'] ?? 1));
                $rewardGranted = floor($amount / $points_money_rate) * $points_reward_rate;

                $stmt = $pdo->prepare("UPDATE users SET points = points + ? WHERE id = ?");
                if ($stmt) $stmt->execute([$rewardGranted, $userId]);

                addActivityLog($pdo, '🛍️', "<b>{$user['name']}</b> 門市消費 HK\$ {$amount}，發放 {$rewardGranted} 積分！", "");
                echo json_encode_safe(['success' => true, 'message' => "已為 {$user['name']} 發放 {$rewardGranted} 積分！"]);
            }
            break;

        case 'staff_check_voucher':
            requireStaffLogin();
            $code = trim($_POST['code'] ?? '');
            $stmt = $pdo->prepare("SELECT v.*, u.name as member_name FROM vouchers v JOIN users u ON v.user_id = u.id WHERE v.code = ?"); 
            if ($stmt && $stmt->execute([$code])) {
                $voucher = $stmt->fetch(PDO::FETCH_ASSOC);
            } else { $voucher = false; }
            
            if (!$voucher) { echo json_encode_safe(['success' => false, 'message' => '無效的優惠券代碼']); exit; }
            if ($voucher['used'] == 1) { echo json_encode_safe(['success' => false, 'message' => '此優惠券已經被使用過了']); exit; }
            if (strtotime($voucher['expiry_date'] . ' 23:59:59') < time()) { echo json_encode_safe(['success' => false, 'message' => '此優惠券已過期']); exit; }
            
            echo json_encode_safe(['success' => true, 'voucher' => $voucher]);
            break;

        case 'staff_redeem_voucher':
            requireStaffLogin();
            $voucherId = intval($_POST['voucher_id'] ?? 0);
            $stmt = $pdo->prepare("UPDATE vouchers SET used = 1 WHERE id = ?");
            if ($stmt) $stmt->execute([$voucherId]);
            echo json_encode_safe(['success' => true, 'message' => '優惠券核銷成功！']);
            break;

        // ============================================
        // ADMIN ACTIONS 
        // ============================================
        case 'admin_get_data':
            $stats = ['totalMembers' => 0, 'totalVouchers' => 0, 'usedVouchers' => 0, 'totalSpins' => 0];
            $settings = getPublicSettings($pdo);
            if (!isset($settings['otp_enabled'])) $settings['otp_enabled'] = '1';
            if (!isset($settings['otp_method'])) $settings['otp_method'] = 'both';
            if (!isset($settings['otp_expiry_days'])) $settings['otp_expiry_days'] = '30';

            $users = []; $vouchers = []; $rewards = []; $wheel_prizes = [];
            $receipts = []; $tasks = []; $staff = []; $tasks_config = []; $logs = [];

            try {
                // Safe DB extractions protecting against "fetchColumn() on bool/false" PHP Error 500s!
                $q1 = $pdo->query("SELECT COUNT(*) FROM users"); if ($q1) $stats['totalMembers'] = $q1->fetchColumn();
                $q2 = $pdo->query("SELECT COUNT(*) FROM vouchers"); if ($q2) $stats['totalVouchers'] = $q2->fetchColumn();
                $q3 = $pdo->query("SELECT COUNT(*) FROM vouchers WHERE used = 1"); if ($q3) $stats['usedVouchers'] = $q3->fetchColumn();
                $q4 = $pdo->query("SELECT COUNT(*) FROM activity_logs WHERE icon = '🎡'"); if ($q4) $stats['totalSpins'] = $q4->fetchColumn() + 130;

                $usersStmt = $pdo->query("SELECT id, name, username, points, stamps, spins, joined_date, qr_code FROM users ORDER BY id DESC");
                if ($usersStmt) {
                    $users = $usersStmt->fetchAll();
                    foreach ($users as &$u) {
                        $stmtV = $pdo->prepare("SELECT name, code, expiry_date, used FROM vouchers WHERE user_id = ?");
                        if ($stmtV && $stmtV->execute([$u['id']])) {
                            $u['vouchers'] = $stmtV->fetchAll();
                        } else { $u['vouchers'] = []; }
                    }
                }

                $vStmt = $pdo->query("SELECT v.*, u.name as member_name FROM vouchers v JOIN users u ON v.user_id = u.id ORDER BY v.id DESC");
                if ($vStmt) $vouchers = $vStmt->fetchAll();

                $rStmt = $pdo->query("SELECT * FROM rewards ORDER BY cost ASC");
                if ($rStmt) $rewards = $rStmt->fetchAll();

                $wStmt = $pdo->query("SELECT * FROM wheel_prizes ORDER BY id ASC");
                if ($wStmt) $wheel_prizes = $wStmt->fetchAll();

                $recStmt = $pdo->query("SELECT r.*, u.name as member_name FROM receipts r JOIN users u ON r.user_id = u.id ORDER BY r.id DESC");
                if ($recStmt) $receipts = $recStmt->fetchAll();

                $tStmt = $pdo->query("SELECT t.*, u.name as member_name, c.name_zh as task_name, c.reward_type, c.reward_amount FROM user_tasks t JOIN users u ON t.user_id = u.id LEFT JOIN tasks_config c ON t.task_type = c.id ORDER BY t.id DESC");
                if ($tStmt) $tasks = $tStmt->fetchAll();

                $sStmt = $pdo->query("SELECT id, username, name, created_at FROM staff_users ORDER BY id DESC");
                if ($sStmt) $staff = $sStmt->fetchAll();

                $tcStmt = $pdo->query("SELECT * FROM tasks_config ORDER BY reward_type ASC, id ASC");
                if ($tcStmt) $tasks_config = $tcStmt->fetchAll();

                $lStmt = $pdo->query("SELECT * FROM activity_logs ORDER BY id DESC LIMIT 20");
                if ($lStmt) $logs = $lStmt->fetchAll();
            } catch (Throwable $e) {}

            echo json_encode_safe([
                'success' => true, 'stats' => $stats, 'settings' => $settings, 'members' => $users,
                'vouchers' => $vouchers, 'rewards' => $rewards, 'wheel_prizes' => $wheel_prizes,
                'receipts' => $receipts, 'tasks' => $tasks, 'staff' => $staff, 'tasks_config' => $tasks_config, 'logs' => $logs
            ]);
            break;

        case 'admin_update_settings':
            $params = ['system_mode', 'logo_type', 'logo_text', 'logo_image_url', 'bottom_bar', 'otp_enabled', 'otp_method', 'otp_expiry_days', 'extra_share_reward', 'extra_refer_reward', 'points_money_rate', 'points_reward_rate', 'stamps_money_rate', 'stamps_reward_rate', 'biometric_login_enabled', 'welcome_voucher_enabled', 'welcome_voucher_name'];
            foreach ($params as $key) {
                if (isset($_POST[$key])) {
                    $val = $_POST[$key];
                    $stmt = $pdo->prepare("SELECT count(*) FROM settings WHERE setting_key = ?");
                    if ($stmt && $stmt->execute([$key])) {
                        if ($stmt->fetchColumn() > 0) {
                            $stmtUpd = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
                            if ($stmtUpd) $stmtUpd->execute([$val, $key]);
                        } else {
                            $stmtIns = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)");
                            if ($stmtIns) $stmtIns->execute([$key, $val]);
                        }
                    }
                }
            }
            echo json_encode_safe(['success' => true, 'message' => '系統設定已儲存']);
            break;
        
        case 'admin_adjust_member':
            $memberId = intval($_POST['member_id'] ?? 0);
            $points = intval($_POST['points'] ?? 0);
            $stamps = intval($_POST['stamps'] ?? 0);
            $spins = intval($_POST['spins'] ?? 0);

            $stmt = $pdo->prepare("UPDATE users SET points = ?, stamps = ?, spins = ? WHERE id = ?");
            if ($stmt) $stmt->execute([$points, $stamps, $spins, $memberId]);
            
            $stmtName = $pdo->prepare("SELECT name FROM users WHERE id = ?");
            if ($stmtName && $stmtName->execute([$memberId])) {
                $name = $stmtName->fetchColumn();
                addActivityLog($pdo, '✍️', "管理員手動調整了會員 <b>$name</b> 的帳戶資料", "");
                echo json_encode_safe(['success' => true, 'message' => "已更新 $name 的帳戶資料"]);
            } else {
                echo json_encode_safe(['success' => false, 'message' => '帳戶資料已更新，但找不到該會員名稱']);
            }
            break;

        case 'admin_issue_voucher':
            $memberId = intval($_POST['member_id'] ?? 0);
            $name = trim($_POST['voucher_name'] ?? '');
            $days = intval($_POST['days'] ?? 30);
            if (empty($name)) { echo json_encode_safe(['success' => false, 'message' => '請輸入優惠券名稱']); exit; }

            $code = generateVoucherCode();
            $stmt = $pdo->prepare("INSERT INTO vouchers (user_id, name, code, expiry_date, used) VALUES (?, ?, ?, ?, 0)");
            if ($stmt) $stmt->execute([$memberId, $name, $code, date('Y-m-d', strtotime("+$days days"))]);
            
            $stmtName = $pdo->prepare("SELECT name FROM users WHERE id = ?"); 
            if ($stmtName && $stmtName->execute([$memberId])) {
                $memberName = $stmtName->fetchColumn();
                addActivityLog($pdo, '✉️', "管理員向 <b>$memberName</b> 發放了「{$name}」優惠券", "");
                echo json_encode_safe(['success' => true, 'message' => "已發送優惠券給 $memberName"]);
            } else {
                echo json_encode_safe(['success' => true, 'message' => "已成功發送優惠券"]);
            }
            break;

        case 'admin_process_receipt':
            echo json_encode_safe(['success' => false, 'message' => '收據審核功能已關閉 / Receipt review is disabled']);
            break;

        case 'admin_process_task':
            $taskId = intval($_POST['task_id'] ?? 0);
            $status = $_POST['status'] ?? 'rejected';
            $rewardGranted = intval($_POST['reward_granted'] ?? 0);
            
            $stmtT = $pdo->prepare("SELECT t.*, u.name as member_name, c.name_zh as task_name, c.reward_type, c.reward_amount FROM user_tasks t JOIN users u ON t.user_id = u.id LEFT JOIN tasks_config c ON t.task_type = c.id WHERE t.id = ?");
            if ($stmtT && $stmtT->execute([$taskId])) {
                $task = $stmtT->fetch();
            } else { $task = false; }

            if (!$task) { echo json_encode_safe(['success' => false, 'message' => '任務不存在']); exit; }

            if ($status === 'approved') {
                $stmt = $pdo->prepare("UPDATE user_tasks SET status = 'approved' WHERE id = ?");
                if ($stmt) $stmt->execute([$taskId]);
                $rewardType = $task['reward_type'] ?: 'points';
                
                if ($rewardType === 'stamps') {
                    $stmtAdd = $pdo->prepare("UPDATE users SET stamps = LEAST(10, stamps + ?) WHERE id = ?");
                    if ($stmtAdd) $stmtAdd->execute([$rewardGranted, $task['user_id']]);
                } elseif ($rewardType === 'spins') {
                    $stmtAdd = $pdo->prepare("UPDATE users SET spins = spins + ? WHERE id = ?");
                    if ($stmtAdd) $stmtAdd->execute([$rewardGranted, $task['user_id']]);
                } else {
                    $stmtAdd = $pdo->prepare("UPDATE users SET points = points + ? WHERE id = ?");
                    if ($stmtAdd) $stmtAdd->execute([$rewardGranted, $task['user_id']]);
                }
                echo json_encode_safe(['success' => true, 'message' => '任務已核准，獎勵已發放']);
            } else {
                $stmt = $pdo->prepare("UPDATE user_tasks SET status = 'rejected' WHERE id = ?");
                if ($stmt) $stmt->execute([$taskId]);
                echo json_encode_safe(['success' => true, 'message' => '任務已被駁回']);
            }
            break;

        case 'admin_add_task_config':
            $name_zh = trim($_POST['name_zh'] ?? ''); 
            $desc_zh = trim($_POST['desc_zh'] ?? ''); 
            $reward_type = trim($_POST['reward_type'] ?? $_POST['type'] ?? 'points'); 
            $reward_amount = intval($_POST['reward_amount'] ?? $_POST['amount'] ?? 0);
            $task_type = trim($_POST['task_type'] ?? 'manual');
            $target_value = intval($_POST['target_value'] ?? 0);

            $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM tasks_config WHERE name_zh = ? AND reward_type = ?"); 
            if ($stmtCheck && $stmtCheck->execute([$name_zh, $reward_type])) {
                if ($stmtCheck->fetchColumn() > 0) { echo json_encode_safe(['success' => false, 'message' => '此模式下已存在同名任務']); exit; }
            }

            $stmt = $pdo->prepare("INSERT INTO tasks_config (name_zh, name_en, desc_zh, desc_en, reward_type, reward_amount, task_type, target_value) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt && $stmt->execute([$name_zh, $name_zh, $desc_zh, $desc_zh, $reward_type, $reward_amount, $task_type, $target_value])) {
                echo json_encode_safe(['success' => true, 'message' => '新增任務成功']);
            } else {
                echo json_encode_safe(['success' => false, 'message' => '寫入失敗']);
            }
            break;

        case 'admin_update_task_config':
            $id = intval($_POST['id'] ?? 0); $field = $_POST['field'] ?? ''; $val = $_POST['value'] ?? '';
            if (in_array($field, ['name_zh', 'desc_zh', 'reward_amount'])) {
                $stmt = $pdo->prepare("UPDATE tasks_config SET $field = ? WHERE id = ?");
                if ($stmt) $stmt->execute([$val, $id]);
                echo json_encode_safe(['success' => true, 'message' => '任務更新成功']);
            } else { echo json_encode_safe(['success' => false, 'message' => '無效欄位']); }
            break;

        case 'admin_delete_task_config':
            $stmt = $pdo->prepare("DELETE FROM tasks_config WHERE id = ?");
            if ($stmt) $stmt->execute([intval($_POST['id'] ?? 0)]);
            echo json_encode_safe(['success' => true, 'message' => '任務刪除成功']);
            break;

        case 'admin_save_staff':
            $staffId = intval($_POST['staff_id'] ?? 0); $name = trim($_POST['name'] ?? ''); $username = trim($_POST['username'] ?? ''); $password = trim($_POST['password'] ?? '');
            if(empty($name) || empty($username)) { echo json_encode_safe(['success' => false, 'message' => '名稱與帳號為必填']); exit; }

            if ($staffId > 0) {
                if (!empty($password)) {
                    $stmt = $pdo->prepare("UPDATE staff_users SET name=?, username=?, password=? WHERE id=?");
                    if ($stmt) $stmt->execute([$name, $username, password_hash($password, PASSWORD_BCRYPT), $staffId]);
                } else {
                    $stmt = $pdo->prepare("UPDATE staff_users SET name=?, username=? WHERE id=?");
                    if ($stmt) $stmt->execute([$name, $username, $staffId]);
                }
                echo json_encode_safe(['success' => true, 'message' => '店員資料更新成功']);
            } else {
                if(empty($password)) { echo json_encode_safe(['success' => false, 'message' => '新增店員必須設定密碼']); exit; }
                $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM staff_users WHERE username=?"); 
                if ($stmtCheck && $stmtCheck->execute([$username])) {
                    if ($stmtCheck->fetchColumn() > 0) { echo json_encode_safe(['success' => false, 'message' => '此登入帳號已存在']); exit; }
                }
                $stmt = $pdo->prepare("INSERT INTO staff_users (name, username, password) VALUES (?, ?, ?)");
                if ($stmt) $stmt->execute([$name, $username, password_hash($password, PASSWORD_BCRYPT)]);
                echo json_encode_safe(['success' => true, 'message' => '店員新增成功']);
            }
            break;

        case 'admin_delete_staff':
            $stmt = $pdo->prepare("DELETE FROM staff_users WHERE id=?");
            if ($stmt) $stmt->execute([intval($_POST['staff_id'] ?? 0)]);
            echo json_encode_safe(['success' => true, 'message' => '店員已刪除']);
            break;

        case 'admin_update_voucher_code':
            $voucherId = intval($_POST['voucher_id'] ?? 0); 
            $code = trim($_POST['code'] ?? '');
            $expiry = trim($_POST['expiry_date'] ?? '');
            if (empty($code)) { echo json_encode_safe(['success' => false, 'message' => '優惠券代碼不能為空']); exit; }
            if (empty($expiry)) { echo json_encode_safe(['success' => false, 'message' => '到期日不能為空']); exit; }
            
            $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM vouchers WHERE code = ? AND id != ?"); 
            if ($stmtCheck && $stmtCheck->execute([$code, $voucherId])) {
                if ($stmtCheck->fetchColumn() > 0) { echo json_encode_safe(['success' => false, 'message' => '此代碼已被使用']); exit; }
            }
            
            $stmt = $pdo->prepare("UPDATE vouchers SET code = ?, expiry_date = ? WHERE id = ?");
            if ($stmt && $stmt->execute([$code, $expiry, $voucherId])) {
                echo json_encode_safe(['success' => true, 'message' => '卡券資料已成功更新']);
            } else {
                echo json_encode_safe(['success' => false, 'message' => '更新失敗']);
            }
            break;

        case 'admin_add_wheel':
            $stmt = $pdo->prepare("INSERT INTO wheel_prizes (name_zh, name_en, type, value, weight, color) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt && $stmt->execute([trim($_POST['name_zh'] ?? ''), trim($_POST['name_en'] ?? ''), trim($_POST['type'] ?? 'none'), trim($_POST['value'] ?? ''), intval($_POST['weight'] ?? 1), trim($_POST['color'] ?? '')])) {
                echo json_encode_safe(['success' => true, 'message' => '新增轉盤獎品成功']);
            } else { echo json_encode_safe(['success' => false, 'message' => '資料庫寫入失敗']); }
            break;

        case 'admin_update_wheel':
            $field = $_POST['field'] ?? ''; 
            if (in_array($field, ['name_zh', 'name_en', 'type', 'value', 'weight', 'color'])) {
                $stmt = $pdo->prepare("UPDATE wheel_prizes SET $field = ? WHERE id = ?");
                if ($stmt) $stmt->execute([$_POST['value'] ?? '', intval($_POST['id'] ?? 0)]);
                echo json_encode_safe(['success' => true, 'message' => '獎品更新成功']);
            } else { echo json_encode_safe(['success' => false, 'message' => '無效的欄位']); }
            break;

        case 'admin_delete_wheel':
            $stmt = $pdo->prepare("DELETE FROM wheel_prizes WHERE id = ?");
            if ($stmt) $stmt->execute([intval($_POST['id'] ?? 0)]);
            echo json_encode_safe(['success' => true, 'message' => '獎品刪除成功']);
            break;
            
        case 'admin_add_reward':
            $stmt = $pdo->prepare("INSERT INTO rewards (icon, name_zh, name_en, cost) VALUES (?, ?, ?, ?)");
            if ($stmt && $stmt->execute([trim($_POST['icon'] ?? ''), trim($_POST['name_zh'] ?? ''), trim($_POST['name_en'] ?? ''), intval($_POST['cost'] ?? 0)])) {
                echo json_encode_safe(['success' => true, 'message' => '新增兌換獎賞成功']);
            } else { echo json_encode_safe(['success' => false, 'message' => '資料庫寫入失敗']); }
            break;

        case 'admin_update_reward':
            $field = $_POST['field'] ?? ''; 
            if (in_array($field, ['icon', 'name_zh', 'name_en', 'cost'])) {
                $stmt = $pdo->prepare("UPDATE rewards SET $field = ? WHERE id = ?");
                if ($stmt) $stmt->execute([$_POST['value'] ?? '', intval($_POST['id'] ?? 0)]);
                echo json_encode_safe(['success' => true, 'message' => '獎賞更新成功']);
            } else { echo json_encode_safe(['success' => false, 'message' => '無效的欄位']); }
            break;

        case 'admin_delete_reward':
            $stmt = $pdo->prepare("DELETE FROM rewards WHERE id = ?");
            if ($stmt) $stmt->execute([intval($_POST['id'] ?? 0)]);
            echo json_encode_safe(['success' => true, 'message' => '獎賞刪除成功']);
            break;

        default:
            http_response_code(404); echo json_encode_safe(['success' => false, 'message' => 'Unknown API action']); break;
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode_safe([
        'success' => false, 
        'message' => '系統執行錯誤 (Server Error): ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine()
    ]);
}
?>