<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <h2 class="text-2xl font-bold text-gray-800">Users</h2>
      <button @click="openCreateModal" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
        + Add User
      </button>
    </div>

    <!-- Error State -->
    <div v-if="usersStore.error" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded mb-4">
      <p>{{ usersStore.error }}</p>
    </div>

    <!-- Loading State -->
    <div v-if="usersStore.loading && usersStore.users.length === 0" class="flex justify-center items-center py-10">
      <p class="text-gray-500">Loading users...</p>
    </div>

    <!-- Tabs -->
    <div class="border-b border-gray-200">
      <nav class="-mb-px flex space-x-8">
        <button 
          @click="activeTab = 'customer'"
          :class="[activeTab === 'customer' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300', 'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm']"
        >
          Customers
        </button>
        <button 
          @click="activeTab = 'admin'"
          :class="[activeTab === 'admin' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300', 'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm']"
        >
          Admins
        </button>
      </nav>
    </div>

    <!-- Users List -->
    <div v-if="!usersStore.loading || usersStore.users.length > 0" class="bg-white rounded-xl shadow overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Photo</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name / Username</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="user in filteredUsers" :key="user.id">
            <td class="px-6 py-4 whitespace-nowrap">
              <img v-if="user.photo" :src="`http://localhost:8000/storage/${user.photo}`" class="h-10 w-10 rounded-full object-cover border" />
              <div v-else class="h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center text-xs text-gray-500">No Img</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
              <div class="text-sm text-gray-500">@{{ user.username }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-900">{{ user.email }}</div>
              <div class="text-sm text-gray-500">{{ user.phone }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" :class="user.role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800'">
                {{ user.role }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button @click="openEditModal(user)" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
              <button @click="deleteUser(user.id)" class="text-red-600 hover:text-red-900">Delete</button>
            </td>
          </tr>
          <tr v-if="filteredUsers.length === 0">
            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No {{ activeTab }}s found.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal for Create/Edit -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">{{ isEditing ? 'Edit User' : 'Create New User' }}</h3>
        
        <form @submit.prevent="submitForm">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="col-span-2 md:col-span-1">
              <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
              <input v-model="form.name" type="text" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="col-span-2 md:col-span-1">
              <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
              <input v-model="form.username" type="text" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="col-span-2 md:col-span-1">
              <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
              <input v-model="form.email" type="email" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="col-span-2 md:col-span-1">
              <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
              <input v-model="form.phone" type="text" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="col-span-2 md:col-span-1">
              <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
              <select v-model="form.role" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                <option value="customer">Customer</option>
                <option value="admin">Admin</option>
              </select>
            </div>

            <div class="col-span-2 md:col-span-1">
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Password <span v-if="isEditing" class="text-gray-400 text-xs font-normal">(Leave empty to keep current)</span>
              </label>
              <input v-model="form.password" type="password" :required="!isEditing" minlength="8" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">Photo (Optional)</label>
              <input type="file" @change="handleFileChange" accept="image/*" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
            </div>
          </div>
          
          <div class="flex justify-end space-x-3 mt-6">
            <button type="button" @click="closeModal" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
              Cancel
            </button>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50" :disabled="usersStore.loading">
              {{ usersStore.loading ? 'Saving...' : (isEditing ? 'Update User' : 'Create User') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive, computed } from 'vue'
import { useUsersStore } from '~/stores/users'
import { useAuthStore } from '~/stores/auth'

definePageMeta({ 
  layout: 'admin', 
  middleware: ['admin'] 
})

const usersStore = useUsersStore()
const authStore = useAuthStore()

const showModal = ref(false)
const isEditing = ref(false)
const editingId = ref(null)

const activeTab = ref('customer')

const filteredUsers = computed(() => {
  return usersStore.users.filter(u => u.role === activeTab.value)
})

const form = reactive({
  name: '',
  username: '',
  email: '',
  phone: '',
  role: 'customer',
  password: ''
})
let selectedFile = null

onMounted(() => {
  usersStore.fetchUsers()
})

const resetForm = () => {
  form.name = ''
  form.username = ''
  form.email = ''
  form.phone = ''
  form.role = 'customer'
  form.password = ''
  selectedFile = null
  isEditing.value = false
  editingId.value = null
}

const openCreateModal = () => {
  resetForm()
  showModal.value = true
}

const openEditModal = (user) => {
  resetForm()
  isEditing.value = true
  editingId.value = user.id
  
  form.name = user.name
  form.username = user.username
  form.email = user.email
  form.phone = user.phone
  form.role = user.role
  form.password = '' // Explicitly leave empty
  
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
  formData.append('username', form.username)
  formData.append('email', form.email)
  formData.append('phone', form.phone)
  formData.append('role', form.role)
  
  if (form.password) {
    formData.append('password', form.password)
  }
  
  if (selectedFile) {
    formData.append('photo', selectedFile)
  }

  try {
    if (isEditing.value) {
      await usersStore.updateUser(editingId.value, formData)
      
      // If updating our own profile, fetch user to update layout photo immediately
      if (authStore.user && editingId.value === authStore.user.id) {
        await authStore.fetchUser()
      }
    } else {
      await usersStore.createUser(formData)
    }
    closeModal()
  } catch (e) {
    // Handled in store
  }
}

const deleteUser = async (id) => {
  if (confirm('Are you sure you want to delete this user?')) {
    await usersStore.deleteUser(id)
  }
}
</script>
