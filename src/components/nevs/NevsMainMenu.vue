<template>
  <div :class="{'nevs-main-menu': true, 'nevs-main-menu-collapsed': autoCollapse}">
    <img v-if="logo !== ''" :src="logo" class="nevs-main-menu-logo"/>
    <template v-for="(item1, key1) in items" :key="key1">
      <template v-if="item1.id!=='---'">
        <a :class="{'nevs-main-menu-item': true, 'active': isSelected(item1),
                    'parent-active': autoCollapse && hasSelectedChild(item1)}" :href="item1.link"
           :title="autoCollapse ? item1.label : null"
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
    <div class="nevs-main-menu-footer">
      <span :class="{'nevs-main-menu-collapse-toggle': true, 'pinned': !autoCollapse}"
            :title="autoCollapse ? $LANG.Get('tooltips.disableMenuCollapse') : $LANG.Get('tooltips.enableMenuCollapse')"
            @click="toggleAutoCollapse" v-html="collapseIcon"></span>
    </div>
  </div>
</template>

<script>
// Key of the user_data row that keeps the pin state, per user, between sessions.
const AUTO_COLLAPSE_KEY = 'menu_auto_collapse';

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
  data() {
    return {
      // On desktop, shrink the menu to an icon-only rail and expand it again while the mouse is over it.
      // Toggled by the pin in the menu footer. Read straight out of the store rather than fetched, because
      // the session response already carries it - the menu would otherwise paint expanded and then collapse.
      // Ignored below the responsive breakpoint, where the menu is toggled by the top bar instead.
      autoCollapse: this.$store.state.userData[AUTO_COLLAPSE_KEY] === true
    };
  },
  computed: {
    // Rendered through v-html rather than as a plain <i> with a bound class, like the icons the menu items and
    // the top bar get handed. Font Awesome runs from js/all.min.js, which swaps every <i> it finds for an <svg>
    // and leaves Vue holding the detached <i> - class changes on it would never reach the icon in the document.
    // Replacing the span's contents instead gives Font Awesome a fresh <i> to convert on every toggle.
    collapseIcon() {
      return this.autoCollapse ? '<i class="fa-solid fa-thumbtack-slash"></i>' : '<i class="fa-solid fa-thumbtack"></i>';
    }
  },
  methods: {
    // Written back to user_data on every toggle, so the menu comes back the way this user left it. A failed
    // write is left alone rather than reverted or reported: the click still did what the user asked of it for
    // this session, and a popup over a display preference would be worse than the menu forgetting it.
    toggleAutoCollapse() {
      this.autoCollapse = !this.autoCollapse;
      // Kept in the store too, so a remount within the same session sees the current state and not the one
      // the session response arrived with.
      let userData = {...this.$store.state.userData};
      userData[AUTO_COLLAPSE_KEY] = this.autoCollapse;
      this.$store.commit('setUserData', userData);
      this.$API.APICall('post', 'user-data', {
        key: AUTO_COLLAPSE_KEY,
        data: this.autoCollapse
      }, () => {
      }, false);
    },
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