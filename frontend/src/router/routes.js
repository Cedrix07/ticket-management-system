const routes = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      { path: '', component: () => import('pages/DashboardPage.vue') },
      { path: 'tickets', component: () => import('pages/TicketListPage.vue'), name: 'tickets' },
      { path: 'tickets/queue', component: () => import('pages/TicketListPage.vue') },
      { path: 'tickets/:id', component: () => import('pages/TicketDetailPage.vue')}
    ]
  },
  {
    path: '/login',
    component: () => import('pages/LoginPage.vue')
  },

  // Always leave this as last one,
  // but you can also remove it
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue')
  }
]

export default routes
