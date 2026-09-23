<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <h2 class="text-2xl font-bold text-gray-800">Categories</h2>
      <button @click="openCreateModal" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
        + Add Category
      </button>
    </div>

    <!-- Error State -->
    <div v-if="categoryStore.error" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded mb-4">
      <p>{{ categoryStore.error }}</p>
    </div>

    <!-- Loading State -->
    <div v-if="categoryStore.loading && categoryStore.categories.length === 0" class="flex justify-center items-center py-10">
      <p class="text-gray-500">Loading categories...</p>
    </div>

    <!-- Categories List -->
    <div v-else class="bg-white rounded-xl shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Stock</th>
            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="category in categoryStore.categories" :key="category.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ category.id }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
              <span v-if="editingId !== category.id">{{ category.name }}</span>
              <input 
                v-else 
                v-model="editForm.name" 
                type="text" 
                class="border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                @keyup.enter="saveEdit(category.id)"
              >
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ category.books_sum_stock || 0 }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <div v-if="editingId !== category.id">
                <button @click="startEdit(category)" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                <button @click="deleteCategory(category.id)" class="text-red-600 hover:text-red-900">Delete</button>
              </div>
              <div v-else>
                <button @click="saveEdit(category.id)" class="text-green-600 hover:text-green-900 mr-3" :disabled="categoryStore.loading">Save</button>
                <button @click="cancelEdit" class="text-gray-600 hover:text-gray-900">Cancel</button>
              </div>
            </td>
          </tr>
          <tr v-if="categoryStore.categories.length === 0">
            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">No categories found.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Create Modal (Simple version using v-if) -->
    <div v-if="showCreateModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Create New Category</h3>
        
        <form @submit.prevent="submitCreate">
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Category Name</label>
            <input 
              v-model="createForm.name" 
              type="text" 
              required
              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="e.g. Fiction"
            >
          </div>
          
          <div class="flex justify-end space-x-3">
            <button type="button" @click="closeCreateModal" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
              Cancel
            </button>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50" :disabled="categoryStore.loading || !createForm.name">
              {{ categoryStore.loading ? 'Saving...' : 'Create' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue'
import { useCategoryStore } from '~/stores/category'

definePageMeta({ 
  layout: 'admin', 
  middleware: ['admin'] 
})

const categoryStore = useCategoryStore()

// Create Form State
const showCreateModal = ref(false)
const createForm = reactive({ name: '' })

// Edit Form State
const editingId = ref(null)
const editForm = reactive({ name: '' })

onMounted(() => {
  categoryStore.fetchCategories()
})

const openCreateModal = () => {
  createForm.name = ''
  showCreateModal.value = true
}

const closeCreateModal = () => {
  showCreateModal.value = false
}

const submitCreate = async () => {
  try {
    await categoryStore.createCategory(createForm.name)
    closeCreateModal()
  } catch (e) {
    // Error handled in store
  }
}

const startEdit = (category) => {
  editingId.value = category.id
  editForm.name = category.name
}

const cancelEdit = () => {
  editingId.value = null
  editForm.name = ''
}

const saveEdit = async (id) => {
  if (!editForm.name) return
  try {
    await categoryStore.updateCategory(id, editForm.name)
    editingId.value = null
  } catch (e) {
    // Error handled in store
  }
}

const deleteCategory = async (id) => {
  if (confirm('Are you sure you want to delete this category?')) {
    await categoryStore.deleteCategory(id)
  }
}
</script>
