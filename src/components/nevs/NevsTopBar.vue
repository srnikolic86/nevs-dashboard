<template>
  <div class="nevs-top-bar">
    <div @click="menuButtonClick" class="nevs-top-bar-menu-button">
      <i class="fa-solid fa-bars"></i>
    </div>
    <div class="nevs-top-bar-breadcrumbs">
      <div v-for="(breadcrumb, key) in breadcrumbs" :key="key" class="nevs-top-bar-breadcrumbs-item">
        <span v-if="key !== 0"><i class="fa-solid fa-angle-right nevs-top-bar-breadcrumbs-item-separator"></i></span>
        <a :class="{'active': breadcrumb.link !== null}" @click.prevent="breadcrumbClick(breadcrumb)" :href="breadcrumb.link">{{ breadcrumb.label }}</a>
      </div>
    </div>
    <span v-for="(button, key) in buttons" :key="key" @click="button.action" :title="button.tooltip" class="nevs-top-bar-button" v-html="button.icon">
    </span>
  </div>
</template>

<script>
export default {
  name: "NevsTopBar",
  props: {
    breadcrumbs: {
      type: Array,
      default: () => {
        return [];
      }
    },
    buttons: {
      type: Array,
      default: () => {
        return [];
      }
    }
  },
  emits: [
    'toggleMenu'
  ],
  methods: {
    menuButtonClick() {
      this.$emit('toggleMenu');
    },
    breadcrumbClick(breadcrumb) {
      if (breadcrumb.link !== null) {
        this.$router.push(breadcrumb.link);
      }
    }
  }
}
</script>

<style scoped>

</style>
