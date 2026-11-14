<template>
  <q-page class="q-pa-lg q-pa-sm-xl bg-slate-50">

    <!-- Header: Title, Subtitle & Action Button -->
    <div class="flex justify-between items-center q-mb-lg">
      <div>
        <div class="text-h4 text-weight-bold text-dark q-mb-xs">
          {{ pageTitle }}
        </div>
        <div class="text-body2 text-secondary">
          {{ pageSubtitle }}
        </div>
      </div>

      <!-- Top-Right Action Button (Visible for Standard Ticket Views) -->
      <div v-if="!isQueueView">
        <q-btn
          color="primary"
          icon="add"
          label="Create ticket"
          to="/tickets/create"
          style="border-radius: 8px; height: 40px;"
          unelevated
        />
      </div>
    </div>

    <!-- Ticket Queue Filter Tabs (Only rendered on Queue View) -->
    <div v-if="isQueueView" class="q-mb-md">
      <q-tabs
        v-model="activeQueueTab"
        dense
        no-caps
        align="left"
        indicator-color="primary"
        active-color="primary"
        class="text-secondary queue-tabs"
      >
        <q-tab name="all" label="All Tickets" />
        <q-tab name="unassigned" label="Unassigned" />
        <q-tab name="assigned_to_me" label="Assigned to Me" />
        <q-tab name="urgent" label="Urgent Tickets" />
      </q-tabs>
    </div>

    <!-- Main Table Card Container -->
    <q-card flat bordered class="bg-white q-pa-lg q-card">

      <!-- 1. Search Bar -->
      <q-input
        v-model="filters.search"
        outlined
        dense
        placeholder="Search tickets by number or subject"
        class="q-mb-md filter-input"
        @keydown.enter="handleSearch"
        @change="handleSearch"
      >
        <template v-slot:prepend>
          <q-icon name="search" size="18px" color="secondary" />
        </template>
        <template v-if="filters.search" v-slot:append>
          <q-icon name="close" size="16px" class="cursor-pointer" @click="clearSearch" />
        </template>
      </q-input>

      <!-- 2. Filter Dropdown Controls -->
      <div class="flex items-center q-gutter-sm q-mb-md">
        <!-- Priority Select -->
        <q-select
          v-model="filters.priority"
          :options="priorityOptions"
          outlined
          dense
          emit-value
          map-options
          prefix="Priority:"
          class="filter-select"
          @update:model-value="onFilterChange"
        />

        <!-- Status Select -->
        <q-select
          v-model="filters.status"
          :options="statusOptions"
          outlined
          dense
          emit-value
          map-options
          prefix="Status:"
          class="filter-select"
          @update:model-value="onFilterChange"
        />

        <!-- Category Select (Standard Views) -->
        <q-select
          v-if="!isQueueView"
          v-model="filters.category_id"
          :options="categoryOptions"
          outlined
          dense
          emit-value
          map-options
          prefix="Category:"
          class="filter-select"
          @update:model-value="onFilterChange"
        />

        <!-- Date Select (Standard Views) -->
        <q-select
          v-if="!isQueueView"
          v-model="filters.date"
          :options="dateOptions"
          outlined
          dense
          emit-value
          map-options
          prefix="Date:"
          class="filter-select"
          @update:model-value="onFilterChange"
        />

        <!-- Clear Filters Button -->
        <q-btn
          flat
          dense
          no-caps
          color="secondary"
          label="Clear filters"
          class="text-caption text-weight-medium q-px-sm"
          @click="clearFilters"
        />
      </div>

      <!-- 3. Count & Sorting Header -->
      <div class="text-caption text-secondary q-mb-md">
        {{ summaryText }} · Sorted by latest update
      </div>

      <!-- 4. Tickets Table -->
      <q-table
        flat
        :rows="tickets"
        :columns="columns"
        row-key="id"
        hide-pagination
        :loading="isLoading"
        :rows-per-page-options="[0]"
        class="tickets-table"
      >
        <!-- Ticket Number Link Slot -->
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

        <!-- Subject Slot with Ellipsis -->
        <template v-slot:body-cell-subject="props">
          <q-td :props="props" class="ellipsis" style="max-width: 260px;">
            <span :title="props.value">{{ props.value }}</span>
          </q-td>
        </template>

        <!-- Category Slot -->
        <template v-slot:body-cell-category="props">
          <q-td :props="props">
            {{ props.row.category?.name || 'General' }}
          </q-td>
        </template>

        <!-- Priority Badge Slot -->
        <template v-slot:body-cell-priority="props">
          <q-td :props="props">
            <PriorityBadge :priority="props.value" />
          </q-td>
        </template>

        <!-- Status Badge Slot -->
        <template v-slot:body-cell-status="props">
          <q-td :props="props">
            <StatusBadge :status="props.value" />
          </q-td>
        </template>

        <!-- Requester Slot -->
        <template v-slot:body-cell-requester="props">
          <q-td :props="props">
            {{ props.row.requester?.name || '-' }}
          </q-td>
        </template>

        <!-- Assignee Slot -->
        <template v-slot:body-cell-assignee="props">
          <q-td :props="props">
            {{ props.row.assignee?.name || 'Unassigned' }}
          </q-td>
        </template>

        <!-- Created At Slot -->
        <template v-slot:body-cell-created_at="props">
          <q-td :props="props">
            {{ formatShortDate(props.value) }}
          </q-td>
        </template>

        <!-- Action View Link Slot (Only on Ticket Queue View) -->
        <template v-slot:body-cell-action="props">
          <q-td :props="props" align="right">
            <div class="row items-center justify-end q-gutter-x-xs no-wrap">
              <!-- 1. Eye Icon (View Details): Visible to EVERYONE -->
              <q-btn
                flat
                round
                dense
                size="sm"
                color="secondary"
                icon="visibility"
                :to="'/tickets/' + props.row.id"
              >
                <q-tooltip>View details</q-tooltip>
              </q-btn>
              <!-- 2. Assign Action: Visible ONLY to Admin & Support Agent (TODO: Option A Modal) -->
              <!-- <q-btn
                v-if="authStore.isStaff && !props.row.assignee"
                flat
                round
                dense
                size="sm"
                color="primary"
                icon="person_add_alt"
                @click="openAssignDialog(props.row)"
              >
                <q-tooltip>Assign ticket</q-tooltip>
              </q-btn> -->
            </div>
          </q-td>
        </template>

        <!-- Empty State -->
        <template v-slot:no-data>
          <div class="full-width text-center q-pa-xl text-secondary">
            <q-icon name="inbox" size="40px" class="q-mb-sm text-grey-4" />
            <div class="text-body2">No tickets found matching your criteria.</div>
          </div>
        </template>
      </q-table>

      <!-- 5. Table Footer / Pagination -->
      <div class="flex justify-between items-center q-pt-md border-top">
        <div class="text-caption text-secondary">
          {{ paginationRangeText }}
        </div>

        <div class="flex items-center q-gutter-x-md">
          <span class="text-caption text-secondary">
            Rows per page: {{ pagination.rowsPerPage }}
          </span>
          <q-pagination
            v-model="pagination.page"
            :max="pagination.totalPages"
            direction-links
            flat
            dense
            color="primary"
            active-color="primary"
            @update:model-value="onPageChange"
          />
        </div>
      </div>
    </q-card>

    <!-- Bottom Caption for Queue View -->
    <div v-if="isQueueView" class="text-caption text-secondary q-mt-md">
      Showing tickets you are authorized to access. Assignment changes are recorded in the activity timeline.
    </div>

  </q-page>
</template>

<script setup>
import { ref, reactive, computed, watch, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { api } from 'boot/axios';
import { useAuthStore } from 'src/stores/auth';
import { formatShortDate } from 'src/utils/formatter';
import PriorityBadge from 'src/components/PriorityBadge.vue';
import StatusBadge from 'src/components/StatusBadge.vue';

const route = useRoute();
const authStore = useAuthStore();

const tickets = ref([]);
const categories = ref([]);
const isLoading = ref(false);

const filters = reactive({
  search: '',
  priority: '',
  status: '',
  category_id: '',
  date: '',
});

const activeQueueTab = ref('all');

const pagination = reactive({
  page: 1,
  rowsPerPage: 8,
  total: 0,
  totalPages: 1,
});


const priorityOptions = [
  { label: 'All', value: '' },
  { label: 'Low', value: 'low' },
  { label: 'Medium', value: 'medium' },
  { label: 'High', value: 'high' },
  { label: 'Urgent', value: 'urgent' },
];

const statusOptions = [
  { label: 'All', value: '' },
  { label: 'Open', value: 'open' },
  { label: 'In Progress', value: 'in_progress' },
  { label: 'Resolved', value: 'resolved' },
  { label: 'Closed', value: 'closed' },
];

const dateOptions = [
  { label: 'Any time', value: '' },
  { label: 'Today', value: 'today' },
  { label: 'This week', value: 'week' },
  { label: 'This month', value: 'month' },
];

const categoryOptions = computed(() => {
  return [
    { label: 'All', value: '' },
    ...categories.value.map(c => ({ label: c.name, value: c.id })),
  ];
});


const isQueueView = computed(() => {
  return route.path.includes('/queue') || route.query.queue === 'true';
});

const pageTitle = computed(() => {
  if (isQueueView.value) return 'Ticket queue';
  if (authStore.isAdmin) return 'All tickets';
  return 'My tickets';
});

const pageSubtitle = computed(() => {
  if (isQueueView.value) {
    return 'Find, prioritize and assign work that needs attention.';
  }
  if (authStore.isAdmin) {
    return 'Manage and track support requests across your workspace.';
  }
  if (authStore.isEmployee) {
    return 'Track every request you have submitted.';
  }
  return 'Tickets assigned to you, ready for your next action.';
});

const summaryText = computed(() => {
  const count = pagination.total;
  if (isQueueView.value && activeQueueTab.value === 'assigned_to_me') {
    return `${count} assigned to me tickets`;
  }
  if (authStore.isEmployee) {
    return `${count} submitted tickets`;
  }
  return `${count} total tickets`;
});

const paginationRangeText = computed(() => {
  if (!pagination.total) return '0 of 0 results';
  const start = (pagination.page - 1) * pagination.rowsPerPage + 1;
  const end = Math.min(pagination.page * pagination.rowsPerPage, pagination.total);
  return `${start}-${end} of ${pagination.total} results`;
});


const columns = computed(() => {
  const base = [
    { name: 'ticket_number', label: 'Ticket', field: 'ticket_number', align: 'left' },
    { name: 'subject', label: 'Subject', field: 'subject', align: 'left' },
    { name: 'category', label: 'Category', field: 'category', align: 'left' },
    { name: 'priority', label: 'Priority', field: 'priority', align: 'left' },
    { name: 'status', label: 'Status', field: 'status', align: 'left' },
    { name: 'requester', label: 'Requester', field: 'requester', align: 'left' },
    { name: 'assignee', label: 'Assigned to', field: 'assignee', align: 'left' },
    { name: 'created_at', label: 'Created', field: 'created_at', align: 'left' },
    { name: 'action', label: 'Action', field: 'id', align: 'right' }
  ];
  return base;
});

// Fetch Categories for the Category filter dropdown
const fetchCategories = async () => {
  try {
    const res = await api.get('/ticket-categories');
    categories.value = res.data.data || [];
  } catch (err) {
    console.error('Failed to load categories', err);
  }
};

// Fetch Tickets from API with query params
const fetchTickets = async () => {
  isLoading.value = true;
  try {
    const params = {
      page: pagination.page,
      per_page: pagination.rowsPerPage,
    };

    // Attach filters only if they contain non-empty values
    if (filters.search) params.search = filters.search;
    if (filters.priority) params.priority = filters.priority;
    if (filters.status) params.status = filters.status;
    if (filters.category_id) params.category_id = filters.category_id;

    // Contextual Role & Queue Tab Scoping
    if (isQueueView.value) {
      if (activeQueueTab.value === 'unassigned') {
        params.unassigned = true;
      } else if (activeQueueTab.value === 'assigned_to_me') {
        params.assigned_to_me = true;
      } else if (activeQueueTab.value === 'urgent') {
        params.priority = 'urgent';
      }
    } else if (authStore.isAgent) {
      // Support Agent on standard view only sees tickets assigned to them
      params.assigned_to_me = true;
    }

    const res = await api.get('/tickets', { params });
    tickets.value = res.data.data || [];
    pagination.total = res.data.meta?.total || 0;
    pagination.totalPages = res.data.meta?.last_page || 1;
  } catch (err) {
    console.error('Failed to fetch tickets', err);
  } finally {
    isLoading.value = false;
  }
};


let lastSearched = ''
const handleSearch = () => {
  const currentSearch = filters.search.trim();

  if(currentSearch === lastSearched) {
    return;
  }
  lastSearched = currentSearch;
  pagination.page = 1;
  fetchTickets();
};


const clearSearch = () => {
  filters.search = '';
  lastSearched = '';
  pagination.page = 1;
  fetchTickets();
};

const onFilterChange = () => {
  pagination.page = 1;
  fetchTickets();
};

const clearFilters = () => {
  filters.search = '';
  filters.priority = '';
  filters.status = '';
  filters.category_id = '';
  filters.date = '';
  pagination.page = 1;
  fetchTickets();
};

const onPageChange = (newPage) => {
  pagination.page = newPage;
  fetchTickets();
};

// Watchers
watch(activeQueueTab, () => {
  pagination.page = 1;
  fetchTickets();
});

watch(() => route.fullPath, () => {
  if (route.query.unassigned === 'true') {
    activeQueueTab.value = 'unassigned';
  } else if (route.query.priority === 'urgent') {
    activeQueueTab.value = 'urgent';
  }
  fetchTickets();
});

onMounted(() => {
  if (route.query.unassigned === 'true') {
    activeQueueTab.value = 'unassigned';
  } else if (route.query.priority === 'urgent') {
    activeQueueTab.value = 'urgent';
  }
  fetchCategories();
  fetchTickets();
});
</script>

<style scoped>
.filter-input {
  max-width: 100%;
}

.filter-select {
  min-width: 130px;
}

:deep(.filter-select .q-field__prefix) {
  color: #64748B;
  font-weight: 500;
  font-size: 13px;
  padding-right: 4px;
}

:deep(.filter-select .q-field__native) {
  font-size: 13px;
  font-weight: 500;
}

:deep(.tickets-table th) {
  color: #64748B;
  font-size: 12px;
  font-weight: 600;
  text-transform: capitalize;
  background-color: #F8FAFC;
}

:deep(.tickets-table td) {
  font-size: 13px;
  color: #0F172A;
  height: 52px;
}

.queue-tabs {
  border-bottom: 1px solid #E2E8F0;
}

:deep(.queue-tabs .q-tab) {
  padding: 0 16px;
  font-size: 14px;
  font-weight: 500;
}
</style>
