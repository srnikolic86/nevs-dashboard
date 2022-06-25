<template>
  <div @mouseenter="showHint = true" @mouseleave ="showHint = false" :style="fieldStyle" class="nevs-field">
    <span v-if="label!== '' || reserveHeights" class="nevs-field-label">{{ label }}</span>
    <div class="nevs-upload nevs-field-content">
      <input ref="fileInput" :accept="accepted" class="nevs-upload-file-input" type="file" @change="fileChange"/>
      <template v-if="!uploadInProgress">
        <span v-show="fileData.id===0" class="nevs-upload-icon " @click="uploadClick"><i
            class="fa-solid fa-upload"></i></span>
        <span v-show="fileData.id!==0" class="nevs-upload-icon " @click="downloadClick"><i
            class="fa-solid fa-download"></i></span>
        <span v-show="fileData.id!==0" class="nevs-upload-icon " @click="deleteClick"><i class="fa-solid fa-trash"></i></span>
      </template>
      <div v-if="uploadInProgress">
        {{ $LANG.Get('labels.uploadInProgress') }}
      </div>
    </div>
    <span v-if="(hint!== '' || reserveHeights) && showHint" class="nevs-field-hint">{{ hint }}</span>
    <span v-if="(error!== '' || reserveHeights) && (!showHint || hint==='')" class="nevs-field-error">{{ error }}</span>
  </div>
</template>

<script>
export default {
  name: "NevsUpload",
  components: {},
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
    accept: String,
    modelValue: Object
  },
  emits: [
    'update:modelValue'
  ],
  data() {
    return {
      fileData: {
        name: '',
        link: '',
        id: 0
      },
      uploadInProgress: false,
      showHint: false,
      accepted: ''
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
      if (JSON.stringify(this.fileData) !== JSON.stringify(this.modelValue)) {
        this.fileData = this.modelValue;
      }
    },
    fileData() {
      this.$emit('update:modelValue', this.fileData);
    }
  },
  methods: {
    uploadClick() {
      this.$refs.fileInput.click();
      5
    },
    fileChange() {
      this.uploadInProgress = true;
      let vm = this;
      this.$API.APIUpload(this.$refs.fileInput.files[0], data => {
        if (data['success']) {
          vm.fileData = data['file'];
          vm.uploadInProgress = false;
        }
      });
    },
    downloadClick() {
      window.open(this.fileData.link);
    },
    deleteClick() {
      let vm = this;
      this.$LOCAL_BUS.TriggerEvent('popup', {
        type: 'confirm', text: this.$LANG.Get('alerts.fileDeletionQuestion'), callback: (response) => {
          if (response) {
            vm.fileData = {
              name: '',
              link: '',
              id: 0
            };
          }
        }
      });
    }
  },
  mounted() {
    if (this.accept !== undefined) {
      this.accepted = this.accept;
    }
    if (this.modelValue !== undefined) {
      this.fileData = this.modelValue;
    }
  }
}
</script>

<style scoped>

</style>