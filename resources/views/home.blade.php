<script>
  
    $(function() {
        let lastMessageId = 0;
  
        $('#message-form').submit(function(e) {
            e.preventDefault();
  
            const message = $('#message').val().trim();
            const userId = '{{ $selectedUser->id }}';
  
            if (!message) return alert('Iltimos, xabar kiriting.');
  
            $.post('/message', {
                message,
                user_id: userId,
                _token: '{{ csrf_token() }}'
            }).done(response => {
                $('#message').val('');
                console.log(response);
                updateMessages(response.messages);
            }).fail(() => console.error('Xabar yuborishda xato'));
        });
  
        function updateMessages(messages) {
            const container = $('#chat-messages').empty();
            messages.forEach(message => {
                const isSent = message.sender_id == {{ auth()->id() }};
                container.append(`<div class="message ${isSent ? 'sent' : 'received'}">
                                    <p>${message.content}</p>
                                  </div>`);
                
                if (message.id > lastMessageId) {
                    lastMessageId = message.id;
                    showNotification(message.content);
                    playSound();
                }
            });
        }
  
        function fetchMessages() {
            const userId = '{{ $selectedUser->id }}';
            $.get(`/messages/${userId}`).done(response => updateMessages(response.messages)).fail(() => console.error('Xabarlar olishda xato'));
        }
  
        setInterval(function() {
        fetchMessages();
    }, 1000);;
  
        function showNotification(message) {
            if (Notification.permission === 'granted') {
                new Notification('Yangi xabar', { body: message });
            } else if (Notification.permission !== 'denied') {
                Notification.requestPermission().then(permission => {
                    if (permission === 'granted') {
                        new Notification('Yangi xabar', { body: message });
                    }
                });
            }
        }
  
        function playSound() {
            const sound = new Audio('{{ asset("sounds/notification.mp3") }}');
            sound.play();
        }
    });
  </script>