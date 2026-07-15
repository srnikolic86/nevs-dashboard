<template>
  <div class="nevs-tabs">
    <button v-for="tab in visibleTabs" :key="tab.value" type="button"
            :class="{'nevs-tab': true, 'active': tab.value === modelValue}"
            @click="select(tab.value)">
      {{ tab.label }}
    </button>
  </div>
</template>

<script>
export default {
  name: "NevsTabs",
  props: {
    // Each tab: { value, label, visible? }. A tab is shown unless `visible` is explicitly false.
    tabs: {
      type: Array,
      default: () => []
    },
    modelValue: {
      default: null
    }
  },
  emits: ['update:modelValue'],
  computed: {
    visibleTabs() {
      return this.tabs.filter((tab) => tab.visible !== false);
    }
  },
  methods: {
    select(value) {
      if (value !== this.modelValue) {
        this.$emit('update:modelValue', value);
      }
    }
  }
}
</script>

<style scoped>

</style>
