<template>
  <div class="nevs-main-menu">
    <img v-if="logo !== ''" class="nevs-main-menu-logo" :src="logo"/>
    <template v-for="(item1, key1) in items" :key="key1">
      <a :class="{'nevs-main-menu-item': true, 'active': isSelected(item1)}" @click.prevent="menuClick(item1)"
         :href="item1.link">
        <span class="nevs-main-menu-item-icon" v-if="item1.icon !== null" v-html="item1.icon"></span>{{ item1.label }}
      </a>
      <a :class="{'nevs-main-menu-subitem': true, 'active': isSelected(item2)}" v-for="(item2, key2) in item1.children"
         :key="key2" v-show="$store.state.selectedMenu === item1.id" @click.prevent="menuClick(item2)"
         :href="item2.link">
        <span class="nevs-main-menu-subitem-icon" v-if="item2.icon !== null" v-html="item2.icon"></span>{{ item2.label }}
      </a>
    </template>
    <div class="nevs-main-menu-footer" v-html="$LANG.Get('labels.menuFooter')">
    </div>
  </div>
</template>

<script>
export default {
  name: "NevsMainMenu",
  props: {
    logo: {
      type: String,
      default: ''
    },
    items: {
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
    isSelected(item) {
      if (item.children !== undefined && item.children.length > 0) {
        return false;
      }
      return item.id === this.$store.state.selectedMenu || item.id === this.$store.state.selectedSubMenu;
    },
    menuClick(item) {
      if (item.children !== undefined && item.children.length > 0) {
        if (this.$store.state.selectedMenu !== item.id) {
          this.$store.commit('selectMenu', item.id);
        } else {
          this.$store.commit('selectMenu', null);
        }
      } else {
        this.$router.push(item.link);
        if (window.innerWidth < 800) {
          this.$emit('toggleMenu');
        }
      }
    }
  }
}
</script>

<style scoped>

</style>