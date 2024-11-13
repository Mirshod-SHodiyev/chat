<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Chat App</title>
  <style>
    
    * { box-sizing: border-box; margin: 0; padding: 0; font-family: Arial, sans-serif; }
    body { display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f0f2f5; flex-direction: column; }
    .navbar { width: 100%; background-color: #007bff; padding: 10px 20px; color: #ffffff; display: flex; justify-content: space-between; align-items: center; }
    .navbar .brand { font-size: 1.5rem; font-weight: bold; }
    .navbar .nav-links { display: flex; gap: 15px; }
    .navbar .nav-links a { color: #ffffff; text-decoration: none; font-size: 1rem; padding: 5px 10px; border-radius: 4px; transition: background 0.3s; }
    .navbar .nav-links a:hover { background-color: #0056b3; }
    .chat-container { display: flex; width: 80%; height: 80vh; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); border-radius: 8px; overflow: hidden; }
    .user-list { width: 25%; background-color: #ffffff; border-right: 1px solid #ddd; overflow-y: auto; }
    .user { padding: 15px; cursor: pointer; border-bottom: 1px solid #f0f2f5; transition: background 0.3s; }
    .user:hover { background-color: #f9f9f9; }
    .user.active { background-color: #e6f7ff; }
    .user h4 { font-size: 1rem; font-weight: bold; color: #333; }
    .user p { font-size: 0.9rem; color: #888; }
    .chat-window { width: 75%; display: flex; flex-direction: column; background-color: #ffffff; }
    .chat-header { padding: 15px; background-color: #007bff; color: #ffffff; font-size: 1.1rem; font-weight: bold; text-align: center; }
    .chat-messages { flex: 1; padding: 20px; overflow-y: auto; }
    .message { margin-bottom: 15px; display: flex; flex-direction: column; }
    .message.sent { align-items: flex-end; }
    .message p { max-width: 60%; padding: 10px 15px; border-radius: 18px; font-size: 0.95rem; line-height: 1.4; }
    .message.sent p { background-color: #007bff; color: #ffffff; border-bottom-right-radius: 4px; }
    .message.received p { background-color: #f0f2f5; color: #333; border-bottom-left-radius: 4px; }
    .message-input { padding: 10px; display: flex; border-top: 1px solid #ddd; }
    .message-input input[type="text"] { flex: 1; padding: 10px; font-size: 1rem; border: 1px solid #ddd; border-radius: 4px; outline: none; }
    .message-input button { padding: 10px 15px; margin-left: 10px; font-size: 1rem; color: #ffffff; background-color: #007bff; border: none; border-radius: 4px; cursor: pointer; transition: background 0.3s; }
    .message-input button:hover { background-color: #0056b3; }
  </style>
</head>
<body>


<div class="navbar">
  <div class="brand">Chat App</div>
  <div class="nav-links">
    <a href="{{ route('login') }}">Login</a>
    <a href="{{ route('register') }}">Register</a>
  </div>
</div>

<div class="chat-container">
  
  <div class="user-list">
    @foreach ($users as $user)
      <a href="/chat/{{ $user->id }}" class="user-link" id="user-{{ $user->id }}">
        <div class="user {{ isset($selectedUser) && $selectedUser->id == $user->id ? 'active' : '' }}">
          <h4>{{ $user->name }}</h4>
          <p>{{ $user->last_message ?? 'No messages yet' }}</p>
        </div>
      </a>
    @endforeach
  </div>
  
  
  <div class="chat-window">
    <div class="chat-header" id="chat-header">
      Chat with {{ $selectedUser->name ?? 'a user' }}
    </div>
    <div class="chat-messages" id="chat-messages">
      @foreach ($messages as $message)
        <div class="message {{ $message->sender_id == auth()->id() ? 'sent' : 'received' }}">
          <p>{{ $message->content }}</p>
        </div>
      @endforeach
    </div>
    
  
    <form action="/message" method="post" id="message-form">
      @csrf
      <input type="hidden" name="user_id" value="{{ $selectedUser->id }}">
      <div class="message-input">
        <input type="text" id="message" name="message" placeholder="Type a message..." required>
        <button type="submit">Send</button>
      </div>
    </form>
  </div>
</div>

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

      setInterval(fetchMessages, 5000);

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


</body>
</html>
