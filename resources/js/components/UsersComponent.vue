<template>
<h3 class="text-lg font-bold text-center p-4 border-b whatsapp-green text-white">Chats</h3>
<div v-for="user in users" :key="user.id">
  <a href="#" class="user-chat flex items-center gap-3 p-4 border-b hover:bg-gray-100" @click.prevent="selectUser(user)">
    <img 
        :src="filelink('default.jpeg')" 
        alt="User Image" 
        class="rounded-full w-12 h-12">
    <div>
      <h4 class="font-bold text-gray-800">{{ user.name }}</h4>
      <p class="text-sm text-gray-600">Hello</p>
      <p class="text-sm text-gray-600">No messages yet...</p>
    </div>
  </a>
</div>


</template>
  
  <script setup>
  import { onMounted, ref } from 'vue';
  
  const props = defineProps({
    auth: {
      type: Object,
      required: true
    },
  });
  
  const users = ref([]);
  const emit = defineEmits(['select-user']);
  const selectUser = (user) => {
    emit('select-user', user);
  }

  const filelink = (file) => `/assets/images/${file}`
    
  
  
  onMounted(() => {
    axios.get('/api/users')
      .then((response)=> {
        console.log(response.data);
        users.value = response.data.users;
      });
  });
  
  </script>
  
  <style scoped>
  
  </style>
  