<template>
  <div class="min-h-screen bg-gray-50 p-6">
    <div class="max-w-7xl mx-auto">
      <h1 class="text-2xl font-bold text-gray-800 mb-6">Reports</h1>

      <!-- Filters -->
      <div class="bg-white rounded-xl shadow p-6 mb-6">
        <div class="flex flex-wrap items-end gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
            <select
              v-model="selectedYear"
              class="block w-40 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
            >
              <option value="">All</option>
              <option v-for="year in years" :key="year" :value="year">{{ year }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Month</label>
            <select
              v-model="selectedMonth"
              class="block w-44 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
            >
              <option value="">All</option>
              <option v-for="(month, index) in months" :key="index" :value="index + 1">
                {{ month }}
              </option>
            </select>
          </div>
          <div>
            <button
              @click="fetchReports"
              :disabled="loading"
              class="inline-flex items-center px-5 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 transition-colors"
            >
              <svg
                v-if="loading"
                class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
              >
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
              </svg>
              {{ loading ? 'Loading...' : 'Filter' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Error Message -->
      <div v-if="error" class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
        <div class="flex items-center">
          <svg class="h-5 w-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path
              fill-rule="evenodd"
              d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
              clip-rule="evenodd"
            />
          </svg>
          <p class="text-sm text-red-700">{{ error }}</p>
        </div>
      </div>

      <!-- Summary Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow p-6">
          <p class="text-sm font-medium text-gray-500">Total Orders</p>
          <p class="mt-1 text-3xl font-bold text-gray-900">{{ totalOrders }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-6">
          <p class="text-sm font-medium text-gray-500">Total Revenue</p>
          <p class="mt-1 text-3xl font-bold text-green-600">{{ formatCurrency(totalRevenue) }}</p>
        </div>
      </div>

      <!-- Reports Table -->
      <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Order Code
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Date
                </th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Total Amount
                </th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Items Count
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-if="loading">
                <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                  <svg
                    class="animate-spin h-8 w-8 text-blue-600 mx-auto mb-2"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                  >
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                  </svg>
                  Loading reports...
                </td>
              </tr>
              <tr v-else-if="reports.length === 0">
                <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                  No reports found for the selected period.
                </td>
              </tr>
              <tr v-for="report in reports" :key="report.id" class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  {{ report.order_code }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(report.created_at) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                  {{ formatCurrency(report.total_amount) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                  {{ report.details ? report.details.length : 0 }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useAuthStore } from '~/stores/auth';

definePageMeta({
  layout: 'admin',
  middleware: ['admin'],
});

const config = useRuntimeConfig();
const apiBase = process.server ? 'http://127.0.0.1:8000/api/v1' : config.public.apiBase;
const authStore = useAuthStore();

const years = [2024, 2025, 2026, 2027];
const months = [
  'January', 'February', 'March', 'April', 'May', 'June',
  'July', 'August', 'September', 'October', 'November', 'December',
];

const selectedYear = ref('');
const selectedMonth = ref('');
const reports = ref([]);
const loading = ref(false);
const error = ref('');

const totalOrders = computed(() => reports.value.length);

const totalRevenue = computed(() => {
  return reports.value.reduce((sum, report) => sum + parseFloat(report.total_amount || 0), 0);
});

function formatCurrency(value) {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
  }).format(value);
}

function formatDate(dateString) {
  if (!dateString) return '-';
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  });
}

async function fetchReports() {
  loading.value = true;
  error.value = '';

  try {
    const params = {};
    if (selectedYear.value) params.year = selectedYear.value;
    if (selectedMonth.value) params.month = selectedMonth.value;

    const query = new URLSearchParams(params).toString();
    const url = `${apiBase}/admin/reports${query ? '?' + query : ''}`;

    const data = await $fetch(url, {
      headers: {
        Authorization: `Bearer ${authStore.token}`,
      },
    });

    reports.value = Array.isArray(data) ? data : (data.data || []);
  } catch (err) {
    console.error('Failed to fetch reports:', err);
    error.value = err?.data?.message || err?.message || 'Failed to fetch reports. Please try again.';
    reports.value = [];
  } finally {
    loading.value = false;
  }
}

onMounted(() => {
  fetchReports();
});
</script>
