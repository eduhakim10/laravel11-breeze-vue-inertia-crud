<!-- resources/js/Pages/Employees/Index.vue -->
<template>
  <div>
    <h1>Employees List</h1>
    <inertia-link :href="route('employees.create')">Create New Employee</inertia-link>
    <ul>
      <li v-for="employee in employees" :key="employee.id">
        {{ employee.first_name }} {{ employee.last_name }} - 
        <inertia-link :href="route('employees.show', employee.id)">View</inertia-link>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { defineProps } from 'vue';

// Receive employees as props from the controller
const props = defineProps({
  employees: Array
});
</script>
