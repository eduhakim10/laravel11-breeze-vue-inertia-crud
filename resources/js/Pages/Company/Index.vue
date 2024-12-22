<script setup>
import { ref, onMounted } from 'vue';
import CompaniesTable from '@/Components/CompaniesTable.vue'; // Import the CompaniesTable component
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { usePage } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3'; // For creating a link to the "Create New Employee" page
import axios from 'axios'; // Import axios for making HTTP requests

// Define data and properties
const companies = ref([]); // Data to store the list of companies
const loading = ref(false); // Loading state for the table
const pagination = ref({ current: 1, pageSize: 10, total: 0 }); // Pagination settings
const successMessage = ref(null); // Success message from flash
const generalErrorMessage = ref(null); // General backend error message
const formErrors = ref({}); // Object to hold field-specific errors
const errorMessage = ref(null); 
const { props } = usePage(); // Get page props
successMessage.value = props.flash?.success || null; // Access the success flash message

const closeMessage = () => {
  successMessage.value = null; // Clear the success message
};


// Modal management
const isEditModalVisible = ref(false);
const editFormData = ref({
  id: null,
  name: '',
  email: '',
  website: '',
});

// Fetch companies data from the server
const fetchCompanies = async (paginationData = pagination.value) => {
  loading.value = true;

  try {
    const response = await axios.get('/companies-data', {
      params: {
        page: paginationData.current,
        per_page: paginationData.pageSize,
      },
    });

    companies.value = response.data.data.map((comp, index) => ({
      ...comp,
      index: (paginationData.current - 1) * paginationData.pageSize + index + 1,
    }));

    pagination.value.total = response.data.meta.total;
    pagination.value.current = response.data.meta.current_page;
  } catch (error) {
    console.error('Error fetching companies:', error);
  } finally {
    loading.value = false;
  }
};
// Handle edit button click
const handleEditCompany = (record) => {

  editFormData.value = { ...record }; // Copy data into the form
  isEditModalVisible.value = true; // Show the modal
  console.log(isEditModalVisible)

};

// Close the edit modal
const cancelEdit = () => {
  isEditModalVisible.value = false;
};
const handleFileChange = (event) => {
  const file = event.target.files[0];
  if (file) {
    editFormData.value.logo = file;
    console.log('File selected:', file);  // Log the file object to confirm it
  } else {
    console.log('No file selected');
  }
};
const saveEdit = async () => {

      const formData = new FormData();

// Append other fields to the FormData object
        formData.append('_method', 'PUT'); // Simulate a PUT request
        formData.append('name', editFormData.value.name);
        formData.append('email', editFormData.value.email);
        formData.append('website', editFormData.value.website);

        // Append the logo file if available
        if (editFormData.value.logo && editFormData.value.logo instanceof File) {
          formData.append('logo', editFormData.value.logo);
        } else {
          console.log('No valid logo file found');
        }


        // Send the PUT request to update the company
        try {
            const response = await axios.post(`/companies/${editFormData.value.id}`, formData, {
                  headers: {
                  'Content-Type': 'multipart/form-data',
              },
            });

            successMessage.value = response.data.message;

            // Close the modal after successful update
            isEditModalVisible.value = false;
            fetchCompanies();
        } catch (error) {
          
            if (error.response && error.response.data && error.response.data.errors) {
                formErrors.value = error.response.data.errors;
              } else {
                console.error('Unexpected error:', error);
              }
        }
       
};
onMounted(() => {
  fetchCompanies(); // Fetch companies when the component is mounted
});
</script>

<template>
  <Head title="Companies" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">Companies</h2>
    </template>

    <div class="px-6 py-8">
      <!-- Companies Title Section -->
      <div class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-800">Companies List</h1>
        <Link
          :href="route('companies.create')"
          class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-300"
        >
          Create New Company
        </Link>
      </div>

      <!-- Success Message Section -->
      <div v-if="successMessage" class="alert alert-success bg-green-500 text-white p-4 rounded-lg shadow-md mb-6 flex items-center justify-between">
        <span>{{ successMessage }}</span>
        <button 
          @click="closeMessage"
          class="text-white bg-transparent hover:bg-green-600 focus:outline-none font-bold rounded-full w-6 h-6 flex justify-center items-center"
        >
          ✕
        </button>
      </div>

      <!-- Companies Table -->
      <CompaniesTable
        :companies="companies"
        :loading="loading"
        :pagination="pagination"
        @edit-company="handleEditCompany"
        @update-pagination="fetchCompanies"
        
      />
       <!-- Edit Modal -->
       <a-modal
        v-model:visible="isEditModalVisible"
        title="Edit Company"
        @ok="saveEdit"
        @cancel="cancelEdit"
      >
        <a-form :model="editFormData" ref="formRef">
          <a-form-item 
            label="Name" 
            :rules="[{ required: true, message: 'Name is required' }]" 
            :help="formErrors.name"
          >
            <a-input v-model:value="editFormData.name" />
          </a-form-item>
          
          <a-form-item label="Email" :help="formErrors.email">
            <a-input v-model:value="editFormData.email" />
          </a-form-item>
          
          <a-form-item label="Website" :help="formErrors.website">
            <a-input v-model:value="editFormData.website" />
          </a-form-item>

          <!-- Logo Upload -->
          <a-form-item label="Logo" :help="formErrors.logo">
            <a-input type="file" @change="handleFileChange" />
          </a-form-item>
        </a-form>
      </a-modal>

    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
.company-datatable {
  padding: 20px;
}
.a-modal {
  z-index: 9999 !important;
  position: fixed !important;
}
.ant-form-item-explain {
  color: red !important;
}
</style>
