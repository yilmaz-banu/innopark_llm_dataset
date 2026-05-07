<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InnoPark AI Assistant</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #09090b;
            color: #d4d4d8;
        }
        
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #27272a; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #3f3f46; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }

        .typing-cursor {
            display: inline-block;
            width: 6px;
            height: 1.2em;
            background-color: #10b981;
            vertical-align: text-bottom;
            animation: blink 1s step-end infinite;
            margin-left: 2px;
            box-shadow: 0 0 8px rgba(16, 185, 129, 0.8);
            border-radius: 2px;
        }
        @keyframes blink { 50% { opacity: 0; } }

        /* Modern Markdown Typography */
        .ai-message p, .user-message p { margin-bottom: 1rem; line-height: 1.7; font-weight: 300; }
        .ai-message p:last-child, .user-message p:last-child { margin-bottom: 0; }
        
        .ai-message h1, .ai-message h2, .ai-message h3 {
            margin-top: 1.5rem; margin-bottom: 0.75rem; font-weight: 600; color: #f4f4f5;
        }
        .ai-message h1 { font-size: 1.5rem; border-bottom: 1px solid #27272a; padding-bottom: 0.5rem; }
        .ai-message h2 { font-size: 1.25rem; }
        .ai-message h3 { font-size: 1.1rem; }
        .ai-message ul { list-style-type: disc; margin-left: 1.5rem; margin-bottom: 1rem; }
        .ai-message ol { list-style-type: decimal; margin-left: 1.5rem; margin-bottom: 1rem; }
        .ai-message li { margin-bottom: 0.25rem; line-height: 1.6; }
        .ai-message strong { color: #fafafa; font-weight: 500; }
        .ai-message pre { background: #18181b; border: 1px solid #27272a; padding: 1rem; border-radius: 0.75rem; overflow-x: auto; margin: 1rem 0; }
        .ai-message code { font-family: monospace; background: #27272a; padding: 0.1rem 0.3rem; border-radius: 0.25rem; font-size: 0.85em; color: #e4e4e7; }
        .ai-message pre code { background: transparent; padding: 0; font-size: 0.85em; }

        .glass-panel {
            background: rgba(24, 24, 27, 0.6);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        @keyframes slideUpFade {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .msg-enter { animation: slideUpFade 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; }

        textarea { resize: none; scrollbar-width: none; }
        textarea:focus { outline: none; }
    </style>
</head>
<body class="overflow-hidden text-sm">

    <!-- Login Overlay -->
    <div id="loginOverlay" class="fixed inset-0 z-50 flex items-center justify-center bg-[#09090b] transition-opacity duration-700">
        <div class="p-10 rounded-3xl max-w-sm w-full text-center transform transition-transform duration-700 scale-100">
            <div class="w-20 h-20 bg-emerald-500/10 rounded-3xl flex items-center justify-center mx-auto mb-6 border border-emerald-500/20 relative">
                <div class="absolute inset-0 bg-emerald-500/10 rounded-3xl blur-xl animate-pulse"></div>
                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="relative z-10"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>
            </div>
            <h2 class="text-2xl font-semibold text-zinc-100 mb-2">InnoPark AI</h2>
            <p class="text-zinc-500 text-sm mb-10 font-light tracking-wide">Gelişmiş kurumsal yapay zeka asistanı.</p>
            <button onclick="performLogin()" class="w-full bg-zinc-100 hover:bg-white text-zinc-900 py-3.5 px-4 rounded-xl font-medium transition-all shadow-[0_0_15px_rgba(255,255,255,0.1)] active:scale-95 flex items-center justify-center gap-2">
                Sisteme Giriş Yap
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </button>
        </div>
    </div>

    <!-- Confirm Modal -->
    <div id="confirmModal" class="fixed inset-0 z-50 flex items-center justify-center bg-[#09090b]/80 backdrop-blur-sm hidden transition-opacity duration-300 opacity-0">
        <div class="bg-[#18181b] p-8 rounded-3xl max-w-sm w-full text-center border border-white/5 shadow-2xl transform transition-transform duration-300 scale-95" id="confirmModalContent">
            <h3 id="modalTitle" class="text-lg font-semibold text-zinc-100 mb-2"></h3>
            <p id="modalMessage" class="text-zinc-400 text-sm mb-8 font-light"></p>
            <div class="flex gap-3">
                <button onclick="closeModal()" class="flex-1 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 py-3 rounded-xl font-medium transition-colors">İptal</button>
                <button id="modalConfirmBtn" class="flex-1 py-3 rounded-xl font-medium transition-all">Onayla</button>
            </div>
        </div>
    </div>

    <!-- Main App Structure -->
    <div class="flex h-screen w-full relative">
        
        <!-- Sidebar -->
        <div class="w-64 bg-[#09090b] border-r border-white/5 hidden md:flex flex-col p-4 z-20">
            <div class="flex items-center gap-3 mb-8 px-2 mt-2">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center border border-white/10 bg-zinc-900 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>
                </div>
                <h1 class="text-[15px] font-medium text-zinc-200">InnoPark AI</h1>
            </div>
            
            <button onclick="newChat()" class="w-full py-2.5 px-3 rounded-xl hover:bg-zinc-900 text-zinc-300 transition-colors flex items-center justify-between mb-6 group border border-transparent hover:border-white/5">
                <span class="flex items-center gap-2 text-sm font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    Yeni Sohbet
                </span>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-0 group-hover:opacity-100 transition-opacity"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
            </button>

            <div class="flex-1 overflow-y-auto space-y-8 scrollbar-hide pb-4">
                <!-- Geçmiş Sohbetler -->
                <div>
                    <div class="text-[10px] font-medium text-zinc-500 uppercase tracking-widest mb-3 px-3">Geçmiş</div>
                    <div id="chatHistoryList" class="space-y-0.5">
                        <!-- JS ile otomatik dolacak -->
                    </div>
                </div>

                <!-- Hızlı Sorgular -->
                <div>
                    <div class="text-[10px] font-medium text-zinc-500 uppercase tracking-widest mb-3 px-3">Kısayollar</div>
                    <div class="space-y-0.5">
                        <button onclick="askQuestion('InnoPark ekibinde kimler var?')" class="w-full text-left px-3 py-2.5 rounded-lg hover:bg-zinc-800/80 transition-all border border-transparent hover:border-white/5 text-zinc-400 hover:text-zinc-200 text-sm">👥 Ekip Üyeleri</button>
                        <button onclick="askQuestion('InnoPark\'taki firmaların listesini ver.')" class="w-full text-left px-3 py-2.5 rounded-lg hover:bg-zinc-800/80 transition-all border border-transparent hover:border-white/5 text-zinc-400 hover:text-zinc-200 text-sm">🏢 Firmalar</button>
                        <button onclick="askQuestion('Girişimciler için ne gibi destekler var?')" class="w-full text-left px-3 py-2.5 rounded-lg hover:bg-zinc-800/80 transition-all border border-transparent hover:border-white/5 text-zinc-400 hover:text-zinc-200 text-sm">🚀 Girişimci Desteği</button>
                        <button onclick="askQuestion('Çevre ve Kalite politikaları nedir?')" class="w-full text-left px-3 py-2.5 rounded-lg hover:bg-zinc-800/80 transition-all border border-transparent hover:border-white/5 text-zinc-400 hover:text-zinc-200 text-sm">📜 Standartlar</button>
                    </div>
                </div>
            </div>

            <!-- Alt Butonlar -->
            <div class="pt-4 mt-auto border-t border-white/5 space-y-1">
                <button onclick="confirmClearHistory()" class="w-full px-3 py-2.5 rounded-lg hover:bg-zinc-900 text-zinc-500 hover:text-zinc-300 transition-colors flex items-center gap-3 text-[13px]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                    Geçmişi Temizle
                </button>
                <button onclick="confirmLogout()" class="w-full px-3 py-2.5 rounded-lg hover:bg-red-500/10 text-zinc-500 hover:text-red-400 transition-colors flex items-center gap-3 text-[13px]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                    Çıkış Yap
                </button>
            </div>
        </div>

        <!-- Main Chat Area -->
        <div class="flex-1 flex flex-col relative bg-[#09090b]">
            
            <!-- Minimalist Status Indicator -->
            <div class="absolute top-5 right-6 z-10 hidden md:block">
                <div class="flex items-center gap-2 px-3 py-1.5 text-zinc-500 text-xs font-medium">
                    <span class="relative flex h-2 w-2">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-40"></span>
                      <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    Sistem Aktif
                </div>
            </div>

            <!-- Messages -->
            <div id="chatWindow" class="flex-1 overflow-y-auto px-4 md:px-32 lg:px-48 xl:px-64 pt-16 pb-48">
                <!-- Messages will be injected here -->
            </div>

            <!-- Gradient fading at bottom for floating input -->
            <div class="absolute bottom-0 left-0 w-full h-40 bg-gradient-to-t from-[#09090b] via-[#09090b]/80 to-transparent z-10 pointer-events-none"></div>

            <!-- Floating Input Area -->
            <div class="absolute bottom-8 left-0 w-full px-4 md:px-32 lg:px-48 xl:px-64 z-20 pointer-events-none">
                <div class="max-w-4xl mx-auto pointer-events-auto">
                    <div class="relative bg-[#18181b] border border-white/10 rounded-2xl flex flex-col shadow-2xl transition-all focus-within:border-zinc-500/50 focus-within:bg-[#1f1f22]">
                        <textarea id="userInput" rows="1" placeholder="InnoPark hakkında bir soru sorun..." 
                            class="w-full bg-transparent border-none py-4 pl-5 pr-14 text-zinc-200 placeholder-zinc-500 focus:outline-none focus:ring-0 max-h-40 overflow-y-auto text-[15px]"></textarea>
                        
                        <button id="sendBtn" onclick="handleSend()" 
                            class="absolute right-2 bottom-2 w-10 h-10 bg-zinc-100 hover:bg-white text-zinc-900 rounded-xl flex items-center justify-center transition-transform active:scale-90 disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                        </button>
                    </div>
                    <div class="text-center mt-3 text-[11px] text-zinc-500 font-medium tracking-wide">
                        InnoPark AI hata yapabilir. Lütfen önemli bilgileri doğrulayın.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const chatWindow = document.getElementById('chatWindow');
        const userInput = document.getElementById('userInput');
        const sendBtn = document.getElementById('sendBtn');
        const chatHistoryList = document.getElementById('chatHistoryList');

        let chats = JSON.parse(localStorage.getItem('innopark_chats')) || [];
        let currentChatId = null;

        window.onload = () => {
            checkLogin();
            renderHistory();
            newChat();
        };

        function checkLogin() {
            const loggedIn = localStorage.getItem('innopark_logged_in');
            const overlay = document.getElementById('loginOverlay');
            if (loggedIn === 'true') {
                overlay.classList.add('hidden');
            } else {
                overlay.classList.remove('hidden');
            }
        }

        function performLogin() {
            localStorage.setItem('innopark_logged_in', 'true');
            const overlay = document.getElementById('loginOverlay');
            overlay.classList.add('opacity-0');
            setTimeout(() => {
                overlay.classList.add('hidden');
                overlay.classList.remove('opacity-0');
            }, 700);
        }

        let pendingModalAction = null;

        function showModal(title, message, type, action) {
            const modal = document.getElementById('confirmModal');
            const content = document.getElementById('confirmModalContent');
            const titleEl = document.getElementById('modalTitle');
            const msgEl = document.getElementById('modalMessage');
            const confirmBtn = document.getElementById('modalConfirmBtn');

            titleEl.textContent = title;
            msgEl.textContent = message;
            pendingModalAction = action;

            if (type === 'danger') {
                confirmBtn.className = 'flex-1 bg-red-500/10 hover:bg-red-500/20 text-red-500 py-3 rounded-xl font-medium transition-colors';
                confirmBtn.textContent = 'Sil';
            } else {
                confirmBtn.className = 'flex-1 bg-zinc-100 hover:bg-white text-zinc-900 py-3 rounded-xl font-medium transition-colors';
                confirmBtn.textContent = 'Onayla';
            }

            confirmBtn.onclick = () => {
                if (pendingModalAction) pendingModalAction();
                closeModal();
            };

            modal.classList.remove('hidden');
            void modal.offsetWidth; 
            modal.classList.remove('opacity-0');
            content.classList.remove('scale-95');
        }

        function closeModal() {
            const modal = document.getElementById('confirmModal');
            const content = document.getElementById('confirmModalContent');
            modal.classList.add('opacity-0');
            content.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
            pendingModalAction = null;
        }

        function confirmClearHistory() {
            showModal(
                "Geçmişi Sil", 
                "Tüm sohbet geçmişiniz kalıcı olarak silinecektir. Bu işlem geri alınamaz.", 
                "danger", 
                () => {
                    localStorage.removeItem('innopark_chats');
                    chats = [];
                    newChat();
                }
            );
        }

        function confirmLogout() {
            showModal(
                "Çıkış Yap", 
                "Asistandan çıkış yapmak üzeresiniz. Tekrar girmek için kilit ekranına yönlendirileceksiniz.", 
                "info", 
                () => {
                    localStorage.setItem('innopark_logged_in', 'false');
                    window.location.reload();
                }
            );
        }

        function renderHistory() {
            chatHistoryList.innerHTML = '';
            const sortedChats = [...chats].sort((a, b) => b.id - a.id);
            sortedChats.forEach(chat => {
                const btn = document.createElement('button');
                btn.className = `w-full text-left px-3 py-2.5 rounded-lg transition-colors text-[13px] truncate flex items-center gap-3 ${chat.id === currentChatId ? 'bg-zinc-800 text-zinc-200 font-medium' : 'hover:bg-zinc-900 text-zinc-400'}`;
                btn.innerHTML = `<span>${chat.title}</span>`;
                btn.onclick = () => loadChat(chat.id);
                chatHistoryList.appendChild(btn);
            });
        }

        function saveChats() {
            localStorage.setItem('innopark_chats', JSON.stringify(chats));
            renderHistory();
        }

        function loadChat(id) {
            currentChatId = id;
            const chat = chats.find(c => c.id === id);
            if (!chat) return;

            chatWindow.innerHTML = '';
            chat.messages.forEach(msg => {
                appendMessage(msg.role, msg.text, false);
            });
            renderHistory();
            scrollToBottom();
        }

        function newChat() {
            currentChatId = null;
            chatWindow.innerHTML = `
                <div class="flex flex-col items-center justify-center min-h-[60vh] text-center msg-enter">
                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-6 border border-white/5 bg-zinc-900 shadow-[0_0_30px_rgba(16,185,129,0.1)] relative group cursor-default">
                         <div class="absolute inset-0 bg-emerald-500/20 rounded-2xl blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
                         <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="relative z-10 transition-transform duration-500 group-hover:scale-110"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>
                    </div>
                    <h2 class="text-2xl font-semibold text-zinc-100 mb-2 tracking-tight">Size nasıl yardımcı olabilirim?</h2>
                </div>
            `;
            renderHistory();
        }

        // Auto-resize textarea
        userInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });

        userInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                handleSend();
            }
        });

        async function handleSend() {
            const query = userInput.value.trim();
            if (!query) return;

            userInput.value = '';
            userInput.style.height = 'auto';
            
            // Remove welcome screen
            if (chatWindow.querySelector('.min-h-\\[60vh\\]')) {
                chatWindow.innerHTML = '';
            }

            appendMessage('user', query, false);

            if (!currentChatId) {
                currentChatId = Date.now();
                chats.push({
                    id: currentChatId,
                    title: query.substring(0, 25) + (query.length > 25 ? '...' : ''),
                    messages: []
                });
            }
            
            const currentChat = chats.find(c => c.id === currentChatId);
            currentChat.messages.push({ role: 'user', text: query });
            saveChats();

            const loadingDiv = document.createElement('div');
            loadingDiv.className = 'flex items-start gap-4 mb-8 msg-enter w-full max-w-3xl mx-auto';
            loadingDiv.innerHTML = `
                <div class="w-8 h-8 rounded-md flex-shrink-0 flex items-center justify-center border border-white/5 bg-zinc-900 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>
                </div>
                <div class="flex gap-1.5 items-center h-8">
                    <div class="w-1.5 h-1.5 bg-zinc-500 rounded-full animate-bounce"></div>
                    <div class="w-1.5 h-1.5 bg-zinc-500 rounded-full animate-bounce [animation-delay:-0.2s]"></div>
                    <div class="w-1.5 h-1.5 bg-zinc-500 rounded-full animate-bounce [animation-delay:-0.4s]"></div>
                </div>
            `;
            chatWindow.appendChild(loadingDiv);
            scrollToBottom();

            try {
                const response = await fetch('api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ query: query })
                });
                
                const data = await response.json();
                chatWindow.removeChild(loadingDiv);

                if (data.error) {
                    if (data.auth_required) {
                        appendAuthCard();
                    } else {
                        appendMessage('error', `Bilinmeyen bir hata oluştu: ${data.error}`, false);
                    }
                } else {
                    appendMessage('ai', data.response, true);
                    currentChat.messages.push({ role: 'ai', text: data.response });
                    saveChats();
                }
            } catch (error) {
                if (chatWindow.contains(loadingDiv)) chatWindow.removeChild(loadingDiv);
                appendMessage('error', `Sunucuya ulaşılamadı. Lütfen PHP servisinin çalıştığından emin olun.`, false);
            }
        }

        function appendAuthCard() {
            const msgDiv = document.createElement('div');
            msgDiv.className = `flex justify-center msg-enter mb-8 w-full`;
            
            msgDiv.innerHTML = `
                <div class="bg-zinc-900 border border-red-500/30 p-6 rounded-2xl text-center w-full max-w-lg shadow-xl">
                    <div class="flex justify-center mb-4">
                        <div class="w-10 h-10 bg-red-500/10 rounded-full flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-zinc-100 mb-2">Kimlik Doğrulama Gerekli</h3>
                    <p class="text-zinc-400 mb-6 text-[13px] font-light">NotebookLM bağlantısı güvenlik nedeniyle kapatıldı.</p>
                    <div class="bg-zinc-950 p-4 rounded-xl text-left border border-white/5 mb-5">
                        <ol class="text-zinc-400 text-xs space-y-2.5 list-decimal list-inside font-light">
                            <li>Komut satırı (CMD / Terminal) açın.</li>
                            <li><code class="bg-zinc-800 text-zinc-200 px-1.5 py-0.5 rounded font-medium">nlm login</code> yazıp Enter'a basın.</li>
                            <li>Açılan Chrome penceresinde onay verin.</li>
                            <li>İşlem bittikten sonra sayfayı yenileyin.</li>
                        </ol>
                    </div>
                    <button onclick="window.location.reload()" class="w-full bg-zinc-100 hover:bg-white text-zinc-900 py-3 rounded-xl font-medium transition-colors flex justify-center items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                        Giriş Yaptım, Sayfayı Yenile
                    </button>
                </div>
            `;
            chatWindow.appendChild(msgDiv);
            scrollToBottom();
        }

        function appendMessage(role, text, animate = true) {
            const msgDiv = document.createElement('div');
            msgDiv.className = `flex ${role === 'user' ? 'justify-end' : 'justify-start'} mb-8 msg-enter w-full max-w-3xl mx-auto`;
            
            if (role === 'ai') {
                msgDiv.innerHTML = `
                    <div class="w-8 h-8 rounded-md flex-shrink-0 flex items-center justify-center border border-white/5 bg-zinc-900 shadow-sm mr-4 mt-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>
                    </div>
                    <div class="ai-message text-zinc-300 w-full overflow-hidden text-[15px] leading-relaxed"></div>
                `;
                const textContainer = msgDiv.querySelector('.ai-message');
                chatWindow.appendChild(msgDiv);
                
                if (animate) {
                    let i = 0;
                    let currentText = "";
                    const charsPerTick = 12; 
                    
                    const typingInterval = setInterval(() => {
                        currentText += text.substring(i, i + charsPerTick);
                        textContainer.innerHTML = marked.parse(currentText) + '<span class="typing-cursor"></span>';
                        scrollToBottom();
                        i += charsPerTick;
                        
                        if (i >= text.length) {
                            clearInterval(typingInterval);
                            textContainer.innerHTML = marked.parse(text); 
                            scrollToBottom();
                        }
                    }, 5); 
                } else {
                    textContainer.innerHTML = marked.parse(text); 
                }
            } else if (role === 'error') {
                msgDiv.className = `flex justify-center mb-8 msg-enter w-full max-w-3xl mx-auto`;
                msgDiv.innerHTML = `
                    <div class="bg-red-500/10 text-red-400 border border-red-500/20 py-3 px-5 rounded-xl max-w-lg text-[14px]">
                        ${text}
                    </div>
                `;
                chatWindow.appendChild(msgDiv);
            } else {
                msgDiv.innerHTML = `
                    <div class="user-message bg-[#27272a] text-zinc-100 py-3.5 px-5 rounded-3xl rounded-tr-sm max-w-[85%] text-[15px] shadow-sm">
                        ${text}
                    </div>
                `;
                chatWindow.appendChild(msgDiv);
            }
            scrollToBottom();
        }

        function scrollToBottom() {
            chatWindow.scrollTop = chatWindow.scrollHeight;
        }

        function askQuestion(q) {
            userInput.value = q;
            userInput.style.height = 'auto';
            handleSend();
        }
    </script>
</body>
</html>
