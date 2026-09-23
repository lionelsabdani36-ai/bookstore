<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <h2 class="text-2xl font-bold text-gray-800">Books</h2>
      <button @click="openCreateModal" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
        + Add Book
      </button>
    </div>

    <!-- Error State -->
    <div v-if="booksStore.error" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded mb-4">
      <p>{{ booksStore.error }}</p>
    </div>

    <!-- Loading State -->
    <div v-if="booksStore.loading && booksStore.books.length === 0" class="flex justify-center items-center py-10">
      <p class="text-gray-500">Loading books...</p>
    </div>

    <!-- Books List -->
    <div v-else class="bg-white rounded-xl shadow overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cover</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prices (Cost / Sell)</th>
            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="book in booksStore.books" :key="book.id">
            <td class="px-6 py-4 whitespace-nowrap">
              <img v-if="book.cover_image" :src="`http://localhost:8000/storage/${book.cover_image}`" class="h-12 w-12 object-cover rounded-md border" />
              <div v-else class="h-12 w-12 bg-gray-200 rounded-md flex items-center justify-center text-xs text-gray-500">No Img</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ book.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ book.category?.name || '-' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ book.stock }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${{ book.cost_price }} / ${{ book.sell_price }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button @click="openEditModal(book)" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
              <button @click="deleteBook(book.id)" class="text-red-600 hover:text-red-900">Delete</button>
            </td>
          </tr>
          <tr v-if="booksStore.books.length === 0">
            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No books found.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal for Create/Edit -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">{{ isEditing ? 'Edit Book' : 'Create New Book' }}</h3>
        
        <form @submit.prevent="submitForm">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="col-span-2 md:col-span-1">
              <label class="block text-sm font-medium text-gray-700 mb-1">Book Name</label>
              <input v-model="form.name" type="text" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="col-span-2 md:col-span-1">
              <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
              <select v-model="form.category_id" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                <option value="" disabled>Select category</option>
                <option v-for="cat in categoryStore.categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
              </select>
            </div>

            <div class="col-span-2 md:col-span-1">
              <label class="block text-sm font-medium text-gray-700 mb-1">Publish Date</label>
              <input v-model="form.publish_date" type="date" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="col-span-2 md:col-span-1">
              <label class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
              <input v-model="form.stock" type="number" min="0" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="col-span-2 md:col-span-1">
              <label class="block text-sm font-medium text-gray-700 mb-1">Cost Price ($)</label>
              <input v-model="form.cost_price" type="number" step="0.01" min="0" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="col-span-2 md:col-span-1">
              <label class="block text-sm font-medium text-gray-700 mb-1">Sell Price ($)</label>
              <input v-model="form.sell_price" type="number" step="0.01" min="0" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
              <textarea v-model="form.description" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500"></textarea>
            </div>

            <div class="col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">Cover Image (Optional)</label>
              <input type="file" @change="handleFileChange" accept="image/*" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
            </div>
          </div>
          
          <div class="flex justify-end space-x-3 mt-6">
            <button type="button" @click="closeModal" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
              Cancel
            </button>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50" :disabled="booksStore.loading">
              {{ booksStore.loading ? 'Saving...' : (isEditing ? 'Update Book' : 'Create Book') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue'
import { useBooksStore } from '~/stores/books'
import { useCategoryStore } from '~/stores/category'

definePageMeta({ 
  layout: 'admin', 
  middleware: ['admin'] 
})

const booksStore = useBooksStore()
const categoryStore = useCategoryStore()

const showModal = ref(false)
const isEditing = ref(false)
const editingId = ref(null)

const form = reactive({
  name: '',
  category_id: '',
  publish_date: '',
  stock: 0,
  cost_price: 0,
  sell_price: 0,
  description: ''
})
let selectedFile = null

onMounted(() => {
  booksStore.fetchBooks()
  categoryStore.fetchCategories()
})

const resetForm = () => {
  form.name = ''
  form.category_id = ''
  form.publish_date = ''
  form.stock = 0
  form.cost_price = 0
  form.sell_price = 0
  form.description = ''
  selectedFile = null
  isEditing.value = false
  editingId.value = null
}

const openCreateModal = () => {
  resetForm()
  showModal.value = true
}

const openEditModal = (book) => {
  resetForm()
  isEditing.value = true
  editingId.value = book.id
  
  form.name = book.name
  form.category_id = book.category_id
  form.publish_date = book.publish_date ? book.publish_date.split(' ')[0] : '' // basic date formatting
  form.stock = book.stock
  form.cost_price = book.cost_price
  form.sell_price = book.sell_price
  form.description = book.description || ''
  
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}

const handleFileChange = (e) => {
  if (e.target.files.length > 0) {
    selectedFile = e.target.files[0]
  } else {
    selectedFile = null
  }
}

const submitForm = async () => {
  const formData = new FormData()
  formData.append('name', form.name)
  formData.append('category_id', form.category_id)
  if (form.publish_date) formData.append('publish_date', form.publish_date)
  formData.append('stock', form.stock)
  formData.append('cost_price', form.cost_price)
  formData.append('sell_price', form.sell_price)
  if (form.description) formData.append('description', form.description)
  if (selectedFile) formData.append('cover_image', selectedFile)

  try {
    if (isEditing.value) {
      await booksStore.updateBook(editingId.value, formData)
    } else {
      await booksStore.createBook(formData)
    }
    closeModal()
  } catch (e) {
    // Handled in store
  }
}

const deleteBook = async (id) => {
  if (confirm('Are you sure you want to delete this book?')) {
    await booksStore.deleteBook(id)
  }
}
</script>
