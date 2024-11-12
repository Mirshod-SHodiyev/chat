<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Chat App</title>
  <style>
    /* Global styles */
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
    }

    body {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      background-color: #f0f2f5;
      flex-direction: column;
    }

    /* Navbar */
    .navbar {
      width: 100%;
      background-color: #007bff;
      padding: 10px 20px;
      color: #ffffff;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .navbar .brand {
      font-size: 1.5rem;
      font-weight: bold;
    }

    .navbar .nav-links {
      display: flex;
      gap: 15px;
    }

    .navbar .nav-links a {
      color: #ffffff;
      text-decoration: none;
      font-size: 1rem;
      padding: 5px 10px;
      border-radius: 4px;
      transition: background 0.3s;
    }

    .navbar .nav-links a:hover {
      background-color: #0056b3;
    }

    /* Chat container */
    .chat-container {
      display: flex;
      width: 80%;
      height: 80vh;
      box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
      border-radius: 8px;
      overflow: hidden;
    }

    /* Users list */
    .user-list {
      width: 25%;
      background-color: #ffffff;
      border-right: 1px solid #ddd;
      overflow-y: auto;
    }

    .user {
      padding: 15px;
      cursor: pointer;
      border-bottom: 1px solid #f0f2f5;
      transition: background 0.3s;
    }

    .user:hover {
      background-color: #f9f9f9;
    }

    .user.active {
      background-color: #e6f7ff;
    }

    .user h4 {
      font-size: 1rem;
      font-weight: bold;
      color: #333;
    }

    .user p {
      font-size: 0.9rem;
      color: #888;
    }

    /* Chat window */
    .chat-window {
      width: 75%;
      display: flex;
      flex-direction: column;
      background-color: #ffffff;
    }

    .chat-header {
      padding: 15px;
      background-color: #007bff;
      color: #ffffff;
      font-size: 1.1rem;
      font-weight: bold;
      text-align: center;
    }

    .chat-messages {
      flex: 1;
      padding: 20px;
      overflow-y: auto;
    }

    .message {
      margin-bottom: 15px;
      display: flex;
      flex-direction: column;
    }

    .message.sent {
      align-items: flex-end;
    }

    .message p {
      max-width: 60%;
      padding: 10px 15px;
      border-radius: 18px;
      font-size: 0.95rem;
      line-height: 1.4;
    }

    .message.sent p {
      background-color: #007bff;
      color: #ffffff;
      border-bottom-right-radius: 4px;
    }

    .message.received p {
      background-color: #f0f2f5;
      color: #333;
      border-bottom-left-radius: 4px;
    }

    /* Message input */
    .message-input {
      padding: 10px;
      display: flex;
      border-top: 1px solid #ddd;
    }

    .message-input input[type="text"] {
      flex: 1;
      padding: 10px;
      font-size: 1rem;
      border: 1px solid #ddd;
      border-radius: 4px;
      outline: none;
    }

    .message-input button {
      padding: 10px 15px;
      margin-left: 10px;
      font-size: 1rem;
      color: #ffffff;
      background-color: #007bff;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      transition: background 0.3s;
    }

    .message-input button:hover {
      background-color: #0056b3;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
  <div class="brand">Chat App</div>
  <div class="nav-links">
    <a href="/login">Login</a>
    <a href="/register">Register</a>
  </div>
</div>

<div class="chat-container">
    <div class="user-list">
        @foreach ($users as $user)
          <div class="user" onclick="selectUser({{ $user->id }}, '{{ $user->name }}')">
            <h4>{{ $user->name }}</h4>
            <p>{{ $user->last_message ?? 'No messages yet' }}</p>
          </div>
        @endforeach
      </div>
      
  <!-- Chat window -->
  <div class="chat-window">
    <div class="chat-header" id="chat-header">Chat with a user</div>
    <div class="chat-messages" id="chat-messages">
      <!-- Sample messages -->
      <div class="message received">
        <p>Hello! How are you?</p>
      </div>
      <div class="message sent">
        <p>I'm good, thank you! And you?</p>
      </div>
      <!-- Add more messages here -->
    </div>
    <div class="message-input">
      <input type="text" id="message" placeholder="Type a message...">
      <button onclick="sendMessage()">Send</button>
    </div>
  </div>
</div>

<script>
  let receiverId = null;

  function selectUser(userId, userName) {
    receiverId = userId;
    document.getElementById("chat-header").textContent = `Chat with ${userName}`;
    // Clear the message box when switching users
    document.getElementById("chat-messages").innerHTML = '';
  }

  function sendMessage() {
    const messageInput = document.getElementById("message");
    const messageText = messageInput.value;

    if (messageText.trim() !== "" && receiverId !== null) {
      const chatMessages = document.getElementById("chat-messages");

      const newMessage = document.createElement("div");
      newMessage.classList.add("message", "sent");
      const messageContent = document.createElement("p");
      messageContent.textContent = messageText;
      newMessage.appendChild(messageContent);
      chatMessages.appendChild(newMessage);

      messageInput.value = "";

      chatMessages.scrollTop = chatMessages.scrollHeight;

      // Send the message to the server
      fetch('/send-message', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}', // Laravel CSRF token
        },
        body: JSON.stringify({
          receiver_id: receiverId,
          content: messageText,
        }),
      })
      .then(response => response.json())
      .then(data => {
        console.log('Message sent:', data);
      })
      .catch(error => {
        console.error('Error sending message:', error);
      });
    }
  }
</script>

</body>
</html>
