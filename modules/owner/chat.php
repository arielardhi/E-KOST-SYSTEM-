<?php
require_once '../../config/database.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SESSION['role'] !== 'owner') {
    header("Location: ../../index.php");
    exit();
}

$user_id     = $_SESSION['user_id'];
$receiver_id = (int)($_GET['receiver_id'] ?? 0);

// Get contact list
$stmt = $pdo->prepare("
    SELECT u.id, u.full_name, u.role,
           (SELECT message FROM chat
            WHERE (sender_id = u.id AND receiver_id = :uid)
               OR (sender_id = :uid2 AND receiver_id = u.id)
            ORDER BY created_at DESC LIMIT 1) AS last_msg,
           (SELECT created_at FROM chat
            WHERE (sender_id = u.id AND receiver_id = :uid3)
               OR (sender_id = :uid4 AND receiver_id = u.id)
            ORDER BY created_at DESC LIMIT 1) AS last_time,
           (SELECT COUNT(*) FROM chat WHERE sender_id = u.id AND receiver_id = :uid5 AND is_read = 0) AS unread
    FROM users u
    JOIN chat c ON (u.id = c.sender_id OR u.id = c.receiver_id)
    WHERE (c.sender_id = :uid6 OR c.receiver_id = :uid7) AND u.id != :uid8
    GROUP BY u.id
    ORDER BY last_time DESC
");
$stmt->execute([
    ':uid'  => $user_id, ':uid2' => $user_id, ':uid3' => $user_id,
    ':uid4' => $user_id, ':uid5' => $user_id, ':uid6' => $user_id,
    ':uid7' => $user_id, ':uid8' => $user_id,
]);
$contacts = $stmt->fetchAll();

$receiver_info = null;
$messages      = [];
$last_id       = 0;

if ($receiver_id) {
    $stmt = $pdo->prepare("SELECT id, full_name, role, phone FROM users WHERE id = ?");
    $stmt->execute([$receiver_id]);
    $receiver_info = $stmt->fetch();

    $stmt = $pdo->prepare("
        SELECT * FROM chat
        WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)
        ORDER BY created_at ASC
    ");
    $stmt->execute([$user_id, $receiver_id, $receiver_id, $user_id]);
    $messages = $stmt->fetchAll();

    $pdo->prepare("UPDATE chat SET is_read = 1 WHERE sender_id = ? AND receiver_id = ? AND is_read = 0")
        ->execute([$receiver_id, $user_id]);

    $last_id = !empty($messages) ? end($messages)['id'] : 0;
}

include '../../layouts/header.php';
?>

<style>
.chat-wrapper { height: calc(100vh - 160px); min-height: 500px; }
.contact-list { height: 100%; overflow-y: auto; }
.chat-messages { height: calc(100% - 130px); overflow-y: auto; background: #f0f2f5; padding: 16px; }
.bubble { max-width: 72%; padding: 10px 14px; border-radius: 18px; margin-bottom: 8px; word-break: break-word; }
.bubble.me { background: #001ee1; color: #fff; border-bottom-right-radius: 4px; }
.bubble.them { background: #fff; color: #222; border-bottom-left-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,.1); }
.bubble small { font-size: 0.68rem; opacity: .7; display: block; margin-top: 4px; }
.contact-item { cursor: pointer; border-left: 3px solid transparent; transition: all .15s; }
.contact-item:hover { background: #f5f7ff; border-left-color: #001ee1; }
.contact-item.active { background: #eef1ff; border-left-color: #001ee1; }
.unread-badge { background: #001ee1; color: #fff; border-radius: 50%; width: 20px; height: 20px; font-size: 0.7rem; display: flex; align-items: center; justify-content: center; }
.chat-input-area { padding: 12px 16px; background: #fff; border-top: 1px solid #eee; }
#msg-input { border-radius: 24px; padding: 10px 18px; border: 2px solid #ddd; outline: none; }
#msg-input:focus { border-color: #001ee1; box-shadow: none; }
#send-btn { border-radius: 50%; width: 42px; height: 42px; background: #001ee1; border: none; color: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
#send-btn:hover { background: #0016b0; }
.wa-link { background: #25D366; color: #fff; border-radius: 8px; padding: 6px 14px; font-size: .85rem; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
.wa-link:hover { background: #1da851; color: #fff; }
</style>

<div class="container-fluid px-3 px-md-4">
    <div class="row g-0 chat-wrapper" style="border: 2px solid #ddd; border-radius: 12px; overflow: hidden; background:#fff;">

        <!-- SIDEBAR KONTAK -->
        <div class="col-md-4 col-lg-3 border-end">
            <div class="d-flex flex-column h-100">
                <div class="p-3 border-bottom" style="background:#001ee1;">
                    <h6 class="mb-0 text-white fw-bold"><i class="bi bi-chat-dots-fill me-2"></i>Pesan Masuk</h6>
                </div>
                <div class="contact-list">
                    <?php if (empty($contacts) && !$receiver_id): ?>
                        <div class="p-4 text-center text-muted mt-4">
                            <i class="bi bi-chat-square-text fs-2 mb-2 d-block"></i>
                            <small>Belum ada pesan masuk</small>
                        </div>
                    <?php endif; ?>

                    <?php
                    // Jika receiver baru, tampilkan di atas
                    $contact_ids = array_column($contacts, 'id');
                    if ($receiver_id && $receiver_info && !in_array($receiver_id, $contact_ids)):
                    ?>
                        <div class="contact-item active p-3 d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                 style="width:42px;height:42px;background:#eef1ff;">
                                <i class="bi bi-person-fill" style="color:#001ee1;"></i>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="fw-semibold text-truncate" style="font-size:.9rem;"><?= htmlspecialchars($receiver_info['full_name']) ?></div>
                                <small class="text-muted"><?= ucfirst($receiver_info['role']) ?></small>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php foreach ($contacts as $c): ?>
                        <a href="chat.php?receiver_id=<?= $c['id'] ?>"
                           class="contact-item <?= $receiver_id == $c['id'] ? 'active' : '' ?> p-3 d-flex align-items-center gap-3 text-decoration-none text-dark">
                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                 style="width:42px;height:42px;background:<?= $receiver_id == $c['id'] ? '#001ee1' : '#eef1ff' ?>;">
                                <i class="bi bi-person-fill" style="color:<?= $receiver_id == $c['id'] ? '#fff' : '#001ee1' ?>;"></i>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-semibold text-truncate" style="font-size:.9rem;"><?= htmlspecialchars($c['full_name']) ?></span>
                                    <?php if ($c['unread'] > 0): ?>
                                        <div class="unread-badge ms-1 flex-shrink-0"><?= $c['unread'] ?></div>
                                    <?php endif; ?>
                                </div>
                                <small class="text-muted text-truncate d-block" style="font-size:.78rem;">
                                    <?= $c['last_msg'] ? htmlspecialchars(substr($c['last_msg'], 0, 35)) . (strlen($c['last_msg']) > 35 ? '...' : '') : '' ?>
                                </small>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- AREA CHAT -->
        <div class="col-md-8 col-lg-9 d-flex flex-column">
            <?php if ($receiver_id && $receiver_info): ?>
                <div class="p-3 border-bottom d-flex align-items-center gap-3" style="background:#fff;">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:42px;height:42px;background:#eef1ff;flex-shrink:0;">
                        <i class="bi bi-person-fill" style="color:#001ee1;font-size:1.2rem;"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-bold" style="font-size:.95rem;"><?= htmlspecialchars($receiver_info['full_name']) ?></div>
                        <small class="text-muted"><?= ucfirst($receiver_info['role']) ?></small>
                    </div>
                    <?php if (!empty($receiver_info['phone'])): ?>
                        <?php
                        $phone = preg_replace('/\D/', '', $receiver_info['phone']);
                        if (str_starts_with($phone, '0')) $phone = '62' . substr($phone, 1);
                        elseif (!str_starts_with($phone, '62')) $phone = '62' . $phone;
                        ?>
                        <a href="https://wa.me/<?= $phone ?>" target="_blank" class="wa-link">
                            <i class="bi bi-whatsapp"></i> WhatsApp
                        </a>
                    <?php endif; ?>
                </div>

                <div class="chat-messages" id="chat-box">
                    <?php foreach ($messages as $m): ?>
                        <div class="d-flex mb-1 <?= $m['sender_id'] == $user_id ? 'justify-content-end' : 'justify-content-start' ?>"
                             data-msg-id="<?= $m['id'] ?>">
                            <div class="bubble <?= $m['sender_id'] == $user_id ? 'me' : 'them' ?>">
                                <?= nl2br(htmlspecialchars($m['message'])) ?>
                                <small><?= date('H:i', strtotime($m['created_at'])) ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="chat-input-area d-flex align-items-center gap-2">
                    <input type="text" id="msg-input" class="form-control flex-grow-1"
                           placeholder="Ketik pesan..." autocomplete="off">
                    <button id="send-btn" title="Kirim">
                        <i class="bi bi-send-fill" style="font-size:.9rem;"></i>
                    </button>
                </div>

                <script>
                const userId     = <?= $user_id ?>;
                const receiverId = <?= $receiver_id ?>;
                let lastId       = <?= $last_id ?>;
                const chatBox    = document.getElementById('chat-box');
                const msgInput   = document.getElementById('msg-input');
                const sendBtn    = document.getElementById('send-btn');
                const baseUrl    = window.BASE_URL || '/e-kost-system/';
                const apiBase    = baseUrl + 'modules/chat_api.php';

                function scrollBottom() { chatBox.scrollTop = chatBox.scrollHeight; }
                scrollBottom();

                function renderBubble(m) {
                    const isMe = m.sender_id == userId;
                    const time = new Date(m.created_at.replace(' ', 'T'));
                    const hhmm = time.toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'});
                    const div  = document.createElement('div');
                    div.className = `d-flex mb-1 ${isMe ? 'justify-content-end' : 'justify-content-start'}`;
                    div.dataset.msgId = m.id;
                    div.innerHTML = `<div class="bubble ${isMe ? 'me' : 'them'}">${m.message.replace(/\n/g,'<br>')}<small>${hhmm}</small></div>`;
                    chatBox.appendChild(div);
                    scrollBottom();
                }

                async function sendMessage() {
                    const text = msgInput.value.trim();
                    if (!text) return;
                    msgInput.value = '';
                    msgInput.focus();
                    const res  = await fetch(`${apiBase}?action=send`, {
                        method: 'POST',
                        headers: {'Content-Type':'application/json'},
                        body: JSON.stringify({receiver_id: receiverId, message: text})
                    });
                    const data = await res.json();
                    if (data.success) { renderBubble(data.message); lastId = data.message.id; }
                }

                sendBtn.addEventListener('click', sendMessage);
                msgInput.addEventListener('keydown', e => { if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); } });

                async function poll() {
                    try {
                        const res  = await fetch(`${apiBase}?action=poll&receiver_id=${receiverId}&last_id=${lastId}`);
                        const data = await res.json();
                        if (Array.isArray(data) && data.length > 0) {
                            data.forEach(m => {
                                if (!document.querySelector(`[data-msg-id="${m.id}"]`)) {
                                    renderBubble(m); lastId = Math.max(lastId, m.id);
                                }
                            });
                        }
                    } catch(e) {}
                    setTimeout(poll, 3000);
                }
                setTimeout(poll, 3000);
                </script>

            <?php else: ?>
                <div class="flex-grow-1 d-flex flex-column align-items-center justify-content-center text-muted">
                    <i class="bi bi-chat-dots" style="font-size:3rem;color:#001ee1;opacity:.3;"></i>
                    <p class="mt-3">Pilih kontak untuk membalas pesan</p>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php include '../../layouts/footer.php'; ?>
