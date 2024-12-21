<script setup>
import { ref } from 'vue';
import { Inertia } from '@inertiajs/vue3';
import { defineProps } from 'vue';

const props = defineProps({
  company: Object
});

const form = ref({
  name: props.company.name,
  email: props.company.email,
  logo: null,
  website: props.company.website,
});

function submitForm() {
  Inertia.put(route('companies.update', props.company.id), form.value);
}
</script>

<template>
  <div class="container mx-auto">
    <h1 class="text-2xl font-bold mb-4">Edit Company</h1>

    <form @submit.prevent="submitForm" class="space-y-4">
      <div>
        <label for="name" class="block">Name</label>
        <input v-model="form.name" type="text" id="name" class="w-full border p-2 rounded" required />
      </div>

      <div>
        <label for="email" class="block">Email</label>
        <input v-model="form.email" type="email" id="email" class="w-full border p-2 rounded" />
      </div>

      <div>
        <label for="logo" class="block">Logo</label>
        <input v-model="form.logo" type="file" id="logo" class="w-full border p-2 rounded" />
      </div>

      <div>
        <label for="website" class="block">Website</label>
        <input v-model="form.website" type="url" id="website" class="w-full border p-2 rounded" />
      </div>

      <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Save Changes</button>
    </form>
  </div>
</template>
