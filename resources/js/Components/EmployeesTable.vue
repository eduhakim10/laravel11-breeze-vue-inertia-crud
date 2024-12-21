<template>
  <a-table
    :columns="columns"
    :data-source="employees"
    :pagination="pagination"
    :loading="loading"
    @change="handleTableChange"
  >
    <template #logo="{ record }">
      <img :src="record.logo" alt="Logo" class="w-10 h-10 object-cover" />
    </template>
    <template #website="{ record }">
      <a :href="record.website" target="_blank">{{ record.website }}</a>
    </template>
    <template #action="{ record }">
      <a-button @click="editemployee(record)">Edit</a-button>
      <a-button type="danger" @click="deleteemployee(record)">Delete</a-button>
    </template>
  </a-table>
</template>

<script>
import { ref, defineComponent } from 'vue';
import { Table, Button } from 'ant-design-vue';

export default defineComponent({
  name: 'EmployeeTable',
  components: {
    ATable: Table,
    AButton: Button,
  },
  props: {
    employees: Array, // Array of employees passed as a prop
    loading: Boolean, // Loading state to show a spinner
    pagination: Object, // Pagination object to control the current page and page size
  },
  data() {
    return {
      columns: [
        { title: 'Index', dataIndex: 'index' },
        { title: 'Name', dataIndex: 'name' },
        { title: 'Email', dataIndex: 'email' },
        { title: 'Phone', dataIndex: 'phone', scopedSlots: { customRender: 'phone' } },
        { title: 'Action', dataIndex: 'action', scopedSlots: { customRender: 'action' } },
      ],
    };
  },
  methods: {
    handleTableChange(pagination, filters, sorter) {
      // Handle pagination, sorting, etc. and emit event back to the parent component
      this.$emit('update-pagination', pagination);
    },
    editemployee(record) {
      // Handle edit functionality here
    },
    deleteemployee(record) {
      // Handle delete functionality here
    },
  },
});
</script>