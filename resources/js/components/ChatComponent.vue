<template>
   
 <!-- User Header -->
<div class="whatsapp-green text-white py-4 px-6 flex items-center gap-3">
  <img 
      :src="filelink('default.jpeg')" 
      alt="Chat User Image" 
      class="rounded-full w-12 h-12">
  <div>
    <span class="text-lg font-semibold">{{ user.name }}</span>
  </div>
</div>

<!-- Messages -->
<div v-for="message in messages" :key="message.id" class="flex-1 p-4 overflow-y-auto">
  <div v-if="message.sender_id === auth.id" class="mb-4 text-right">
    <p class="inline-block px-4 py-2 rounded-lg whatsapp-dark text-white max-w-xs">
      {{ message.message }}
    </p>
  </div>

  <div v-else class="mb-4 text-left">
    <p class="inline-block px-4 py-2 rounded-lg whatsapp-light text-gray-800 max-w-xs">
      {{ message.message }}
    </p>
  </div>
</div>

<!-- Message Input -->
<div class="p-4 bg-white border-t flex gap-2">
  <input type="text" v-model="newMessage" @keyup.enter="sendMessage" placeholder="Type a message..." class="flex-1 px-4 py-2 border rounded-lg" required>
  <button @click="sendMessage" class="px-4 py-2 whatsapp-green text-white rounded-lg hover:bg-green-700">
    Send
  </button>
</div>

<!-- Placeholder if no chat is selected -->
<div v-if="!selectedUser" class="flex-1 flex items-center justify-center text-gray-600">
  <p>Select a user to start chatting</p>
</div>

</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
  user: {
    type: Object,
    required: true
  },
  auth: {
    type: Object,
    required: true
  }
});

const messages = ref([]);
const newMessage = ref("");

const filelink = (file) => `/assets/images/${file}`
const fetchMessages = () => {
  axios
    .get(`/api/messages/${props.user.id}`)
    .then((response) => {
      messages.value = response.data;
    })
    .catch((error) => {
      console.error("Error fetching messages:", error);
    });
};

// Send a new message
const sendMessage = () => {
  if (newMessage.value.trim() !== "") {
    axios
      .post('/api/messages/', {
        sender_id: props.auth.id,
        receiver_id: props.user.id,
        message: newMessage.value
      })
      .then((response) => {
        messages.value.push(response.data);
        newMessage.value = "";
      })
      .catch((error) => {
        console.error("Error sending message:", error);
      });
  }
};


const formatMessageTime = (timestamp) => {
  const date = new Date(timestamp);
  const options = {
    hour: 'numeric', minute: 'numeric', second: 'numeric',
    weekday: 'short', month: 'short', day: 'numeric',
  };
  return new Intl.DateTimeFormat('en-US', options).format(date);
};


watch(
  () => props.user.id, 
  fetchMessages
);


onMounted(() => {
  fetchMessages();
});

</script>

<style scoped>

</style>
