<style>
/* CSS for CS AI Widget */
.cs-widget-container {
    position: fixed;
    bottom: 30px;
    right: 30px;
    z-index: 9999;
    font-family: 'Inter', sans-serif;
}

.cs-widget-button {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #0d6efd, #0dcaf0);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: transform 0.3s ease;
}

.cs-widget-button:hover {
    transform: scale(1.1);
}

.cs-widget-logo {
    color: white;
    font-size: 24px;
    animation: pulseLogo 2s infinite;
}

@keyframes pulseLogo {
    0% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.1); opacity: 0.8; }
    100% { transform: scale(1); opacity: 1; }
}

.cs-accuracy-badge {
    position: absolute;
    top: -10px;
    right: -10px;
    background: #198754;
    color: white;
    font-size: 10px;
    padding: 3px 6px;
    border-radius: 10px;
    font-weight: bold;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}

.cs-chat-window {
    display: none;
    position: absolute;
    bottom: 80px;
    right: 0;
    width: 350px;
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 25px rgba(0,0,0,0.15);
    overflow: hidden;
    flex-direction: column;
}

.cs-chat-header {
    background: linear-gradient(135deg, #0d6efd, #0dcaf0);
    color: white;
    padding: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.cs-chat-header h6 {
    margin: 0;
    font-weight: bold;
}

.cs-chat-body {
    height: 300px;
    overflow-y: auto;
    padding: 15px;
    background: #f8f9fa;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.cs-chat-message {
    max-width: 80%;
    padding: 10px 15px;
    border-radius: 15px;
    font-size: 14px;
    line-height: 1.4;
}

.cs-message-bot {
    background: #e9ecef;
    color: #333;
    align-self: flex-start;
    border-bottom-left-radius: 5px;
}

.cs-message-user {
    background: #0d6efd;
    color: white;
    align-self: flex-end;
    border-bottom-right-radius: 5px;
}

.cs-chat-input-area {
    padding: 15px;
    background: white;
    border-top: 1px solid #eee;
    display: flex;
    gap: 10px;
}

.cs-chat-input {
    flex: 1;
    border: 1px solid #dee2e6;
    border-radius: 20px;
    padding: 8px 15px;
    outline: none;
    font-size: 14px;
}

.cs-chat-send {
    background: #0d6efd;
    color: white;
    border: none;
    border-radius: 50%;
    width: 38px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background 0.2s;
}

.cs-chat-send:hover {
    background: #0b5ed7;
}

.cs-typing {
    font-size: 12px;
    color: #6c757d;
    font-style: italic;
    display: none;
    padding-left: 15px;
    margin-bottom: 5px;
}
</style>

<div class="cs-widget-container">
    <div class="cs-chat-window" id="csChatWindow">
        <div class="cs-chat-header">
            <div>
                <h6>Zavora AI Assistant</h6>
                <small style="font-size: 11px; opacity: 0.9;">Accuracy: 78.9% | Online</small>
            </div>
            <span style="cursor: pointer;" onclick="toggleCSChat()"><i class="fas fa-times"></i></span>
        </div>
        <div class="cs-chat-body" id="csChatBody">
            <div class="cs-chat-message cs-message-bot">
                Halo! Saya AI Assistant. Ada yang bisa saya bantu terkait halaman ini?
            </div>
        </div>
        <div class="cs-typing" id="csTypingIndicator">AI sedang mengetik...</div>
        <div class="cs-chat-input-area">
            <input type="text" id="csChatInput" class="cs-chat-input" placeholder="Tanyakan sesuatu..." onkeypress="handleCSInput(event)">
            <button class="cs-chat-send" onclick="sendCSMessage()">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>
    
    <div class="cs-widget-button" onclick="toggleCSChat()">
        <span class="cs-accuracy-badge">78.9%</span>
        <i class="fas fa-robot cs-widget-logo"></i>
    </div>
</div>

<script>
function toggleCSChat() {
    const chatWindow = document.getElementById('csChatWindow');
    chatWindow.style.display = chatWindow.style.display === 'flex' ? 'none' : 'flex';
}

function handleCSInput(e) {
    if (e.key === 'Enter') {
        sendCSMessage();
    }
}

async function sendCSMessage() {
    const inputField = document.getElementById('csChatInput');
    const message = inputField.value.trim();
    if (!message) return;

    // Add user message to chat
    appendCSMessage(message, 'user');
    inputField.value = '';

    // Show typing indicator
    document.getElementById('csTypingIndicator').style.display = 'block';

    // Get current page path
    const currentPath = window.location.pathname;

    try {
        const response = await fetch('/api/cs_chat.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                message: message, 
                context: currentPath 
            })
        });

        const data = await response.json();
        
        // Hide typing indicator
        document.getElementById('csTypingIndicator').style.display = 'none';

        if (data.success) {
            appendCSMessage(data.reply, 'bot');
        } else {
            appendCSMessage('Maaf, terjadi kesalahan pada sistem AI kami.', 'bot');
        }
    } catch (error) {
        document.getElementById('csTypingIndicator').style.display = 'none';
        appendCSMessage('Koneksi terputus. Coba lagi nanti.', 'bot');
    }
}

function appendCSMessage(text, sender) {
    const chatBody = document.getElementById('csChatBody');
    const msgDiv = document.createElement('div');
    msgDiv.className = `cs-chat-message cs-message-${sender}`;
    msgDiv.textContent = text;
    chatBody.appendChild(msgDiv);
    chatBody.scrollTop = chatBody.scrollHeight;
}
</script>
