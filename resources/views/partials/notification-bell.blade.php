<!-- Notification Bell Dropdown -->
<div class="relative font-sans inline-block text-left" id="notification-dropdown-wrapper">
    <!-- Bell Button -->
    <button id="notification-bell-btn" class="relative p-2 text-slate-500 hover:text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-full transition-all focus:outline-none">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
        </svg>
        <!-- Unread Badge Count -->
        <span id="notification-badge" class="hidden absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-[9px] font-bold leading-none text-white bg-rose-500 rounded-full transform translate-x-1 -translate-y-1">0</span>
    </button>

    <!-- Dropdown Box -->
    <div id="notification-dropdown-box" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-2xl overflow-hidden border border-slate-100 z-50 transform scale-95 opacity-0 origin-top-right transition-all duration-200">
        <!-- Header -->
        <div class="bg-indigo-600 px-4 py-3 text-white flex items-center justify-between">
            <span class="font-bold text-xs">Notifikasi Terbaru</span>
            <span id="notification-clear-btn" class="text-[10px] text-indigo-200 hover:text-white cursor-pointer transition-colors">Tandai dibaca</span>
        </div>

        <!-- Notification List -->
        <div id="notification-list" class="max-h-64 overflow-y-auto divide-y divide-slate-100 bg-slate-50 scrollbar-thin">
            <!-- Dynamic items go here -->
            <div class="p-4 text-center text-xs text-slate-400">Tidak ada notifikasi baru.</div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const bellBtn = document.getElementById('notification-bell-btn');
    const dropdownBox = document.getElementById('notification-dropdown-box');
    const badge = document.getElementById('notification-badge');
    const listContainer = document.getElementById('notification-list');

    let dropdownOpen = false;

    // Toggle dropdown visibility
    bellBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        dropdownOpen = !dropdownOpen;
        if (dropdownOpen) {
            dropdownBox.classList.remove('hidden');
            setTimeout(() => {
                dropdownBox.classList.remove('scale-95', 'opacity-0');
                dropdownBox.classList.add('scale-100', 'opacity-100');
            }, 10);
        } else {
            closeDropdown();
        }
    });

    document.addEventListener('click', function () {
        if (dropdownOpen) closeDropdown();
    });

    dropdownBox.addEventListener('click', function (e) {
        e.stopPropagation();
    });

    function closeDropdown() {
        dropdownOpen = false;
        dropdownBox.classList.remove('scale-100', 'opacity-100');
        dropdownBox.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            dropdownBox.classList.add('hidden');
        }, 200);
    }

    // AJAX Polling Function (runs every 10 seconds)
    async function pollNotifications() {
        try {
            const res = await fetch('/api/internal/notifications', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            if (!res.ok) return;
            const data = await res.json();

            // 1. Update Badge
            const count = data.unread_count;
            if (count > 0) {
                badge.textContent = count;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }

            // 2. Render List
            if (data.recent.length === 0) {
                listContainer.innerHTML = `<div class="p-4 text-center text-xs text-slate-400">Tidak ada notifikasi baru.</div>`;
            } else {
                listContainer.innerHTML = data.recent.map(item => `
                    <div class="p-3 hover:bg-slate-100 transition-colors flex gap-2 ${item.is_read ? 'opacity-70' : 'bg-white font-medium border-l-4 border-indigo-600'}">
                        <div class="flex-1">
                            <h4 class="text-xs text-slate-800 font-bold leading-tight">${item.title}</h4>
                            <p class="text-[10px] text-slate-500 mt-1">${item.message}</p>
                            <span class="text-[8px] text-slate-400 block mt-1">${item.created_at_human}</span>
                        </div>
                    </div>
                `).join('');
            }
        } catch (err) {
            console.error('Polling notifications failed:', err);
        }
    }

    // Initial load
    pollNotifications();

    // Trigger polling every 10 seconds (10000ms)
    setInterval(pollNotifications, 10000);
});
</script>
