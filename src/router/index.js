import Vue from 'vue'
import VueRouter from 'vue-router'
import routes from './routes'

Vue.use(VueRouter)

const Router = new VueRouter({
  scrollBehavior: () => ({ x: 0, y: 0 }),
  routes,
  mode: 'history'
})

// Navegación Guard
Router.beforeEach((to, from, next) => {
  const requiresAuth = to.matched.some(record => record.meta.requiresAuth)
  const isAuthenticated = localStorage.getItem('authToken')

  // Si la ruta requiere autenticación y no hay token
  if (requiresAuth && !isAuthenticated) {
    next({
      name: 'login',
      query: { redirect: to.fullPath }
    })
  }
  // Si vamos al login y ya estamos autenticados
  else if (to.name === 'login' && isAuthenticated) {
    next({ name: 'dashboard' })
  }
  // En cualquier otro caso, continuar normalmente
  else {
    next()
  }
})

export default Router
