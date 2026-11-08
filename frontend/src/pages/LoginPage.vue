<template>
  <q-layout view="hHh lpR fFf">
    <q-page-container>
      <q-page class="flex flex-center column q-pa-md q-py-xl">

        <!-- 1. Brand Logo for Desktop (Pinned top-left) -->
        <div class="gt-sm fixed-top-left q-pa-lg flex items-center q-gutter-sm">
          <q-avatar size="36px" color="primary" text-color="white" icon="support_agent" rounded />
          <span class="text-weight-bold text-h6 text-dark">Help Desk</span>
        </div>

        <!-- Wrapper to keep mobile logo and card aligned to max-width: 440px -->
        <div style="width: 100%; max-width: 440px;">

          <!-- 2. Brand Logo for mobile  -->
          <div class="lt-md flex items-center q-gutter-sm q-mb-lg">
            <q-avatar size="36px" color="primary" text-color="white" icon="support_agent" rounded />
            <span class="text-weight-bold text-h6 text-dark">Help Desk</span>
          </div>

          <!-- 3. Responsive Card -->
          <q-card class="bg-white q-pa-lg q-pa-sm-xl full-width">

            <div class="text-h5 text-weight-bold text-dark q-mb-xs">Welcome back</div>
            <div class="text-body2 text-secondary q-mb-lg">
              Sign in to manage your tickets and get the support you need.
            </div>

            <!-- Server Error Alert Banner -->
            <AlertBanner
              v-if="errorMessage"
              type="negative"
              title="Email or password is incorrect."
              :message="errorMessage"
              @dismiss="errorMessage = ''"
            />

            <!-- Form -->
            <q-form @submit.prevent="handleLogin" class="q-gutter-y-md">
              <div>
                <div class="text-weight-medium text-dark q-mb-xs">Email *</div>
                <q-input
                  outlined
                  dense
                  v-model="form.email"
                  placeholder="you@company.com"
                  :rules="[required('Email'), isValidEmail]"
                  lazy-rules
                />
              </div>

              <div>
                <div class="text-weight-medium text-dark q-mb-xs">Password *</div>
                <q-input
                  outlined
                  dense
                  v-model="form.password"
                  :type="showPassword ? 'text' : 'password'"
                  placeholder="Enter your password"
                  :rules="[required('Password')]"
                  lazy-rules
                >
                  <template v-slot:append>
                    <q-icon
                      :name="showPassword ? 'visibility_off' : 'visibility'"
                      class="cursor-pointer text-secondary"
                      @click="showPassword = !showPassword"
                    />
                  </template>
                </q-input>
              </div>

              <q-btn
                type="submit"
                color="primary"
                label="Sign in"
                class="full-width q-py-sm text-weight-medium"
                style="border-radius: 8px; height: 44px;"
                :loading="authStore.isLoading"
                :disable="authStore.isLoading"
              >
                <template v-slot:loading>
                  <q-spinner-oval class="on-left" />
                    Signing in...
                </template>
              </q-btn>
            </q-form>

            <div class="text-center text-caption text-secondary q-mt-lg">
              Need an account? Contact your Help Desk administrator.
            </div>
          </q-card>

          <!-- Footer -->
          <div class="q-mt-xl text-center text-caption text-secondary">
            <div>Your workspace is available according to your role.</div>
            <div>© 2026 Acme · Internal support portal</div>
          </div>

        </div>

      </q-page>
    </q-page-container>
  </q-layout>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from 'src/stores/auth';
import { required, isValidEmail } from 'src/utils/validation';
import AlertBanner from 'src/components/AlertBanner.vue';

const authStore = useAuthStore();
const router = useRouter();
const route = useRoute();

const form = reactive({
  email: '',
  password: ''
})

const showPassword = ref(false);
const errorMessage = ref(''); // For server errors

const handleLogin = async () => {
  // Reset error state on new attempt
  errorMessage.value = '';

  try {
    await authStore.login(form);
    const redirect = route.query.redirect || '/';
    router.push(redirect);
  } catch {
    errorMessage.value = authStore.error || 'Login failed. Please check your credentials.';
  }
};
</script>
