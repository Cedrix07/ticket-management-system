<template>
  <q-page class="q-pa-lg q-pa-sm-xl bg-slate-50">

    <!-- Header Greeting & CTA -->
    <div class="flex justify-between items-center q-mb-lg">
      <div>
        <div class="text-h4 text-weight-bold text-dark q-mb-xs">
          Good morning, {{ authStore.user?.name?.split(' ')[0] || 'there' }}
        </div>
        <div class="text-body2 text-secondary">
          {{ subtitleText }}
        </div>
        <div class="text-caption text-secondary q-mt-xs">
          {{ currentDate }} · {{ roleWorkspaceText }}
        </div>
      </div>

      <!-- Top-Right Action Button -->
      <div>
        <!-- Employee: Create Ticket -->
        <q-btn
          v-if="authStore.isEmployee"
          color="primary"
          icon="add"
          label="Create ticket"
          to="/tickets/create"
          style="border-radius: 8px; height: 40px;"
          unelevated
        />
        <!-- Agent & Admin: Open Queue -->
        <q-btn
          v-else
          color="primary"
          icon="inbox"
          label="Open ticket queue"
          to="/tickets?unassigned=true"
          style="border-radius: 8px; height: 40px;"
          unelevated
        />
      </div>
    </div>

    <!--  KPI Metric Cards Grid -->
    <div class="row q-col-gutter-md q-mb-xl">
      <div
        v-for="card in metricCards"
        :key="card.label"
        class="col-12 col-sm-6 col-md"
      >
        <q-card
          flat
          bordered
          class="bg-white q-pa-md cursor-pointer hover-card"
          @click="card.to && router.push(card.to)"
          style="border-radius: 10px; height: 100%; min-height: 110px;"
        >
          <div class="flex justify-between items-center text-caption text-secondary q-mb-sm">
            <span>{{ card.label }}</span>
            <q-icon name="arrow_outward" size="14px" color="secondary" />
          </div>
          <div
            class="text-h4 text-weight-bold"
            :class="card.isUrgent ? 'text-negative' : 'text-dark'"
          >
            <q-skeleton v-if="isLoading" type="text" width="60px" />
            <span v-else>{{ card.value }}</span>
          </div>
        </q-card>
      </div>
    </div>

    <!-- Recent Tickets Table -->
    <q-card flat bordered class="bg-white q-mb-xl" style="border-radius: 10px;">
      <div class="flex justify-between items-center q-pa-lg border-bottom">
        <div class="text-subtitle1 text-weight-bold text-dark">
          {{ authStore.isAdmin ? 'Ticket overview' : 'Recently updated tickets' }}
        </div>
        <q-btn flat dense no-caps color="primary" label="View all tickets" to="/tickets" />
      </div>

      <q-table
        flat
        :rows="recentTickets"
        :columns="columns"
        row-key="id"
        hide-pagination
        :loading="isLoading"
        :rows-per-page-options="[5]"
      >
        <template v-slot:body-cell-ticket_number="props">
          <q-td :props="props">
            <router-link
              :to="'/tickets/' + props.row.id"
              class="text-primary text-weight-medium"
              style="text-decoration: none;"
            >
              {{ props.value }}
            </router-link>
          </q-td>
        </template>

        <!-- Custom Priority Slot -->
        <template v-slot:body-cell-priority="props">
          <q-td :props="props">
            <PriorityBadge :priority="props.value" />
          </q-td>
        </template>

        <!-- Custom Status Slot -->
        <template v-slot:body-cell-status="props">
          <q-td :props="props">
            <StatusBadge :status="props.value" />
          </q-td>
        </template>

        <!-- Custom Updated At Slot -->
        <template v-slot:body-cell-updated_at="props">
          <q-td :props="props">
            {{ formatDate(props.value) }}
          </q-td>
        </template>

        <!-- Empty state -->
        <template v-slot:no-data>
          <div class="full-width text-center q-pa-lg text-secondary">
            No tickets found.
          </div>
        </template>
      </q-table>
    </q-card>

    <!-- Bottom Section: Role-Specific Action Card -->
    <!-- Employee Bottom Card -->
    <q-card v-if="authStore.isEmployee" flat bordered class="bg-white q-pa-lg" style="border-radius: 10px;">
      <div class="text-subtitle1 text-weight-bold text-dark q-mb-xs">Need help with something new?</div>
      <div class="text-body2 text-secondary q-mb-md">
        Share the issue, choose a category and set its priority. Your support team will take it from there.
      </div>
      <q-btn color="primary" icon="add" label="Create ticket" to="/tickets/create" unelevated style="border-radius: 8px;" />
    </q-card>

    <!-- Support Agent Bottom Card -->
    <q-card v-else-if="authStore.isAgent" flat bordered class="bg-white q-pa-lg" style="border-radius: 10px;">
      <div class="text-subtitle1 text-weight-bold text-dark q-mb-xs">Queue needs attention</div>
      <div class="text-body2 text-secondary q-mb-md">
        {{ metrics.unassigned || 0 }} tickets are waiting for assignment. Review urgent requests first.
      </div>
      <q-btn outline color="primary" icon="mail_outline" label="Review unassigned tickets" to="/tickets?unassigned=true" style="border-radius: 8px;" />
    </q-card>

    <!-- Admin Bottom Card: Recent Activities -->
    <q-card v-else-if="authStore.isAdmin" flat bordered class="bg-white q-pa-lg" style="border-radius: 10px;">
      <div class="text-subtitle1 text-weight-bold text-dark q-mb-md">Recent ticket activities</div>
      <q-list separator>
        <q-item v-for="act in recentActivities" :key="act.id" class="q-px-none">
          <q-item-section avatar>
            <q-avatar size="32px" color="blue-1" text-color="primary" icon="history" />
          </q-item-section>
          <q-item-section>
            <q-item-label class="text-body2">
              <span class="text-weight-bold">{{ act.user?.name }}</span> {{ act.description || act.event_type }}
            </q-item-label>
            <q-item-label caption class="text-secondary">
              {{ act.ticket?.ticket_number }} · {{ act.ticket?.subject }}
            </q-item-label>
          </q-item-section>
          <q-item-section side>
            <span class="text-caption text-secondary">{{ formatDate(act.created_at) }}</span>
          </q-item-section>
        </q-item>
        <div v-if="!recentActivities.length" class="text-caption text-secondary text-center q-pa-md">
          No recent activity logged yet.
        </div>
      </q-list>
    </q-card>

  </q-page>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { api } from 'boot/axios';
import { useAuthStore } from 'src/stores/auth';
import { formatDate } from 'src/utils/formatter';
import StatusBadge from 'src/components/StatusBadge.vue';
import PriorityBadge from 'src/components/PriorityBadge.vue';

const router = useRouter();
const authStore = useAuthStore();
const metrics = ref({});
const recentTickets = ref([]);
const recentActivities = ref([]);
const isLoading = ref(true);

// Computed: Subtitle & Date
const subtitleText = computed(() => {
  return authStore.isEmployee
    ? 'Your requests, progress and recent updates in one place.'
    : 'Here is what needs your attention today.';
});

const roleWorkspaceText = computed(() => {
  const map = {
    admin: 'Admin workspace',
    support_agent: 'Support Agent workspace',
    employee: 'Employee workspace',
  };
  return map[authStore.user?.role] || 'Workspace';
});

const currentDate = new Intl.DateTimeFormat('en-US', {
  weekday: 'long',
  month: 'long',
  day: 'numeric',
  year: 'numeric',
}).format(new Date());

// Table columns definition
const columns = [
  { name: 'ticket_number', label: 'Ticket', field: 'ticket_number', align: 'left' },
  { name: 'subject', label: 'Subject', field: 'subject', align: 'left' },
  { name: 'priority', label: 'Priority', field: 'priority', align: 'left' },
  { name: 'status', label: 'Status', field: 'status', align: 'left' },
  { name: 'updated_at', label: 'Updated', field: 'updated_at', align: 'right' },
];

// Computed: Dynamic KPI Cards based on Role
const metricCards = computed(() => {
  if (authStore.isAdmin) {
    return [
      { label: 'Total users', value: metrics.value.total_users ?? 0, to: '/admin/users' },
      { label: 'Total tickets', value: metrics.value.total_tickets ?? 0, to: '/tickets' },
      { label: 'Open tickets', value: metrics.value.open ?? 0, to: '/tickets?status=open' },
      { label: 'In progress', value: metrics.value.in_progress ?? 0, to: '/tickets?status=in_progress' },
      { label: 'Resolved tickets', value: metrics.value.resolved ?? 0, to: '/tickets?status=resolved' },
      { label: 'Urgent tickets', value: metrics.value.urgent ?? 0, to: '/tickets?priority=urgent', isUrgent: true },
    ];
  }
  if (authStore.isAgent) {
    return [
      { label: 'Assigned to me', value: metrics.value.assigned_to_me ?? 0, to: '/tickets?assigned_to_me=true' },
      { label: 'Unassigned tickets', value: metrics.value.unassigned ?? 0, to: '/tickets?unassigned=true' },
      { label: 'Open tickets', value: metrics.value.open ?? 0, to: '/tickets?status=open' },
      { label: 'In progress', value: metrics.value.in_progress ?? 0, to: '/tickets?status=in_progress' },
      { label: 'Urgent tickets', value: metrics.value.urgent ?? 0, to: '/tickets?priority=urgent', isUrgent: true },
    ];
  }
  // Employee
  return [
    { label: 'My submitted tickets', value: metrics.value.my_submitted ?? 0, to: '/tickets' },
    { label: 'Open tickets', value: metrics.value.open ?? 0, to: '/tickets?status=open' },
    { label: 'In progress', value: metrics.value.in_progress ?? 0, to: '/tickets?status=in_progress' },
    { label: 'Resolved tickets', value: metrics.value.resolved ?? 0, to: '/tickets?status=resolved' },
  ];
});

// Fetch on mount
const fetchDashboardData = async () => {
  isLoading.value = true;
  try {
    const [metricsRes, ticketsRes] = await Promise.all([
      api.get('/dashboard/metrics'),
      api.get('/tickets', { params: { per_page: 5 } }),
    ]);

    metrics.value = metricsRes.data.data?.metrics || {};
    recentActivities.value = metricsRes.data.data?.recent_activities || [];
    recentTickets.value = ticketsRes.data.data || [];
  } catch (err) {
    console.error('Failed to load dashboard data', err);
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  fetchDashboardData();
});
</script>

<style scoped>
.hover-card {
  transition: transform 0.15s ease, border-color 0.15s ease;
}
.hover-card:hover {
  border-color: #2563EB;
  transform: translateY(-2px);
}
</style>
