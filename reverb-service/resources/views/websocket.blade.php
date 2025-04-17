<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sports Data WebSocket Client</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .message {
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            background-color: #f3f4f6;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">Sports Data WebSocket Client</h1>
        
        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <h2 class="text-xl font-semibold mb-4">Connection Status</h2>
            <div class="flex items-center">
                <div id="status-indicator" class="w-4 h-4 rounded-full bg-red-500 mr-2"></div>
                <span id="connection-status">Disconnected</span>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-4">Received Messages</h2>
            <div id="messages" class="space-y-2 max-h-96 overflow-y-auto"></div>
        </div>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusIndicator = document.getElementById('status-indicator');
            const connectionStatus = document.getElementById('connection-status');
            const messagesContainer = document.getElementById('messages');

            // Initialize Echo
            window.Echo.channel('sports-data')
                .listen('.sports.data.updated', (data) => {
                    console.log('Received data:', data);
                    
                    // Create message element
                    const messageEl = document.createElement('div');
                    messageEl.className = 'message';
                    messageEl.innerHTML = `
                        <div class="font-semibold">Event: ${data.event}</div>
                        <div>Time: ${new Date().toLocaleTimeString()}</div>
                        <pre class="mt-2 text-sm bg-gray-100 p-2 rounded">${JSON.stringify(data, null, 2)}</pre>
                    `;
                    
                    // Add to container
                    messagesContainer.prepend(messageEl);
                });

            // Connection status handling
            window.Echo.connector.socket.on('connect', () => {
                statusIndicator.classList.remove('bg-red-500');
                statusIndicator.classList.add('bg-green-500');
                connectionStatus.textContent = 'Connected';
            });

            window.Echo.connector.socket.on('disconnect', () => {
                statusIndicator.classList.remove('bg-green-500');
                statusIndicator.classList.add('bg-red-500');
                connectionStatus.textContent = 'Disconnected';
            });
        });
    </script>
</body>
</html>
