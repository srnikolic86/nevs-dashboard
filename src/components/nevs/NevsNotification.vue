<template>
  <div v-if="show" class="nevs-notification">
    <div class="nevs-notification-message" v-html="text"/>
  </div>
</template>

<script>
export default {
  name: "NevsNotification",
  data() {
    return {
      text: '',
      show: false,
      defaultDuration: 2000
    }
  },
  mounted() {
    let vm = this;
    this.$LOCAL_BUS.ListenToEvent('notification', (data) => {
      if (data.text === undefined) return;
      vm.text = data.text;
      let duration = (data.duration !== undefined) ? data.duration : vm.defaultDuration;
      setTimeout(
          () => {
            vm.show = false;
          }, duration);
      vm.show = true;
    });
  }
}
</script>

<style scoped>

</style>