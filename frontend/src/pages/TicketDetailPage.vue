<template>
  <q-page class="q-pa-lg q-pa-sm-xl bg-slate-50">
    <!-- Header -->
     <div class="row">
      <div class="col-12 col-md-8">
        <router-link class="navigation-back text-primary" :to="{ name: 'tickets' }">
        <q-icon name="arrow_back"/> {{ authStore.isAdmin ? 'All tickets' : 'My tickets' }}
        </router-link>
        <span class="text-secondary"> / {{ ticket?.ticket_number }}</span>
      </div>
      <div class="col-12">
        <q-skeleton v-if="isLoading" type="text" width="45%" height="36px" class="q-my-sm" />
        <h4 v-else class="text-h4 q-mt-lg q-mb-sm text-weight-bold">{{ ticket?.subject }}</h4>
        <q-skeleton v-if="isLoading" type="text" width="25%" height="20px" />
        <p v-else class="text-subtitle1 text-secondary">{{ ticket?.ticket_number }} • Submitted by {{ ticket?.requester?.name }}</p>
      </div>
     </div>

     <!-- cards -->
     <div class="row q-col-gutter-lg">
       <!-- Left Column: 3 Cards neatly spaced -->
      <div class="col-12 col-md-8 q-gutter-y-lg">
        <!-- 1. Ticket Information Card -->
        <q-card flat bordered class="bg-white q-pa-lg q-card">
          <div class="text-h6 text-weight-bold">Ticket Information</div>
          <div class="q-mb-md">
            <div v-if="isLoading" class="row q-gutter-x-sm q-mb-md">
              <q-skeleton type="rect" width="75px" height="24px" style="border-radius: 4px;" />
              <q-skeleton type="rect" width="55px" height="24px" style="border-radius: 4px;" />
            </div>
            <div v-else class="q-mb-md">
              <StatusBadge :status="ticket?.status" class="q-mr-sm"/>
              <PriorityBadge :priority="ticket?.priority" />
            </div>
          </div>
          <div class="row q-col-gutter-y-md">
            <div class="col-12 col-sm-6">
              <p class="text-body2 text-secondary text-weight-light q-mb-xs">Requester</p>
              <q-skeleton v-if="isLoading" type="text" width="50%" height="20px" />
              <p v-else class="text-body2">{{ ticket?.requester?.name }}</p>
            </div>
            <div class="col-12 col-sm-6">
              <p class="text-body2 text-secondary text-weight-light q-mb-xs">Email</p>
              <q-skeleton v-if="isLoading" type="text" width="50%" height="20px" />
              <p v-else class="text-body2">{{ ticket?.requester?.email }}</p>
            </div>
          </div>
          <div class="row q-col-gutter-y-md">
            <div class="col-12 col-sm-6">
              <p class="text-body2 text-secondary text-weight-light q-mb-xs">Category</p>
              <q-skeleton v-if="isLoading" type="text" width="50%" height="20px" />
              <p v-else class="text-body2">{{ ticket?.category?.name }}</p>
            </div>
            <div class="col-12 col-sm-6">
              <p class="text-body2 text-secondary text-weight-light q-mb-xs">Created</p>
              <q-skeleton v-if="isLoading" type="text" width="50%" height="20px" />
              <p v-else class="text-body2">{{ formatDateTime(ticket?.created_at) }}</p>
            </div>
            <div class="col-12">
              <p class="text-body2 text-secondary text-weight-light q-mb-xs">Last Updated</p>
              <q-skeleton v-if="isLoading" type="text" width="50%" height="20px" />
              <p v-else class="text-body2">{{ formatDateTime(ticket?.updated_at) }}</p>
            </div>
          </div>
        </q-card>
        <!-- 2. Description Card -->
        <q-card flat bordered class="bg-white q-pa-lg q-card">
          <div class="text-h6 text-weight-bold q-mb-md">Description</div>
          <q-skeleton v-if="isLoading" type="text" width="100%" height="100px" />
          <p v-else class="text-body2 text-secondary">
            {{ ticket?.description }}
          </p>
        </q-card>
        <!-- 3. Comments Card -->
        <q-card flat bordered class="bg-white q-pa-lg q-card">
           <div class="text-h6 text-weight-bold q-mb-md">Comments and replies · {{ comments?.length }}</div>
           <div class="text-subtitle2 text-secondary q-mb-md">Conversation · Oldest first </div>
           <q-skeleton v-if="isLoading" type="text" width="100%" height="50px" />
           <div v-else >
             <div v-if="comments && comments.length > 0">
               <div v-for="comment in comments" :key="comment.id">
                <div class="row">
                  <div class="col-auto q-mr-md flex items-start">
                    <q-avatar size="32px" color="blue-1" text-color="primary" class="text-weight-bold text-caption">
                      {{ getInitials(comment?.user?.name ) }}
                    </q-avatar>
                  </div>
                  <div class="col-9">
                    <div class="q-mt-xs">
                      <span class="text-body2 q-mb-xs text-weight-bold">{{ comment?.user?.name }} </span>
                      <span class="text-caption text-weight-bold text-secondary q-ml-xs">· {{ formatEnum(comment?.user?.role) }}</span>
                      <p class="text-caption text-weight-medium text-secondary q-mb-xs">{{ formatDateTime(comment?.created_at) }}</p>
                    </div>
                    <div class="q-my-sm">
                      <p class="text-body2">{{ comment?.body }}</p>
                    </div>
                    <div>
                      <q-btn flat text-color="primary" label="Reply" size="sm"/>
                    </div>
                  </div>
                </div>
                <q-separator class="q-my-md"/>
               </div>
             </div>
             <div v-else>
              <p class="text-center text-subtitle1 text-secondary">No comments yet.</p>
             </div>
           </div>
           <div>
            <div v-if="ticket?.status !== 'closed'">
              <div class="text-h6 q-mb-sm">Add a comment</div>
                <q-input
                class="q-mb-sm"
                v-model="addComment"
                outlined
                color="primary"
                type="textarea"
                rows="4"
                :placeholder="ticket?.requester?.id === authStore.user?.id ? 'Add a reply' : 'Write your comment...'"
                />
                <q-btn
                  unelevated
                  no-caps
                  color="primary"
                  class="block q-ml-auto"
                  :disable="addComment.trim() === '' || isSubmitting"
                >
                  <q-icon name="o_send" size="14px" class="q-mr-xs q-mb-xs" style="transform: rotate(-30deg);" />
                  <span>Post comment</span>
                </q-btn>
              </div>
              <div v-else>
                <div class="text-body2 text-secondary">
                  This ticket is closed. Create a new ticket if you need more help with this issue.
                </div>
              </div>
            </div>
        </q-card>
      </div>
      <!-- Right Column: 3 Cards neatly spaced -->
      <div class="col-12 col-md-4 q-gutter-y-lg">
        <!-- 1. Assignment Card -->
        <q-card flat bordered class="bg-white q-pa-lg q-card">
          <div class="text-h6 text-weight-bold q-mb-md">Assignment</div>
           <div class="flex justify-start q-mb-sm">
             <q-avatar size="32px" color="blue-1" text-color="primary" class="text-weight-bold text-caption">
                {{ getInitials(ticket?.assignee?.name ) }}
              </q-avatar>
              <div class="q-ml-md">
                <div class="text-body2">
                  {{ ticket?.assignee?.name || 'Unassigned' }}
                </div>
                <div class="text-caption text-secondary">
                  {{ formatEnum(ticket?.assignee?.role) }}
                </div>
              </div>
           </div>
           <div v-if="authStore.isStaff && ticket?.status !== 'closed'">
            <div class="text-body2 q-mb-sm">Assign to</div>
            <q-select
              class="q-my-md"
              v-model="selectedAssigneeId"
              :options="supportAgents"
              option-label="name"
              option-value="id"
              emit-value
              map-options
              outlined
              dense
            />
            <q-btn dense outline color="primary" no-caps label="Update assignment" class="q-mb-sm q-px-md" style="border-radius: 4px;"/>
           </div>
        </q-card>
        <!-- 2. Status Actions Card -->
        <q-card flat bordered class="bg-white q-pa-lg q-card">
          <div class="text-h6 text-weight-bold q-mb-md">Assignment</div>
          
        </q-card>
        <!-- 3. Activity Timeline Card -->
        <q-card flat bordered class="bg-white q-pa-lg q-card">
          ...
        </q-card>
      </div>
     </div>
  </q-page>
</template>
<script setup>
import { onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { api } from 'boot/axios';
import { useAuthStore } from 'src/stores/auth';
import StatusBadge from 'src/components/StatusBadge.vue';
import PriorityBadge from 'src/components/PriorityBadge.vue';
import { formatDateTime, formatEnum, getInitials } from 'src/utils/formatter';

const route = useRoute();
const router = useRouter();
const ticketId = route.params.id;

const authStore = useAuthStore();

const ticket = ref(null);
const comments = ref([]);
const activities = ref([]);
const addComment = ref('');

const supportAgents = ref([]);
const isLoading = ref(true);
const isSubmitting = ref(false);
const selectedAssigneeId = ref(null);
const fetchTicketDetails = async () => {
  isLoading.value = true;
  try {
    const [ticketRes, commentsRes, activitiesRes] = await Promise.all([
      api.get('/tickets/' + ticketId),
      api.get('/tickets/' + ticketId + '/comments'),
      api.get('/tickets/' + ticketId + '/activities'),
    ]);
    ticket.value = ticketRes.data.data;
    comments.value = commentsRes.data.data || [];
    selectedAssigneeId.value = ticket.value?.assignee?.id || null;
    activities.value = activitiesRes.data.data || [];
  } catch (err)
  {
    if(err.response?.status === 404)
    {
      router.replace('/404')
    }
    console.error('Failed to fetch the ticket details', err);
  } finally {
    isLoading.value = false;
  }
}

const fetchSupportAgents = async () => {
  if (!authStore.isStaff) return;
  try {
    const res = await api.get('/users', {
      params: { role: 'support_agent', per_page: 50 }
    });
    supportAgents.value = res.data.data || [];
  } catch (err) {
    console.error('Failed to fetch support agents', err);
  }
};

onMounted(() => {
  fetchTicketDetails();
  fetchSupportAgents();
});

</script>
<style scoped>
.navigation-back{
  text-decoration: none;
}
</style>
