<template>
  <div :class="{'nevs-main-menu': true, 'nevs-main-menu-collapsed': collapse}">
    <img v-if="logo !== ''" :src="logo" class="nevs-main-menu-logo"/>
    <template v-for="(item1, key1) in items" :key="key1">
      <template v-if="item1.id!=='---'">
        <a :class="{'nevs-main-menu-item': true, 'active': isSelected(item1),
                    'parent-active': collapse && hasSelectedChild(item1)}" :href="item1.link"
           :title="collapse ? item1.label : null"
           @click.prevent="menuClick(item1)">
          <span v-if="item1.icon !== null" class="nevs-main-menu-item-icon" v-html="item1.icon"></span><span
            class="nevs-main-menu-item-label">{{ item1.label }}</span>
        </a>
        <a v-for="(item2, key2) in item1.children"
           v-show="$store.state.selectedMenu === item1.id"
           :key="key2" :class="{'nevs-main-menu-subitem': true, 'active': isSelected(item2)}" :href="item2.link"
           @click.prevent="menuClick(item2)">
          <span v-if="item2.icon !== null" class="nevs-main-menu-subitem-icon" v-html="item2.icon"></span><span
            class="nevs-main-menu-subitem-label">{{ item2.label }}</span>
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
    },
    // On desktop, shrink the menu to an icon-only rail and expand it again while the mouse is over it.
    // Ignored below the responsive breakpoint, where the menu is toggled by the top bar instead.
    collapse: {
      type: Boolean,
      default: false
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
    // Collapsed mode hides the sub-items, so the group that owns the selected sub-item is highlighted instead.
    // Keyed off selectedSubMenu rather than selectedMenu, because selectedMenu also tracks which group is open
    // and gets cleared when the user closes a group by hand.
    hasSelectedChild(item) {
      if (item.children === undefined || item.children.length === 0) {
        return false;
      }
      return item.children.some(child => child.id === this.$store.state.selectedSubMenu);
    },
    menuClick(item) {
      if (item.children !== undefined && item.children.length > 0) {
        if (this.$store.state.selectedMenu !== item.id) {
          this.$store.commit('selectMenu', item.id);
        } else {
          this.$store.commit('selectMenu', null);
        }
      } else {
        if (item.external === true) {
          window.open(item.link);
        } else {
          this.$router.push(item.link);
          if (window.innerWidth < 800) {
            this.$emit('toggleMenu');
          }
        }
      }
    }
  }
}
</script>

<style scoped>

</style>