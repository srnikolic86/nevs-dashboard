<template>
  <div :style="fieldStyle" class="nevs-field">
    <span v-if="reserveHeights" class="nevs-field-label">&nbsp;</span>
    <div @mouseenter="showHint = true" @mouseleave ="showHint = false" class="nevs-checkbox" @click="toggleValue">
      <span v-if="!value"><i :style="{'font-size': size}" class="fa-regular fa-square"></i></span>
      <span v-if="value"><i :style="{'font-size': size}" class="fa-regular fa-square-check"></i></span>
      <span class="nevs-checkbox-label" v-if="label!== ''" v-html="label"></span>
    </div>
    <span v-if="(hint!== '' || reserveHeights) && showHint" class="nevs-field-hint">{{ hint }}</span>
    <span v-if="(error!== '' || reserveHeights) && (!showHint || hint==='')" class="nevs-field-error">{{ error }}</span>
  </div>
</template>

<script>

export default {
  name: "NevsCheckbox",
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
    readonly: {
      type: Boolean,
      default: false
    },
    modelValue: Boolean,
    size: {
      type: String,
      default: '25px'
    }
  },
  emits: [
    'update:modelValue'
  ],
  data() {
    return {
      showHint: false,
      value: false
    }
  },
  computed: {
    fieldStyle() {
      let style = {};
      if (this.width) {
        style.width = this.width;
      }
      return style;
    }
  },
  watch: {
    modelValue() {
      if (this.modelValue !== this.value) {
        this.value = this.modelValue;
      }
    },
    value() {
      this.$emit('update:modelValue', this.value);
    }
  },
  methods: {
    toggleValue() {
      if (!this.readonly) {
        this.value = !this.value;
      }
    }
  },
  mounted() {
    this.value = this.modelValue;
  }
}

</script>
