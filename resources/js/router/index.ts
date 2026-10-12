import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import AppLayout from '@/layouts/AppLayout.vue';

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/login',
      name: 'login',
      component: () => import('@/pages/Login.vue'),
      meta: { guest: true },
    },
    {
      path: '/',
      component: AppLayout,
      meta: { requiresAuth: true },
      children: [
        {
          path: '',
          name: 'home',
          component: () => import('@/pages/Home.vue'),
        },
        {
          path: 'products',
          name: 'products',
          component: () => import('@/pages/products/Index.vue'),
        },
        {
          path: 'products/create',
          name: 'products.create',
          component: () => import('@/pages/products/Create.vue'),
        },
        {
          path: 'products/:id/edit',
          name: 'products.edit',
          component: () => import('@/pages/products/Edit.vue'),
        },
        {
          path: 'companies',
          name: 'companies',
          component: () => import('@/pages/companies/Index.vue'),
        },
        {
          path: 'companies/create',
          name: 'companies.create',
          component: () => import('@/pages/companies/Create.vue'),
        },
        {
          path: 'companies/:id/edit',
          name: 'companies.edit',
          component: () => import('@/pages/companies/Edit.vue'),
        },
        {
          path: 'genres',
          name: 'genres',
          component: () => import('@/pages/genres/Index.vue'),
        },
        {
          path: 'genres/create',
          name: 'genres.create',
          component: () => import('@/pages/genres/Create.vue'),
        },
        {
          path: 'genres/:id/edit',
          name: 'genres.edit',
          component: () => import('@/pages/genres/Edit.vue'),
        },
        {
          path: 'biddings',
          name: 'biddings',
          component: () => import('@/pages/biddings/Index.vue'),
        },
        {
          path: 'biddings/:id',
          name: 'biddings.show',
          component: () => import('@/pages/biddings/Show.vue'),
        },
        {
          path: 'biddings/create',
          name: 'biddings.create',
          component: () => import('@/pages/biddings/Create.vue'),
        },
        {
          path: 'biddings/:id/edit',
          name: 'biddings.edit',
          component: () => import('@/pages/biddings/Edit.vue'),
        },
        {
          path: 'biddings/:id/items',
          name: 'biddings.items',
          component: () => import('@/pages/biddings/Items.vue'),
        },
        {
          path: 'contracts',
          name: 'contracts',
          component: () => import('@/pages/contracts/Index.vue'),
        },
        {
          path: 'contracts/:id',
          name: 'contracts.show',
          component: () => import('@/pages/contracts/Show.vue'),
        },
        {
          path: 'contracts/create',
          name: 'contracts.create',
          component: () => import('@/pages/contracts/Create.vue'),
        },
        {
          path: 'contracts/:id/edit',
          name: 'contracts.edit',
          component: () => import('@/pages/contracts/Edit.vue'),
        },
        {
          path: 'supply-orders',
          name: 'supply-orders',
          component: () => import('@/pages/supply-orders/Index.vue'),
        },
        {
          path: 'supply-orders/create',
          name: 'supply-orders.create',
          component: () => import('@/pages/supply-orders/Create.vue'),
        },
        {
          path: 'supply-orders/:id',
          name: 'supply-orders.show',
          component: () => import('@/pages/supply-orders/Show.vue'),
        },
        // Futuras rotas (checkpoints, rounds...)
      ],
    },
  ],
});

router.beforeEach(async (to) => {
  const auth = useAuthStore();

  if (!auth.user && to.meta.requiresAuth) {
    await auth.fetchUser();
  }

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return '/login';
  }

  if (to.meta.guest && auth.isAuthenticated) {
    return '/';
  }
  
});

export default router;