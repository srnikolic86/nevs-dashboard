<template>
  <div class="nevs-multi-upload">
    <input ref="fileInput" :accept="acceptAttribute" class="nevs-multi-upload-input" type="file" multiple
           @change="inputChange"/>
    <div class="nevs-multi-upload-zone" :class="{'nevs-multi-upload-zone-active': dragActive}"
         @click="browse" @dragenter.prevent="dragActive = true" @dragover.prevent="dragActive = true"
         @dragleave.prevent="onDragLeave" @drop.prevent="onDrop">
      <template v-if="!uploadingCount">
        <i class="fa-solid fa-cloud-arrow-up nevs-multi-upload-icon"></i>
        <div class="nevs-multi-upload-text">{{ $LANG.Get('labels.dropFilesHere') }}</div>
        <div class="nevs-multi-upload-subtext">{{ $LANG.Get('labels.allowedFileTypes') }}</div>
      </template>
      <template v-else>
        <i class="fa-solid fa-spinner fa-spin nevs-multi-upload-icon"></i>
        <div class="nevs-multi-upload-text">{{ $LANG.Get('labels.uploadInProgress') }}</div>
      </template>
    </div>
  </div>
</template>

<script>
const DEFAULT_EXTENSIONS = [
  'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
  'txt', 'csv', 'rtf', 'odt', 'ods', 'odp',
  'png', 'jpg', 'jpeg', 'gif', 'webp', 'bmp', 'svg', 'tif', 'tiff', 'heic'
];

export default {
  name: "NevsMultiUpload",
  props: {
    extensions: {
      type: Array,
      default: () => DEFAULT_EXTENSIONS
    },
    maxSizeMb: {
      type: Number,
      default: 25
    }
  },
  emits: [
    'uploaded'
  ],
  data() {
    return {
      dragActive: false,
      uploadingCount: 0
    }
  },
  computed: {
    acceptAttribute() {
      return this.extensions.map((extension) => '.' + extension).join(',');
    }
  },
  methods: {
    browse() {
      if (this.uploadingCount) return;
      this.$refs.fileInput.click();
    },
    onDragLeave() {
      this.dragActive = false;
    },
    onDrop(event) {
      this.dragActive = false;
      if (this.uploadingCount) return;
      this.handleFiles(event.dataTransfer.files);
    },
    inputChange() {
      this.handleFiles(this.$refs.fileInput.files);
      this.$refs.fileInput.value = '';
    },
    extensionOf(fileName) {
      let parts = fileName.split('.');
      return parts.length > 1 ? parts.pop().toLowerCase() : '';
    },
    handleFiles(fileList) {
      let files = Array.from(fileList);
      if (files.length === 0) return;

      let accepted = [];
      let rejectedType = [];
      let rejectedSize = [];
      for (let file of files) {
        if (!this.extensions.includes(this.extensionOf(file.name))) {
          rejectedType.push(file.name);
        } else if (file.size > this.maxSizeMb * 1024 * 1024) {
          rejectedSize.push(file.name);
        } else {
          accepted.push(file);
        }
      }

      let messages = [];
      if (rejectedType.length > 0) {
        messages.push(this.$LANG.Get('alerts.fileTypeNotAllowed') + '<br>' + rejectedType.join('<br>'));
      }
      if (rejectedSize.length > 0) {
        messages.push(this.$LANG.Get('alerts.fileTooLargeList').replace('%size%', this.maxSizeMb) + '<br>' + rejectedSize.join('<br>'));
      }
      if (messages.length > 0) {
        this.$LOCAL_BUS.TriggerEvent('popup', {
          type: 'alert',
          text: messages.join('<br><br>')
        });
      }

      if (accepted.length === 0) return;

      let vm = this;
      let uploaded = [];
      this.uploadingCount += accepted.length;
      for (let file of accepted) {
        this.$API.APIUpload(file, (data) => {
          if (data && data['success']) {
            uploaded.push(data['file']);
          }
          vm.uploadingCount--;
          if (vm.uploadingCount === 0 && uploaded.length > 0) {
            vm.$emit('uploaded', uploaded);
          }
        });
      }
    }
  }
}
</script>

<style scoped>

</style>
