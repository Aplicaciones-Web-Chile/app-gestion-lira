const routes = [
  {
    path: '/',
    component: () => import('layouts/Main.vue'),
    children: [
      {
        path: '',
        redirect: '/dashboard'
      },
      {
        path: '/dashboard',
        meta: { requiresAuth: true },
        component: () => import('pages/Dashboard.vue')
      },
      {
        path: '/detail',
        name: 'detail',
        meta: { requiresAuth: true },
        component: () => import('pages/Detail.vue'),
        props: route => ({
          cardId: route.query.id,
          selectedDate: route.query.date
        })
      },
      {
        path: '/login',
        name: 'login',
        meta: { requiresAuth: false },
        component: () => import('pages/login.vue')
      },
      {
        path: '/logout',
        name: 'logout',
        meta: { requiresAuth: false },
        component: () => import('pages/logout.vue')
      }
    ]
  },
  // Ruta para el error 404
  {
    path: '*',
    name: '404',
    component: () => import('pages/Error404.vue')
  }
]

export default routes
