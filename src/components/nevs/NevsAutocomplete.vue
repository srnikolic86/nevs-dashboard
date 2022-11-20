<template>
  <div :style="wrapperStyle" class="nevs-field">
    <span v-if="label!== '' || reserveHeights" class="nevs-field-label">{{ label }}</span>
    <input @focusin="toggleDropdown" @focusout="toggleDropdown" v-model='selected' type="text" class="nevs-autocomplete nevs-field-content" />
    <span v-if="(hint!== '' || reserveHeights) && showHint" class="nevs-field-hint">{{ hint }}</span>
    <div class="nevs-dropdown-holder">
      <Transition name="dropdown">
        <div v-show="showDropdown" class="nevs-dropdown-frame">
          <div class="nevs-dropdown-options">
            <div v-for="(option, key) in filteredOptions" :key="key"
                 class="nevs-dropdown-option" @click="selectOption(option)">
              {{ option }}
            </div>
          </div>
        </div>
      </Transition>
    </div>
    <span v-if="(error!== '' || reserveHeights) && (!showHint || hint==='')" class="nevs-field-error">{{ error }}</span>
  </div>
</template>

<script>

export default {
  name: "NevsAutocomplete",
  props: {

    width: {
      type: String,
      default: '100%'
    },
    label: {
      type: String,
      default: ''
    },
    error: {
      type: String,
      default: ''
    },
    hint: {
      type: String,
      default: ''
    },
    reserveHeights: {
      type: Boolean,
      default: false
    },
    options: Array,
    ajax: String,
    minimumSearchLength: Number,
    modelValue: String
  },
  emits: [
    'update:modelValue'
  ],
  data() {
    return {
      showHint: false,
      selected: '',
      allOptions: [],
      showDropdown: false
    }
  },
  computed: {
    wrapperStyle() {
      let style = {};
      if (this.width) {
        style.width = this.width;
      }
      return style;
    },

    filteredOptions() {
      if (this.minimumSearchLength) {
        if (this.search.length < this.minimumSearchLength) {
          return [];
        }
      }

      let filtered = [];
      for (let option of this.allOptions) {
        if (this.$HELPERS.ToCroatianLower(option).search(this.$HELPERS.ToCroatianLower(this.selected)) !== -1) {
          filtered.push(option);
        }
      }
      return filtered;
    }
  },
  watch: {
    modelValue() {
      if (this.modelValue !== this.selected) {
        this.setValue(this.modelValue);
      }
    },
    selected() {
      this.selectOption(this.selected);
    }
  },
  methods: {
    setValue(value) {
      this.selected = value;
    },
    selectOption(option) {
      this.selected = option;
      this.$emit('update:modelValue', this.selected);
    },
    toggleDropdown() {
      this.showDropdown = !this.showDropdown;
      this.showHint = !this.showHint;
    },
    loadAPIOptions() {
      let vm = this;
      this.$API.APICall('get', this.ajax, {protected: this.protected}, (data, success) => {
        if (success) {
          vm.allOptions = data;
          vm.$nextTick(() => {
            vm.setValue(vm.modelValue);
          });
        }
      });
    }
  },
  mounted() {
    if (!this.ajax) {
      this.allOptions = this.options;
      this.setValue(this.modelValue);
    } else {
      this.loadAPIOptions();
    }
    let vm = this;
    this.$CROSS_TAB_BUS.ListenToEvent('reload-data', () => {
      vm.loadAPIOptions();
    });
  }
}

</script>
