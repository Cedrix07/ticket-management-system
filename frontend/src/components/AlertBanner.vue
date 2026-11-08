<template>
  <q-banner
    rounded
    :class="[bgClass, textClass, 'relative-position q-mb-md']"
  >
    <template v-slot:avatar>
      <q-icon :name="iconName" :color="type" />
    </template>

    <!-- Title & Content -->
    <div v-if="title" class="text-weight-bold">{{ title }}</div>
    <div class="text-caption">
      <slot>{{ message }}</slot>
    </div>

    <!-- Dismiss Button (Pinned Top-Right) -->
    <q-btn
      v-if="dismissible"
      flat
      round
      dense
      icon="close"
      size="sm"
      :class="['absolute-top-right q-ma-xs', textClass]"
      @click="$emit('dismiss')"
    />
  </q-banner>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  type: {
    type: String,
    default: 'negative', // 'negative' | 'positive' | 'warning' | 'info'
  },
  title: {
    type: String,
    default: '',
  },
  message: {
    type: String,
    default: '',
  },
  dismissible: {
    type: Boolean,
    default: true,
  },
});

defineEmits(['dismiss']);

// Map semantic types to Quasar pastel background & icon
const bgClass = computed(() => {
  const map = {
    negative: 'bg-red-1',
    positive: 'bg-green-1',
    warning: 'bg-amber-1',
    info: 'bg-blue-1',
  };
  return map[props.type] || 'bg-red-1';
});

const textClass = computed(() => {
  return `text-${props.type}`;
});

const iconName = computed(() => {
  const map = {
    negative: 'error_outline',
    positive: 'check_circle_outline',
    warning: 'warning_amber',
    info: 'info_outline',
  };
  return map[props.type] || 'error_outline';
});
</script>
