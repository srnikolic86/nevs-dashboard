<template>
  <div class="nevs-table-filters-panel">
    <div class="nevs-table-filters-panel-header" @click="toggle"
         :title="expanded ? $LANG.Get('tooltips.hideFilters') : $LANG.Get('tooltips.showFilters')">
      <span class="nevs-table-filters-panel-title">
        <i class="fa-solid fa-filter"></i> {{ titleText }}
      </span>
      <!-- Collapsed state still has to show what is being filtered on, so the active values are listed here. -->
      <span v-show="!expanded" class="nevs-table-filters-panel-summary">
        <template v-if="activeSummary.length > 0">
          <span v-for="(entry, key) in activeSummary" :key="key" class="nevs-table-filters-panel-pill">
            <span class="nevs-table-filters-panel-pill-label">{{ entry.label }}:</span> {{ entry.value }}
            <!-- Named entries can be reset straight from the collapsed bar, without opening the panel. -->
            <span v-if="entry.name !== undefined && entry.name !== null"
                  class="nevs-table-filters-panel-pill-clear" :title="$LANG.Get('tooltips.clearFilter')"
                  @click.stop="clear(entry)"><i class="fa-solid fa-xmark"></i></span>
          </span>
        </template>
        <span v-else class="nevs-table-filters-panel-empty">{{ $LANG.Get('labels.noActiveFilters') }}</span>
      </span>
      <span class="nevs-table-filters-panel-toggle">
        <i :class="expanded ? 'fa-solid fa-angle-up' : 'fa-solid fa-angle-down'"></i>
      </span>
    </div>
    <div v-show="expanded" class="nevs-table-filters-panel-body">
      <slot></slot>
    </div>
  </div>
</template>

<script>
export default {
  name: "NevsTableFilters",
  props: {
    // Human readable description of the currently applied filters, as {label, value, name} entries. Entries
    // with an empty value are dropped, so the parent can hand over the full list and let this component pick
    // the active ones. `name` is optional and only identifies the entry back to the parent when its reset
    // icon is clicked; entries without one get no icon.
    summary: {
      type: Array,
      default: () => {
        return [];
      }
    },
    title: {
      type: String,
      default: ''
    },
    defaultExpanded: {
      type: Boolean,
      default: false
    }
  },
  emits: [
    'toggle',
    // The panel does not own the filter values, so resetting one is up to the parent.
    'clear'
  ],
  data() {
    return {
      expanded: this.defaultExpanded
    }
  },
  computed: {
    titleText() {
      return this.title !== '' ? this.title : this.$LANG.Get('labels.filters');
    },
    activeSummary() {
      return this.summary.filter(entry => {
        return entry.value !== null && entry.value !== undefined && String(entry.value).trim() !== '';
      });
    }
  },
  methods: {
    toggle() {
      this.expanded = !this.expanded;
      this.$emit('toggle', this.expanded);
    },
    clear(entry) {
      this.$emit('clear', entry);
    }
  }
}
</script>

<style scoped>

</style>
