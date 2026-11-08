import { defineStore } from 'pinia'
import { api } from 'boot/axios'

export const useAuthStore = defineStore('auth', {
    state: () => ({
        token: localStorage.getItem('token') || null,
        user: JSON.parse(localStorage.getItem('user')) || null,
        isLoading: false,
        error: null
    }),
    getters: {
      isAuthenticated: (state) => !! state.token,
      isAdmin: (state) => state.user?.role === 'admin',
      isAgent: (state) => state.user?.role === 'support_agent',
      isStaff: (state) => ['admin', 'support_agent'].includes(state.user?.role),
      isEmployee: (state) => state.user?.role === 'employee',
    },
    actions: {
      async login(credentials) {
        this.isLoading = true
        this.error = null
        try {
          const res = await api.post('/login', credentials)
          const {token, user } = res.data
          // update the state
          this.token = token
          this.user = user

          // save to local storage
          localStorage.setItem('token', token)
          localStorage.setItem('user', JSON.stringify(user))
        } catch(error) {
          this.error = error.response?.data?.message || 'Login failed'
          throw error
        } finally {
          this.isLoading = false
        }
      },
      async logout() {
        try {
          await api.post('/logout')
        } catch (error) {
          console.warn('Backend logout failed, clearing local session anyway.', error)
        } finally {
          this.token = null
          this.user = null
          localStorage.removeItem('token')
          localStorage.removeItem('user')
        }
      },
      async fetchMe() {
         try {
          const res = await api.get('/me')
          this.user = res.data.data || res.data
          localStorage.setItem('user', JSON.stringify(this.user))
        } catch(error) {
          console.log('Error', error)
        }
      },
    }
})
