<template>
  <q-layout view="lHh LpR lFf">

    <!-- Top Header -->
    <q-header bordered class="bg-white text-dark" height-hint="64">
      <q-toolbar style="height: 64px;" class="q-px-lg">
        <!-- Mobile Drawer Toggle -->
        <q-btn
          flat
          dense
          round
          icon="menu"
          class="lt-md q-mr-sm"
          @click="leftDrawerOpen = !leftDrawerOpen"
        />

        <!-- Title -->
        <q-toolbar-title class="text-subtitle1 text-weight-medium">
          Dashboard
        </q-toolbar-title>

        <!-- User Menu Dropdown (Top-Right) -->
        <div class="flex items-center cursor-pointer q-gutter-x-sm">
          <q-avatar size="32px" color="blue-1" text-color="primary" class="text-weight-bold text-caption">
            {{ getInitials(authStore.user?.name) }}
          </q-avatar>
          <span class="text-body2 text-weight-medium gt-xs">{{ authStore.user?.name }}</span>
          <q-icon name="keyboard_arrow_down" size="18px" color="secondary" />

          <!-- Dropdown Menu -->
          <q-menu fit anchor="bottom right" self="top right">
            <q-list style="min-width: 160px">
              <q-item clickable v-close-popup to="/profile">
                <q-item-section avatar><q-icon name="person_outline" size="20px" /></q-item-section>
                <q-item-section>My Profile</q-item-section>
              </q-item>
              <q-separator />
              <q-item clickable v-close-popup @click="handleLogout" class="text-negative">
                <q-item-section avatar><q-icon name="logout" size="20px" /></q-item-section>
                <q-item-section>Logout</q-item-section>
              </q-item>
            </q-list>
          </q-menu>
        </div>
      </q-toolbar>
    </q-header>

    <!-- Sidebar Drawer -->
    <q-drawer
      v-model="leftDrawerOpen"
      show-if-above
      bordered
      :width="240"
      class="bg-white flex column justify-between"
    >
      <!-- Top Section: Brand + Workspace Title + Links -->
      <div class="q-pa-md">
        <!-- Logo -->
        <div class="flex items-center q-gutter-x-sm q-mb-xl q-px-sm">
          <q-avatar size="32px" color="primary" text-color="white" icon="support_agent" rounded />
          <span class="text-weight-bold text-h6 text-dark">Help Desk</span>
        </div>

        <!-- Role Workspace Label -->
        <div class="text-caption text-weight-bold text-secondary q-px-sm q-mb-sm text-uppercase" style="letter-spacing: 0.05em; font-size: 11px;">
          {{ workspaceLabel }}
        </div>

        <!-- Navigation Links -->
        <q-list class="q-gutter-y-xs">
          <q-item
            v-for="item in navItems"
            :key="item.label + item.to"
            clickable
            v-ripple
            :to="item.to"
            exact
            active-class="bg-blue-1 text-primary text-weight-medium"
            class="text-secondary"
            style="border-radius: 8px; min-height: 40px;"
          >
            <q-item-section avatar style="min-width: 36px;">
              <q-icon :name="item.icon" size="20px" />
            </q-item-section>
            <q-item-section class="text-body2">
              {{ item.label }}
            </q-item-section>
          </q-item>
        </q-list>
      </div>

      <!-- Bottom Section: Logout + User Identity Card -->
      <div class="q-pa-md border-top">
        <!-- Logout Item -->
        <q-item
          clickable
          v-ripple
          @click="handleLogout"
          class="text-secondary q-mb-sm"
          style="border-radius: 8px; min-height: 40px;"
        >
          <q-item-section avatar style="min-width: 36px;">
            <q-icon name="logout" size="20px" />
          </q-item-section>
          <q-item-section class="text-body2">Logout</q-item-section>
        </q-item>

        <q-separator class="q-my-sm" />

        <!-- User Profile Tile -->
        <div class="flex items-center q-gutter-x-sm q-px-sm q-py-xs">
          <q-avatar size="36px" color="blue-1" text-color="primary" class="text-weight-bold text-caption">
            {{ getInitials(authStore.user?.name) }}
          </q-avatar>
          <div class="column">
            <span class="text-caption text-weight-bold text-dark ellipsis" style="max-width: 130px;">
              {{ authStore.user?.name }}
            </span>
            <span class="text-caption text-secondary" style="font-size: 11px;">
              {{ formatEnum(authStore.user?.role) }}
            </span>
          </div>
        </div>
      </div>
    </q-drawer>

    <!-- Page Content Area -->
    <q-page-container>
      <router-view />
    </q-page-container>

  </q-layout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from 'src/stores/auth';
import { getInitials, formatEnum } from 'src/utils/formatter';

const authStore = useAuthStore();
const router = useRouter();

// Navigation configuration
const allNavItems = [
  { label: 'Dashboard', icon: 'grid_view', to: '/', roles: ['admin', 'support_agent', 'employee'] },
  { label: 'All Tickets', icon: 'inbox', to: '/tickets', roles: ['admin'] },
  { label: 'My Tickets', icon: 'inbox', to: '/tickets?assigned_to_me=true', roles: ['support_agent'] },
  { label: 'My Tickets', icon: 'inbox', to: '/tickets', roles: ['employee'] },
  { label: 'Ticket Queue', icon: 'mail_outline', to: '/tickets?unassigned=true', roles: ['admin', 'support_agent'] },
  { label: 'Create Ticket', icon: 'add_circle_outline', to: '/tickets/create', roles: ['employee'] },
  { label: 'User Management', icon: 'people_outline', to: '/admin/users', roles: ['admin'] },
  { label: 'Categories', icon: 'folder_open', to: '/admin/categories', roles: ['admin'] },
  { label: 'My Profile', icon: 'account_circle', to: '/profile', roles: ['admin', 'support_agent', 'employee'] },
];

const navItems = computed(() => {
  const currentRole = authStore.user?.role;
  return allNavItems.filter(item => item.roles.includes(currentRole));
});

const leftDrawerOpen = ref(false);

const workspaceLabel = computed(() => {
  const map = {
    admin: 'ADMIN WORKSPACE',
    support_agent: 'SUPPORT AGENT WORKSPACE',
    employee: 'EMPLOYEE WORKSPACE',
  };
  return map[authStore.user?.role] || 'WORKSPACE';
});


const handleLogout = async () => {
  await authStore.logout()
  router.push('/login')
}
</script>
