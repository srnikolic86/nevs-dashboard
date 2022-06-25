<template>
<div></div>
</template>

<script>
export default {
  name: "ModuleHome",
  mounted() {
    this.$store.commit('selectMenu', 'home');
    this.$store.commit('selectSubMenu', null);
    this.$store.commit('setBreadcrumbs', [
      {
        label: this.$LANG.Get('modules.home'),
        link: null
      }
    ]);
  }
}
</script>

<style scoped>

</style>