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
    <div class="nevs-top-bar-buttons">
      <span v-for="(button, key) in buttons" :key="key" @click="button.action" :title="button.tooltip" class="nevs-top-bar-button" v-html="button.icon">
      </span>
      <!-- Built in rather than handed over through `buttons`, so every project gets dark mode without wiring it.
           Last in the markup, which the right-to-left row puts to the left of the project's own buttons. -->
      <span v-if="allowDarkMode" :title="darkMode ? $LANG.Get('tooltips.disableDarkMode') : $LANG.Get('tooltips.enableDarkMode')"
            class="nevs-top-bar-button" @click="toggleDarkMode" v-html="darkModeIcon"></span>
    </div>
  </div>
</template>

<script>
import NevsDarkMode from "@/components/nevs/NevsDarkMode.js";

// Key of the user_data row that keeps the light/dark choice, per user, between sessions.
const DARK_MODE_KEY = 'dark_mode';

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
  data() {
    // With dark mode disallowed a saved preference is ignored rather than cleared, and comes back if it is
    // allowed again.
    const allowDarkMode = NevsDarkMode.Allowed();
    // The user's own choice wins, read straight out of the store because the session response already carries
    // it. A user who never chose gets whatever this device was last left in, which is also what the login
    // screen just showed, so logging in does not flip the page.
    const saved = this.$store.state.userData[DARK_MODE_KEY];
    const preferred = saved === true || saved === false ? saved : NevsDarkMode.Stored() === true;
    return {
      allowDarkMode: allowDarkMode,
      darkMode: allowDarkMode && preferred
    };
  },
  computed: {
    // Through v-html for the same reason as the pin in NevsMainMenu: Font Awesome replaces the <i> with an <svg>,
    // so a bound class would never reach the icon in the document. Shows what a click switches to.
    darkModeIcon() {
      return this.darkMode ? '<i class="fa-solid fa-sun"></i>' : '<i class="fa-solid fa-moon"></i>';
    }
  },
  methods: {
    // Also remembered on this device, so the login screen comes up the way the last user here left it.
    applyDarkMode() {
      NevsDarkMode.Apply(this.darkMode);
      if (this.allowDarkMode) {
        NevsDarkMode.Store(this.darkMode);
      }
    },
    // Saved the same way as the menu pin: a failed write is left alone, the switch still holds for this session.
    toggleDarkMode() {
      this.darkMode = !this.darkMode;
      this.applyDarkMode();
      let userData = {...this.$store.state.userData};
      userData[DARK_MODE_KEY] = this.darkMode;
      this.$store.commit('setUserData', userData);
      this.$API.APICall('post', 'user-data', {
        key: DARK_MODE_KEY,
        data: this.darkMode
      }, () => {
      }, false);
    },
    menuButtonClick() {
      this.$emit('toggleMenu');
    },
    breadcrumbClick(breadcrumb) {
      if (breadcrumb.link !== null) {
        this.$router.push(breadcrumb.link);
      }
    }
  },
  // Nothing to undo on unmount: after logout the login screen stays in the mode the user left the page in.
  created() {
    this.applyDarkMode();
  }
}
</script>

<style scoped>

</style>
