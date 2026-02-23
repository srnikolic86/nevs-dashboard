<template>
  <table class="nevs-table" :style="{'width': tableWidthComputed, 'height': tableHeightComputed}">
    <tbody>
    <tr class="nevs-table-header">
      <td :style="{'width': fieldWidthComputed(field)}" :class="{'nevs-table-sortable-header': checkSortable(field)}"
          @click="toggleSort(field)" v-for="(field) in fields" :key="field.name">
            <span class="nevs-table-sort-arrow" v-show="sort.field === field.name && !sort.descending">
              <i class="fa-solid fa-arrow-down-short-wide"></i>
            </span>
        <span class="nevs-table-sort-arrow" v-show="sort.field === field.name && sort.descending">
              <i class="fa-solid fa-arrow-down-wide-short"></i>
            </span>
        {{ field.label }}
      </td>
    </tr>
    <slot></slot>
    </tbody>
  </table>
  <div class="nevs-table-footer" :style="{'width': tableWidthComputed }">
    <NevsNumberField :width="'70px'" :thousand-separator="''" :decimal-places="0"
                     v-model="rowsPerPage"></NevsNumberField>
    <span class="nevs-table-footer-label">
        {{ $LANG.Get('pagination.resultsPerPage') }},
        {{ $LANG.Get('pagination.page') }}
      </span>
    <span class="nevs-table-pagination-arrow" @click="modifyCurrentPage(-1)" v-show="currentPage > 1">
            <i class="fa-solid fa-caret-left"></i>
          </span>
    <NevsNumberField :width="'60px'" :thousand-separator="''" :decimal-places="0"
                     v-model="currentPage"></NevsNumberField>
    <span class="nevs-table-pagination-arrow" @click="modifyCurrentPage(1)" v-show="currentPage < totalPages">
            <i class="fa-solid fa-caret-right"></i>
          </span>
    <span class="nevs-table-footer-label">
        {{ $LANG.Get('pagination.of') }}
        {{ totalPages }}
      </span>
  </div>
</template>

<script>

import NevsNumberField from "@/components/nevs/NevsNumberField";

export default {
  name: "NevsTable",
  components: {
    NevsNumberField
  },
  props: {
    fields: Array,
    totalRecords: Number,
    defaultSort: Object,
    filters: Array,
    defaultRowsPerPage: Number,
    width: String,
    height: String
  },
  emits: [
    'reload'
  ],
  data() {
    return {
      currentPage: 1,
      rowsPerPage: 20,
      sort: {
        field: '',
        descending: false
      },
      filtersData: {},
      tableWidthComputed: 'auto',
      tableHeightComputed: 'auto'
    }
  },
  computed: {
    totalPages() {
      if (this.rowsPerPage === 0) return 0;
      return Math.ceil(this.totalRecords / this.rowsPerPage);
    }
  },
  watch: {
    currentPage() {
      this.reload();
    },
    rowsPerPage() {
      this.reload();
    },
    height() {
      if (this.height !== undefined) {
        this.tableHeightComputed = this.height;
      }
    }
  },
  methods: {
    fieldWidthComputed(field) {
      if (field.width !== undefined) {
        return field.width;
      }
      return 'auto';
    },
    checkSortable(field) {
      return field.sortable !== false;
    },
    modifyCurrentPage(modifier) {
      this.currentPage += modifier;
    },
    toggleSort(field) {
      if (!this.checkSortable(field)) return;
      if (this.sort.field !== field.name) {
        this.sort.field = field.name;
        this.sort.descending = false;
      } else {
        this.sort.descending = !this.sort.descending;
      }
      this.reload();
    },
    reload() {
      let payload = {
        sort: this.sort,
        filters: this.filtersData,
        currentPage: this.currentPage,
        rowsPerPage: this.rowsPerPage
      };
      this.$emit('reload', payload);
    }
  },
  mounted() {
    if (this.defaultRowsPerPage !== undefined) {
      this.rowsPerPage = this.defaultRowsPerPage;
    }
    if (this.defaultSort !== undefined) {
      this.sort = this.defaultSort;
    } else {
      if (this.fields.length > 0) {
        this.sort = {
          field: this.fields[0].name,
          descending: false
        }
      }
    }
    if (this.width !== undefined) {
      this.tableWidthComputed = this.width;
    }
    if (this.height !== undefined) {
      this.tableHeightComputed = this.height;
    }
    this.reload();
  }
}
</script>

<style scoped>

</style>