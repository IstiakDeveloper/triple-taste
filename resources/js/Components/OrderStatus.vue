<script setup>
import { computed } from 'vue';

const props = defineProps({
  status: {
    type: String,
    required: true
  }
});

const statusConfig = computed(() => {
  const configs = {
    pending: {
      color: 'gray',
      label: 'Pending'
    },
    confirmed: {
      color: 'blue',
      label: 'Confirmed'
    },
    preparing: {
      color: 'yellow',
      label: 'Preparing'
    },
    ready: {
      color: 'indigo',
      label: 'Ready'
    },
    delivering: {
      color: 'purple',
      label: 'Delivering'
    },
    delivered: {
      color: 'green',
      label: 'Delivered'
    },
    cancelled: {
      color: 'red',
      label: 'Cancelled'
    }
  };

  return configs[props.status] || configs.pending;
});

const badgeClasses = computed(() => {
  const colorClasses = {
    gray: 'bg-gray-100 text-gray-800',
    blue: 'bg-blue-100 text-blue-800',
    yellow: 'bg-yellow-100 text-yellow-800',
    indigo: 'bg-indigo-100 text-indigo-800',
    purple: 'bg-purple-100 text-purple-800',
    green: 'bg-green-100 text-green-800',
    red: 'bg-red-100 text-red-800'
  };

  return `inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${colorClasses[statusConfig.value.color]}`;
});
</script>

<template>
  <span :class="badgeClasses">
    {{ statusConfig.label }}
  </span>
</template>
