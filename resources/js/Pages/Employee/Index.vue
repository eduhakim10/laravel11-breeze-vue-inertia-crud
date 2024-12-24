<script setup>
import { ref, onMounted } from 'vue';

import EmployeesTable from '@/Components/EmployeesTable.vue'; // Import the EmployeesTable component
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { usePage } from '@inertiajs/vue3'; 
import { Head, Link } from '@inertiajs/vue3'; // For creating a link to the "Create New Employee" page
import axios from 'axios'; // Import axios for making HTTP requests

// Define data and properties
const employees = ref([]); // Data to store the list of Employees

const loading = ref(false); // Loading state for the table
const pagination = ref({ current: 1, pageSize: 10, total: 0 }); // Pagination settings
const formErrors = ref({}); 
const isEditModalVisible = ref(false);
const editFormData = ref({
  id: null,
  first_name: '',
  last_name: '',
  email: '',
  phone: '',
  company_id: '',
});
const { props } = usePage(); // Get page props
const companies = props.companies || []; // Fetch companies from props
const successMessage = props.flash?.success || null; // Access the success flash message

// Handle edit button click
const handleEditEmployee = (record) => {

  editFormData.value = { ...record,
  company_id: record.company?.id || null
  }; // Copy data into the form
  isEditModalVisible.value = true; // Show the modal

};
// Close the edit modal
const cancelEdit = () => {
  isEditModalVisible.value = false;
};
const fetchEmployees = async (paginationData = pagination.value) => {
  loading.value = true;

  try {
    const response = await axios.get('/employees-data', {
      params: {
        page: paginationData.current,
        per_page: paginationData.pageSize,
      },
    });

    employees.value = response.data.data.map((comp, index) => ({
      ...comp,
      index: (paginationData.current - 1) * paginationData.pageSize + index + 1,
    }));

    pagination.value.total = response.data.meta.total;
    pagination.value.current = response.data.meta.current_page;
  } catch (error) {
    console.error('Error fetching employees:', error);
  } finally {
    loading.value = false;
  }
};
const saveEdit = async () => {

// Append other fields to the FormData object
    if (!editFormData.value) {
       
        return; // Stop further execution if there's an issue
    }
    console.log("Edit Form Data:", editFormData.value);
    const formData = {
    first_name: editFormData.value.first_name,
    last_name: editFormData.value.last_name,
    email: editFormData.value.email,
    phone: editFormData.value.phone,
    company_id: editFormData.value.company_id,
  };


  // Send the PUT request to update the company
  try {
      const response = await axios.put(`/employees/${editFormData.value.id}`, formData);

      successMessage.value = response.data.message;
      console.log('Backend response:', response);
      console.log(response.data.message);
      // Close the modal after successful update
      isEditModalVisible.value = false;
      fetchEmployees();
  } catch (error) {
    
        if (error.response && error.response.data && error.response.data.errors) {
          formErrors.value = error.response.data.errors;
        } else {
          console.error('Unexpected error');
          isEditModalVisible.value = false;
          fetchEmployees();
        }
  }
 
};
const closeMessage = () => {
  successMessage.value = null; // Clear the success message
};


onMounted(() => {
  fetchEmployees(); // Fetch employees on initial mount
});
</script>
<template>
  <Head title="Employee" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">Employees</h2>
    </template>

    <div class="px-6 py-8">
      <!-- Employees Title Section -->
      <div class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-800">Employees List</h1>
        <Link
          :href="route('employees.create')"
          class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-300"
        >
          Create New Employee
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


      <!-- Employees Table -->
      <EmployeesTable
        :employees="employees"
        :loading="loading"
        :pagination="pagination"
        @update-pagination="fetchEmployees"
        @edit-employee="handleEditEmployee"
      
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
            label="First Name" 
            :rules="[{ required: true, message: 'First Name is required' }]" 
            :help="formErrors.first_name"
          >
            <a-input v-model:value="editFormData.first_name" />
          </a-form-item>
          <a-form-item 
            label="Last Name" 
            :rules="[{ required: true, message: 'Last Name is required' }]" 
            :help="formErrors.last_name"
          >
            <a-input v-model:value="editFormData.last_name" />
          </a-form-item>
          
          <a-form-item label="Email" :help="formErrors.email">
            <a-input v-model:value="editFormData.email" />
          </a-form-item>
          
          <a-form-item label="Phone" :help="formErrors.phone">
            <a-input v-model:value="editFormData.phone" />
          </a-form-item>

         <!-- Company Selection -->
        <a-form-item
          label="Company"
          :rules="[{ required: true, message: 'Please select a company' }]"
          :help="formErrors.company_id"
        >
          <a-select
            v-model:value="editFormData.company_id"
            placeholder="Select a company"
          >
            <a-select-option
              v-for="company in companies"
              :key="company.id"
              :value="company.id"
            >
              {{ company.name }}
            </a-select-option>
          </a-select>
        </a-form-item>
        </a-form>
      </a-modal>

    </div>
  </AuthenticatedLayout>
</template>
