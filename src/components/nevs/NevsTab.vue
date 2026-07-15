<template>
  <div v-show="value === modelValue" class="nevs-tab-body">
    <slot></slot>
  </div>
</template>

<script>
export default {
  name: "NevsTab",
  props: {
    // This pane's identifier; the pane is shown while it equals the active value (modelValue).
    value: {
      default: null
    },
    modelValue: {
      default: null
    }
  },
  emits: ['update:modelValue']
}
</script>

<style scoped>

</style>
