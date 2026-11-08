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
  status: {
    type: String,
    required: true,
  }
})

const statusMap = {
  open: { bg: 'bg-blue-1', text: 'text-primary', label: 'OPEN' },
  in_progress: { bg: 'bg-amber-1', text: 'text-warning', label: 'IN PROGRESS' },
  resolved: { bg: 'bg-green-1', text: 'text-positive', label: 'RESOLVED' },
  closed: { bg: 'bg-grey-2', text: 'text-secondary', label: 'CLOSED' },
};

const styleConfig = computed(() => {
  return statusMap[props.status] || { bg: 'bg-grey-2', text: 'text-secondary', label: props.status || 'UNKNOWN' }
})

</script>
