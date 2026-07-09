<script src="https://cdn.tailwindcss.com"></script>
<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <form action="<?php echo e(route('login')); ?>" method="POST" class="bg-white p-8 rounded shadow-md w-96">
        <?php echo csrf_field(); ?>
        <h2 class="text-2xl font-bold mb-6">Login</h2>
        <input type="email" name="email" placeholder="Email" class="w-full p-2 mb-4 border rounded" required>
        <input type="password" name="password" placeholder="Password" class="w-full p-2 mb-4 border rounded" required>
        <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded">Login</button>
    </form>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pusher/8.3.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>

<script>
    window.Pusher = Pusher;
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: '<?php echo e(env("VITE_REVERB_APP_KEY")); ?>',
        wsHost: '127.0.0.1', 
        wsPort: 8080,
        forceTLS: false,
        enabledTransports: ['ws', 'wss'],
    });
    window.Echo.connector.pusher.connection.bind('connected', () => {
        console.log('CONNECTED TO REVERB!');
    });

    window.Echo.channel('team-notifications')
        .listen('.App\\Events\\ReportImported', (e) => {
         //console.log('Event Data:', e);

            const encodedData = btoa(JSON.stringify(e.reports));

            const url = `/native-notify?data=${encodedData}`;


            const windowId = 'import_alert_' + Date.now();

            if (window.NativePHP) {
                window.NativePHP.openWindow(windowId, {
                    url: url,
                    width: 1000,
                    height: 800,
                    alwaysOnTop: true,
                    resizable: true
                });
            } else {
               
                const win = window.open(url, windowId, 'width=1000,height=900');
                if (win) win.focus();
            } 
        });
</script><?php /**PATH /var/www/html/desktopnotification/resources/views/auth/login.blade.php ENDPATH**/ ?>