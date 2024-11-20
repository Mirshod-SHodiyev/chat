<template>
  <!-- Main Content -->
  <div class="flex-1 flex overflow-hidden">
   
    <div class="w-1/4 bg-white border-r overflow-y-auto">
      <UsersComponent :auth="props.auth" @select-user="openChat" />  
    </div>

    <div class="w-3/4 flex flex-col">
     <ChatComponent :user="selectedUser" :auth="auth" v-if="selectedUser"/>
   </div>
 </div>

  
</template>

<script setup>
import { ref } from 'vue';
import UsersComponent from './components/UsersComponent.vue';  // Users component
import ChatComponent from './components/ChatComponent.vue'; // Chat component

// Receive auth prop
const props = defineProps({
  auth: {
    type: Object,
    required: true
  }
});

const selectedUser = ref(null);
const openChat = (user) => {
  selectedUser.value = user;
};
</script>

<style scoped>
/* Add your custom styles here */
</style>
