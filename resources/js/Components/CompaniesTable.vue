<template>
  <a-table
    :columns="columns"
    :data-source="companies"
    :pagination="pagination"
    :loading="loading"
    @change="handleTableChange"
  >
  <template #logo="{ record }">
  <a :href="record.logo" target="_blank">
    <img :src="record.logo" alt="Logo" class="w-10 h-10 object-cover" />
  </a>
</template>
    <template #website="{ record }">
      <a :href="record.website" target="_blank">{{ record.website }}</a>
    </template>
    <template #action="{ record }">
      <a-button type="primary" @click="$emit('edit-company', record)">Edit</a-button>
      <a-button type="danger" @click="confirmDelete(record)">Delete</a-button>
    </template>
  </a-table>

  <!-- Delete Confirmation Modal -->
  <a-modal
    v-model:visible="isDeleteModalVisible"
    title="Confirm Deletion"
    @ok="performDelete"
    @cancel="cancelDelete"
  >
    <p>Are you sure you want to delete this company?</p>
  </a-modal>
</template>

<script>
import { ref, defineComponent } from 'vue';
import { Table, Button } from 'ant-design-vue';
import axios from 'axios';

export default defineComponent({
  name: 'CompaniesTable',
  components: {
    ATable: Table,
    AButton: Button,
  },
  props: {
    companies: Array, // Array of companies passed as a prop
    loading: Boolean, // Loading state to show a spinner
    pagination: Object, // Pagination object to control the current page and page size
  },
  data() {
    return {
      columns: [
        { title: 'Index', dataIndex: 'index' },
        { title: 'Name', dataIndex: 'name' },
        { title: 'Email', dataIndex: 'email' },
        { title: 'Logo', dataIndex: 'logo', slots: { customRender: 'logo' } },
        { title: 'Website', dataIndex: 'website', slots: { customRender: 'website' } },
        { title: 'Action', dataIndex: 'action', slots: { customRender: 'action' } },
      ],
      isEditModalVisible: false, // Controls visibility of the edit modal
      isDeleteModalVisible: false, // Controls visibility of the delete confirmation modal
      editFormData: {
        name: '',
        email: '',
        website: '',
      }, // Form data for the edit modal
      companyToDelete: null, // Store company data for deletion
    };
  },
  methods: {
    handleTableChange(pagination, filters, sorter) {
      // Handle pagination, sorting, etc. and emit event back to the parent component
      this.$emit('update-pagination', pagination);
    },
   
  
    confirmDelete(record) {
      this.companyToDelete = record;
      this.isDeleteModalVisible = true;
    },
  
    performDelete() {
      if (this.companyToDelete) {
        this.deleteCompany(this.companyToDelete);
      }
      this.isDeleteModalVisible = false;
      this.companyToDelete = null;
    },
    async deleteCompany(company) {

      try {
        await axios.delete(`/companies/${company.id}`);
        this.$emit('update-pagination'); // Notify the parent to refresh the data
      } catch (error) {
        console.error('Failed to delete company:', error);
      }
    },
    cancelDelete() {
      this.isDeleteModalVisible = false;
      this.companyToDelete = null;
    },
  },
});
</script>
