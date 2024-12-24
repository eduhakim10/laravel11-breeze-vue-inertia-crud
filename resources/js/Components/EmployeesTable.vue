<template>
  <a-table
    :columns="columns"
    :data-source="employees"
    :pagination="pagination"
    :loading="loading"
    :row-key="record => record.id"
    @change="handleTableChange"
   
  >
   <!-- Action Column Slot -->
   <template #action="{ record }">
      <a-button type="primary" @click="$emit('edit-employee', record)">Edit</a-button>
      <a-button type="danger" @click="confirmDelete(record)">Delete</a-button>
    </template>
    <!-- Expanded Row Slot -->
    <template #expandedRowRender="{ record }">
      <div>
        <p><strong>Company Name:</strong> {{ record.company.name }}</p>
        <p><strong>Company Email:</strong> {{ record.company.email }}</p>
        <p><strong>Company Website:</strong> <a :href="record.company.website" target="_blank">{{ record.company.website }}</a></p>
      
      </div>
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
        { title: 'First Name', dataIndex: 'first_name' },
        { title: 'Last Name', dataIndex: 'last_name' },
        { title: 'Email', dataIndex: 'email' },
        { title: 'Phone', dataIndex: 'phone' },
        { title: 'Action', dataIndex: 'action', slots: { customRender: 'action' } },
      ],
      isEditModalVisible: false, // Controls visibility of the edit modal
      isDeleteModalVisible: false, // Controls visibility of the delete confirmation modal
      companyToDelete: null, // Store company data for deletion
      expandableConfig: {
        expandedRowRender: (record) => {
          return this.$slots.expandedRowRender({ record });
        },
      },
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
        await axios.delete(`/employees/${company.id}`);
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