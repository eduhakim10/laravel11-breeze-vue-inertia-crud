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
const showBankPage = ref(false); // Toggle for bank section (if needed)

const { props } = usePage(); // Get page props
console.log(props.flash?.success || null);
const successMessage = props.flash?.success || null; // Access the success flash message

const fetchCompanies = async (paginationData) => {
  loading.value = true;

  try {
    const response = await axios.get('/companies-data', {
      params: {
        page: paginationData?.current || pagination.value.current,
        per_page: paginationData?.pageSize || pagination.value.pageSize,
      },
    });

    companies.value = response.data.companies;
    pagination.value.total = response.data.total;
    pagination.value.current = response.data.current_page;
  } catch (error) {
    console.error('Error fetching companies:', error);
  } finally {
    loading.value = false;
  }
};
const closeMessage = () => {
  successMessage.value = null; // Clear the success message
};

const editCompany = (company) => {
  console.log('Editing company:', company);
  // Implement your edit logic here, e.g., open a modal or navigate to an edit page
};

const deleteCompany = (id) => {
  companies.value = companies.value.filter(company => company.id !== id);
  // Call API to delete company (if needed)
};

onMounted(() => {
  fetchCompanies(); // Fetch companies on initial mount
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
        @update-pagination="fetchCompanies"
        @edit-company="editCompany"
        @delete-company="deleteCompany"
      />

      <!-- Bank Section (if needed) -->
      <div v-if="showBankPage" class="mt-8 p-6 bg-gray-100 rounded-lg">
        <h2 class="text-xl font-semibold text-gray-700">Bank Information</h2>
        <p class="text-gray-600">This is the bank page content.</p>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

