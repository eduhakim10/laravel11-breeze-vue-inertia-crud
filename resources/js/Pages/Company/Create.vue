<script setup>
import { ref } from 'vue';
import { Inertia } from '@inertiajs/inertia'; // For submitting form data
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// Form fields and file input state
const companyName = ref('');
const companyEmail = ref('');
const companyWebsite = ref('');
const companyLogo = ref(null);

// Handle file input change (for logo upload)
const handleFileChange = (event) => {
  companyLogo.value = event.target.files[0]; // Store the selected file
};

// Submit the form with FormData (including the file)
const submitForm = () => {
  const formData = new FormData();
  formData.append('name', companyName.value);
  formData.append('email', companyEmail.value);
  formData.append('website', companyWebsite.value);


 if (companyLogo.value) {
    formData.append('logo', companyLogo.value);
  }
  // Log the name and email to verify the values
  

  // Send data using Inertia.js
  Inertia.post('/companies', formData, {
      onSuccess: () => {
        // Redirect to the index route after success
        Inertia.visit(route('companies.index'));
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
                Create Company
            </h2>
        </template>
  <div class="max-w-lg mx-auto p-6 bg-white shadow-md rounded-md form-companies">
    <h1 class="text-2xl font-semibold mb-4">Create Company</h1>

    <!-- Form to create a company -->
    <form @submit.prevent="submitForm">
      <!-- Company Name -->
      <div class="mb-4">
        <label for="name" class="block text-sm font-medium text-gray-700">Company Name</label>
        <input
          type="text"
          id="name"
          v-model="companyName"
          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
          required
        />
      </div>

      <!-- Company Email -->
      <div class="mb-4">
        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
        <input
          type="email"
          id="email"
          v-model="companyEmail"
          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
        />
      </div>

      <!-- Company Website -->
      <div class="mb-4">
        <label for="website" class="block text-sm font-medium text-gray-700">Website</label>
        <input
          type="url"
          id="website"
          v-model="companyWebsite"
          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
        />
      </div>

      <!-- Company Logo -->
      <div class="mb-4">
        <label for="logo" class="block text-sm font-medium text-gray-700">Upload Logo</label>
        <input
          type="file"
          id="logo"
          @change="handleFileChange"
          class="mt-1 block w-full"
        />
        <!-- Show selected file name -->
        <p v-if="companyLogo" class="mt-2 text-sm text-gray-600">{{ companyLogo.name }}</p>
      </div>

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
            Create Company
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