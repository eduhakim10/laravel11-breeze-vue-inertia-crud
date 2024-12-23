<script setup>
import { ref } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { Inertia } from '@inertiajs/inertia'; // For submitting form data
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';



// Form fields and file input state
const employeeFirstname = ref('');
const employeeLastname = ref('');
const employeePhone = ref('');
const employeeEmail = ref('');
const company_id = ref(null);
const props = defineProps({
  companies: Array,
});
const { errors } = usePage().props;
  console.log(errors); 
  
// Reactive data for company selection
const companyData = ref({
  company_id: null, // Initialize with null
});

// Submit the form with FormData (including the file)
const submitForm = () => {
  const formData = new FormData();
  formData.append('first_name', employeeFirstname.value);
  formData.append('last_name', employeeLastname.value);
  formData.append('company_id', company_id.value);
  formData.append('phone', employeePhone.value);
   formData.append('email', employeeEmail.value);

  // Send data using Inertia.js
  Inertia.post(route('employees.store'), formData, {
      onSuccess: () => {
        // Redirect to the index route after success
      //  Inertia.visit(route('employees.index'));
      console.log('Employee created successfully!');
      },
      onError: (errors) => {
        alert('Error creating company');
        console.error(errors);
      },
    });
};
</script>

<template>
  <Head title="Companies" />

  <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Create Employee
            </h2>
        </template>
  <div class="max-w-lg mx-auto p-6 bg-white shadow-md rounded-md form-companies">
    <h1 class="text-2xl font-semibold mb-4">Create Employee</h1>

    <!-- Form to create a employee -->
    <form @submit.prevent="submitForm">
      <div v-if="errors && Object.keys(errors).length" class="text-red-500 text-sm">
  <ul>
    <li v-for="(error, field) in errors" :key="field">
      {{ field }}: {{ error }}
    </li>
  </ul>
</div>



      <!-- Employee Name -->
      <div class="mb-4">
        <label for="name" class="block text-sm font-medium text-gray-700">First Name</label>
        <input
          type="text"
          id="name"
          v-model="employeeFirstname"
          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
          required
        />
       
      </div>

       <div class="mb-4">
        <label for="name" class="block text-sm font-medium text-gray-700">Last Name</label>
        <input
          type="text"
          id="name"
          v-model="employeeLastname"
          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
          required
        />
      
      </div>

      <!-- Employee Email -->
      <div class="mb-4">
        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
        <input
          type="email"
          id="email"
          v-model="employeeEmail"
          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
        />
       
      </div>

      <!-- Employee Phone -->
      <div class="mb-4">
        <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
        <input
          type="text"
          id="phone"
          v-model="employeePhone"
          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
        />
        
      </div>

      <!-- Employee Company -->
      <a-form-item label="Company" :rules="[{ required: true, message: 'Company is required' }]">
        <a-select v-model:value="company_id" placeholder="Select Company">
          <a-select-option v-for="company in companies" :key="company.id" :value="company.id">
            {{ company.name }}
          </a-select-option>
        </a-select>

      </a-form-item>

      <!-- Submit Button -->
             <div class="flex justify-between">
          <Link
            :href="route('companies.index')"
            class="py-2 px-4 bg-gray-600 text-white font-semibold rounded-md hover:bg-gray-700"
          >
            Back
          </Link>

          <button
            type="submit"
            class="py-2 px-4 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700"
          >
            Create Employee
          </button>
        </div>

    </form>
  </div>
  </AuthenticatedLayout>
</template>

<style scoped>
/* You can add custom styling if needed */

.form-companies{
  margin-top:15px;
}
</style>
