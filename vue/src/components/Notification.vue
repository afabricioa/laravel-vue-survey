<script setup>
    import { useUserStore } from '../store';
    import { computed } from 'vue';

    const store = useUserStore();

    const notification = computed(() => store.notification);

</script>

<template>
    <div
        v-if="notification.show"
        class="fixed w-[300px] border rounded-md left-4 top-8 py-2 px-4 text-white animate-fade-in-down flex justify-between"
        :class="[notification.type === 'success' ? 'bg-emerald-300' : 'bg-red-500']"
    >
        {{ notification.message }}
        <span>&times</span>
    </div>
</template>
