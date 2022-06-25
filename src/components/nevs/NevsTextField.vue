<template>
  <div :style="fieldStyle" class="nevs-field">
    <span v-if="label!== '' || reserveHeights" class="nevs-field-label">{{ label }}</span>
    <input :name="name" :type="type" @focusin="showHint=true" @focusout="showHint=false" v-model='value'
           class="nevs-text-field nevs-field-content"/>
    <span v-if="(hint!== '' || reserveHeights) && showHint" class="nevs-field-hint">{{ hint }}</span>
    <span v-if="(error!== '' || reserveHeights) && (!showHint || hint==='')" class="nevs-field-error">{{ error }}</span>
  </div>
</template>

<script>

export default {
  name: "NevsTextField",
  props: {
    width: {
      type: String,
      default: '100%'
    },
    name: String,
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
    type: {
      type: String,
      default: 'text'
    },
    modelValue: String,
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
