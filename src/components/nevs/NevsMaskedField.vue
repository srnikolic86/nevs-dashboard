<template>
  <div :style="fieldStyle" class="nevs-field">
    <span v-if="label!== '' || reserveHeights" class="nevs-field-label">{{ label }}</span>
    <input :readonly="readonly" @focusin="this.showHint = true" @focusout="this.showHint = false" v-model='value' class="nevs-text-field nevs-field-content" type="text"/>
    <span v-if="(hint!== '' || reserveHeights) && showHint" class="nevs-field-hint">{{ hint }}</span>
    <span v-if="(error!== '' || reserveHeights) && (!showHint || hint==='')" class="nevs-field-error">{{ error }}</span>
  </div>
</template>

<script>

export default {
  name: "NevsMaskedField",
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
    mask: String,
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
      this.applyMask();
      this.$emit('update:modelValue', this.value);
    }
  },
  methods: {
    applyMask() {
      this.value = this.processValue(this.value).masked;
    },
    processValue(value="") {
      if (!this.mask) return value;
      let masked = '';
      let broken = false;
      let valueIndex = -1;
      for (let maskIndex = 0; maskIndex < this.mask.length; maskIndex++) {
        if (broken) break;
        valueIndex++;
        switch (this.mask[maskIndex]) {
          case '\\':
            masked += this.mask[++maskIndex];
            if (value.length >= valueIndex || this.mask[maskIndex] !== value[valueIndex]) {
              broken = true;
            }
            break;
          case 'C':
            if (!broken && value.length > valueIndex) {
              masked += value[valueIndex];
            } else {
              broken = true;
            }
            break;
          case 'A':
            if (!broken && value.length > valueIndex && (/[a-zA-Z\d]/).test(value[valueIndex])) {
              masked += value[valueIndex];
            } else {
              broken = true;
            }
            break;
          case 'L':
            if (!broken && value.length > valueIndex && (/[a-zA-Z]/).test(value[valueIndex])) {
              masked += value[valueIndex];
            } else {
              broken = true;
            }
            break;
          case 'N':
            if (!broken && value.length > valueIndex && (/\d/).test(value[valueIndex])) {
              masked += value[valueIndex];
            } else {
              broken = true;
            }
            break;
          case 'X':
            if (!broken && value.length > valueIndex && (/[^a-zA-Z\d]/).test(value[valueIndex])) {
              masked += value[valueIndex];
            } else {
              broken = true;
            }
            break;
          case 'c':
            if (!broken && value.length > valueIndex) {
              masked += value[valueIndex];
            }
            break;
          case 'a':
            if (!broken && value.length > valueIndex && (/[a-zA-Z\d]/).test(value[valueIndex])) {
              masked += value[valueIndex];
            } else {
              valueIndex--;
            }
            break;
          case 'l':
            if (!broken && value.length > valueIndex && (/[a-zA-Z]/).test(value[valueIndex])) {
              masked += value[valueIndex];
            } else {
              valueIndex--;
            }
            break;
          case 'n':
            if (!broken && value.length > valueIndex && (/\d/).test(value[valueIndex])) {
              masked += value[valueIndex];
            } else {
              valueIndex--;
            }
            break;
          case 'x':
            if (!broken && value.length > valueIndex && (/[^a-zA-Z\d]/).test(value[valueIndex])) {
              masked += value[valueIndex];
            } else {
              valueIndex--;
            }
            break;
          default:
            if (value.length <= valueIndex || this.mask[maskIndex] !== value[valueIndex]) {
              broken = true;
            } else {
              masked += this.mask[maskIndex];
            }
        }
      }
      return {
        masked: masked,
        broken: broken
      };
    },
  },
  mounted() {
    this.value = this.processValue(this.modelValue).masked;
  }
}

</script>
