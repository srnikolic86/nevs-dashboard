<template>
  <div class="nevs-main-menu">
    <img v-if="logo !== ''" :src="logo" class="nevs-main-menu-logo"/>
    <template v-for="(item1, key1) in items" :key="key1">
      <template v-if="item1.id!=='---'">
        <a :class="{'nevs-main-menu-item': true, 'active': isSelected(item1)}" :href="item1.link"
           @click.prevent="menuClick(item1)">
          <span v-if="item1.icon !== null" class="nevs-main-menu-item-icon" v-html="item1.icon"></span>{{ item1.label }}
        </a>
        <a v-for="(item2, key2) in item1.children"
           v-show="$store.state.selectedMenu === item1.id"
           :key="key2" :class="{'nevs-main-menu-subitem': true, 'active': isSelected(item2)}" :href="item2.link"
           @click.prevent="menuClick(item2)">
          <span v-if="item2.icon !== null" class="nevs-main-menu-subitem-icon" v-html="item2.icon"></span>{{
            item2.label
          }}
        </a>
      </template>
      <template v-if="item1.id==='---'">
        <div class="nevs-main-menu-separator" />
      </template>
    </template>
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