<template>
  <div :style="fieldStyle" class="nevs-field">
    <span v-if="label!== '' || reserveHeights" class="nevs-field-label">{{ label }}</span>
    <input :readonly="readonly" ref="input" v-model='value' class="nevs-number-field nevs-field-content" type="text"
           @focusin="focusIn" @focusout="focusOut"/>
    <span v-if="(hint!== '' || reserveHeights) && showHint" class="nevs-field-hint">{{ hint }}</span>
    <span v-if="(error!== '' || reserveHeights) && (!showHint || hint==='')" class="nevs-field-error">{{ error }}</span>
  </div>
</template>

<script>

import Big from 'big.js';

export default {
  name: "NevsNumberField",
  props: {
    readonly: {
      type: Boolean,
      default: false
    },
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
    thousandSeparator: {
      type: String,
      default: '.'
    },
    decimalSeparator: {
      type: String,
      default: ','
    },
    decimalPlaces: {
      type: Number,
      default: 2
    },
    modelValue: [Number, String]
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
      let formatted = this.formatNumber(this.modelValue.toString());
      if (formatted !== this.value) {
        this.value = formatted;
      }
    }
  },
  methods: {
    formatInput() {
      this.value = this.cleanupNumber(this.value);
      this.$emit('update:modelValue', parseFloat(this.value.split(this.thousandSeparator).join('').split(this.decimalSeparator).join('.')));
    },
    focusIn() {
      this.showHint = true;
      this.selectAll();
    },
    focusOut() {
      this.showHint = false;
      this.formatInput();
    },
    selectAll() {
      this.$refs.input.select();
    },
    cleanupNumber(value) {
      let cleanedUp = value;
      for (let i = 0; i < cleanedUp.length; i++) {
        if (!((/\d/).test(cleanedUp[i])) && cleanedUp[i] !== this.decimalSeparator) {
          if (i + 1 < cleanedUp.length) {
            cleanedUp = cleanedUp.slice(0, i) + cleanedUp.slice(i + 1);
          } else {
            cleanedUp = cleanedUp.slice(0, i);
          }
          --i;
        }
      }
      cleanedUp = cleanedUp.split(this.decimalSeparator).join('.');
      if (cleanedUp === '') cleanedUp = 0;
      let number = new Big(cleanedUp).round(this.decimalPlaces);
      let preview = number.toFixed(this.decimalPlaces).toString();
      preview = preview.replace('.', this.decimalSeparator);
      let location = preview.indexOf(this.decimalSeparator) - 3;
      while (location > 0) {
        preview = preview.slice(0, location) + this.thousandSeparator + preview.slice(location);
        location -= 3;
      }
      return preview;
    },
    formatNumber(value) {
      let preview = new Big(value).round(this.decimalPlaces).toFixed(this.decimalPlaces).toString().replace('.', this.decimalSeparator);
      let location = preview.indexOf(this.decimalSeparator) - 3;
      while (location > 0) {
        preview = preview.slice(0, location) + this.thousandSeparator + preview.slice(location);
        location -= 3;
      }
      return preview;
    }
  },
  mounted() {
    if (this.modelValue !== undefined) {
      this.value = this.formatNumber(this.modelValue.toString());
    }
  }
}

</script>
