<template>
  <div class="nevs-field nevs-wysiwyg">
    <span v-if="label !== ''" class="nevs-field-label">{{ label }}</span>
    <div ref="editor" class="nevs-wysiwyg-editor"></div>
    <span v-if="error !== ''" class="nevs-field-error">{{ error }}</span>
  </div>
</template>

<script>
import Quill from 'quill';
import 'quill/dist/quill.snow.css';

export default {
  name: "NevsWysiwyg",
  props: {
    label: {
      type: String,
      default: ''
    },
    error: {
      type: String,
      default: ''
    },
    readonly: {
      type: Boolean,
      default: false
    },
    modelValue: {
      type: String,
      default: ''
    }
  },
  emits: ['update:modelValue'],
  data() {
    return {
      quill: null,
      // Guards against feeding the editor's own emitted value back into it (which would move the caret).
      internalChange: false
    }
  },
  watch: {
    modelValue(value) {
      if (this.quill && !this.internalChange && value !== this.currentHtml()) {
        this.quill.root.innerHTML = value || '';
      }
    },
    readonly(value) {
      if (this.quill) this.quill.enable(!value);
    }
  },
  methods: {
    currentHtml() {
      let html = this.quill.root.innerHTML;
      return html === '<p><br></p>' ? '' : html;
    }
  },
  mounted() {
    this.quill = new Quill(this.$refs.editor, {
      theme: 'snow',
      readOnly: this.readonly,
      modules: {
        toolbar: [
          [{'header': [1, 2, 3, false]}],
          ['bold', 'italic', 'underline'],
          [{'list': 'ordered'}, {'list': 'bullet'}],
          ['link'],
          ['clean']
        ]
      }
    });
    if (this.modelValue) {
      this.quill.root.innerHTML = this.modelValue;
    }
    this.quill.on('text-change', () => {
      this.internalChange = true;
      this.$emit('update:modelValue', this.currentHtml());
      this.$nextTick(() => {
        this.internalChange = false;
      });
    });
    if (this.readonly) {
      this.quill.enable(false);
    }
  }
}
</script>

<style scoped>

</style>
