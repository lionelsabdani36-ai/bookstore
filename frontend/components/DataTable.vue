<template>
  <div class="overflow-x-auto bg-white rounded-xl shadow-sm border border-gray-100">
    <table class="min-w-full divide-y divide-gray-200">
      <thead class="bg-primary-50">
        <tr>
          <th v-for="col in columns" :key="col.key" class="px-6 py-3 text-left text-xs font-medium text-primary-700 uppercase tracking-wider">
            {{ col.label }}
          </th>
          <th v-if="actions" class="px-6 py-3 text-right text-xs font-medium text-primary-700 uppercase tracking-wider">
            Actions
          </th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200">
        <tr v-for="item in data" :key="item.id" class="hover:bg-gray-50">
          <td v-for="col in columns" :key="col.key" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
            <slot :name="col.key" :item="item">
              {{ item[col.key] }}
            </slot>
          </td>
          <td v-if="actions" class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
            <slot name="actions" :item="item"></slot>
          </td>
        </tr>
        <tr v-if="data.length === 0">
          <td :colspan="columns.length + (actions ? 1 : 0)" class="px-6 py-8 text-center text-gray-500">
            No data available.
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
defineProps({
  columns: {
    type: Array,
    required: true
  },
  data: {
    type: Array,
    required: true
  },
  actions: {
    type: Boolean,
    default: false
  }
});
</script>
