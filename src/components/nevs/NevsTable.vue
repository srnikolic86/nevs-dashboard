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
    <!-- Plain inputs rather than NevsNumberField: these only ever hold a small whole number, and the field
         component's label/hint/error slots make the footer taller than it needs to be. -->
    <input class="nevs-table-footer-input" type="text" inputmode="numeric" maxlength="6"
           :value="rowsPerPageText" @input="digitsOnly($event, 'rowsPerPageText')"
           @focus="$event.target.select()" @blur="commitRowsPerPage" @keyup.enter="$event.target.blur()"/>
    <span class="nevs-table-footer-label">
        {{ $LANG.Get('pagination.resultsPerPage') }},
        {{ $LANG.Get('pagination.page') }}
      </span>
    <span class="nevs-table-pagination-arrow" @click="modifyCurrentPage(-1)" v-show="currentPage > 1">
            <i class="fa-solid fa-caret-left"></i>
          </span>
    <input class="nevs-table-footer-input" type="text" inputmode="numeric" maxlength="6"
           :value="currentPageText" @input="digitsOnly($event, 'currentPageText')"
           @focus="$event.target.select()" @blur="commitCurrentPage" @keyup.enter="$event.target.blur()"/>
    <span class="nevs-table-pagination-arrow" @click="modifyCurrentPage(1)" v-show="currentPage < totalPages">
            <i class="fa-solid fa-caret-right"></i>
          </span>
    <span class="nevs-table-footer-label">
        {{ $LANG.Get('pagination.of') }}
        {{ totalPagesDisplay }}
      </span>
  </div>
</template>

<script>

export default {
  name: "NevsTable",
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
      // Must match Helpers::CappedCount's cap on the backend: past this many rows the API returns -1 instead
      // of an exact total, and pages are capped here.
      COUNTED_CAP: 10000,
      currentPage: 1,
      rowsPerPage: 20,
      // What the footer inputs show. Kept apart from the numbers above so typing does not fire a reload on
      // every keystroke - the value is committed on blur or Enter.
      currentPageText: '1',
      rowsPerPageText: '20',
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
    // The API returns -1 for total records when the exact count would be too expensive (more than the cap).
    // In that case pages are capped at COUNTED_CAP and the page count is shown as "<n>+".
    countIsCapped() {
      return this.totalRecords === -1;
    },
    totalPages() {
      if (this.rowsPerPage === 0) return 0;
      if (this.countIsCapped) return Math.ceil(this.COUNTED_CAP / this.rowsPerPage);
      return Math.ceil(this.totalRecords / this.rowsPerPage);
    },
    totalPagesDisplay() {
      return this.countIsCapped ? this.totalPages + '+' : this.totalPages;
    }
  },
  watch: {
    currentPage() {
      this.currentPageText = this.currentPage.toString();
      this.reload();
    },
    rowsPerPage() {
      this.rowsPerPageText = this.rowsPerPage.toString();
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
    // Digits only. The element is written back to as well, otherwise a rejected character stays on screen:
    // the bound value did not change, so there is nothing for Vue to re-render.
    digitsOnly(event, key) {
      let cleaned = event.target.value.replace(/\D/g, '');
      this[key] = cleaned;
      if (event.target.value !== cleaned) event.target.value = cleaned;
    },
    commitRowsPerPage() {
      let value = parseInt(this.rowsPerPageText, 10);
      if (isNaN(value) || value < 1) value = 1;
      this.rowsPerPageText = value.toString();
      // An unchanged value leaves the watcher (and the reload it triggers) alone.
      this.rowsPerPage = value;
    },
    commitCurrentPage() {
      let value = parseInt(this.currentPageText, 10);
      if (isNaN(value) || value < 1) value = 1;
      this.currentPageText = value.toString();
      this.currentPage = value;
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