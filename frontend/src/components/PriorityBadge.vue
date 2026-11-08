<template>
  <q-badge
    rounded
    :class="[styleConfig.bg, styleConfig.text, 'text-weight-bold q-px-sm q-py-xs text-uppercase']"
    style="font-size: 11px; letter-spacing: 0.04em; border-radius: 4px;"
  >
    {{ styleConfig.label }}
  </q-badge>
</template>
<script setup>
import { computed } from 'vue'

const props = defineProps({
  priority: {
    type: String,
    required: true,
  }
})

const priorityMap = {
  low: { bg: 'bg-grey-2', text: 'text-secondary', label: 'LOW' },
  medium: { bg: 'bg-blue-1', text: 'text-primary', label: 'MEDIUM' },
  high: { bg: 'bg-amber-1', text: 'text-warning', label: 'HIGH' },
  urgent: { bg: 'bg-red-1', text: 'text-negative', label: 'URGENT' },
};

const styleConfig = computed(() => {
  return priorityMap[props.priority] || { bg: 'bg-grey-2', text: 'text-secondary', label: props.priority || 'UNKNOWN' };
});
</script>
