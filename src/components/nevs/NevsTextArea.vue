<template>
  <div :style="fieldStyle" class="nevs-field">
    <span v-if="label!== '' || reserveHeights" class="nevs-field-label">{{ label }}</span>
    <textarea :readonly="readonly" @focusin="showHint=true" @focusout="showHint=false" v-model='value' class="nevs-text-area nevs-field-content" />
    <span v-if="(hint!== '' || reserveHeights) && showHint" class="nevs-field-hint">{{ hint }}</span>
    <span v-if="(error!== '' || reserveHeights) && (!showHint || hint==='')" class="nevs-field-error">{{ error }}</span>
  </div>
</template>

<script>

export default {
  name: "NevsTextArea",
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
    modelValue: String
  },
  emits: [
    'update:modelValue'
  ],
  data() {
    return {
      showHint: false,
      value: ''
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
  mounted() {
    this.value = this.modelValue;
  }
}

</script>
