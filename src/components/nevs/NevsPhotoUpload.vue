<template>
    <div :class="{'nevs-photo-upload': true, 'has-image': hasImage, 'uploading': uploadInProgress}" :style="sizeStyle">
        <input ref="fileInput" :accept="accepted" class="nevs-photo-upload-input" type="file" @change="fileChange"/>

        <img v-show="hasImage" :src="hasImage ? fileData.link : undefined" alt="" class="nevs-photo-upload-image"/>
        <span v-show="!hasImage" class="nevs-photo-upload-placeholder">
            <i class="fa-solid fa-user"></i>
        </span>

        <div class="nevs-photo-upload-overlay">
            <span v-show="uploadInProgress" class="nevs-photo-upload-spinner">
                <i class="fa-solid fa-spinner fa-spin"></i>
            </span>
            <span v-show="!uploadInProgress"
                  :title="hasImage ? $LANG.Get('tooltips.changePhoto') : $LANG.Get('tooltips.uploadPhoto')"
                  class="nevs-photo-upload-button" @click="uploadClick">
                <i class="fa-solid fa-camera"></i>
            </span>
            <span v-show="!uploadInProgress && hasImage" :title="$LANG.Get('tooltips.delete')"
                  class="nevs-photo-upload-button nevs-photo-upload-button-delete" @click="deleteClick">
                <i class="fa-solid fa-trash"></i>
            </span>
        </div>
    </div>
</template>

<script>
export default {
    name: "NevsPhotoUpload",
    props: {
        size: {
            type: String,
            default: '120px'
        },
        accept: {
            type: String,
            default: 'image/*'
        },
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
            accepted: 'image/*'
        }
    },
    computed: {
        hasImage() {
            return !!(this.fileData && this.fileData.id && this.fileData.link);
        },
        sizeStyle() {
            return {width: this.size, height: this.size};
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
        },
        fileChange() {
            this.uploadInProgress = true;
            let vm = this;
            this.$API.APIUpload(this.$refs.fileInput.files[0], data => {
                vm.uploadInProgress = false;
                if (vm.$refs.fileInput) {
                    vm.$refs.fileInput.value = '';
                }
                if (data && data['success']) {
                    vm.fileData = data['file'];
                }
            });
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

<style scoped lang="scss">
@use '../../scss/nevs/vars' as *;

.nevs-photo-upload {
    position: relative;
    flex: 0 0 auto;
    border-radius: 50%;
    overflow: hidden;
    background: $sand-100;
    border: 2px solid $sand-300;
    display: flex;
    align-items: center;
    justify-content: center;
}

.nevs-photo-upload-input {
    display: none;
}

.nevs-photo-upload-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.nevs-photo-upload-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 46px;
    color: $ink-muted;
}

.nevs-photo-upload-overlay {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    background: rgba(0, 0, 0, 0.45);
    opacity: 0;
    transition: opacity 0.15s ease;
}

// When there is no image yet, keep the controls visible so the affordance is obvious.
.nevs-photo-upload:not(.has-image) .nevs-photo-upload-overlay {
    background: transparent;
    opacity: 1;
    align-items: flex-end;
    padding-bottom: 8px;
}

.nevs-photo-upload:hover .nevs-photo-upload-overlay {
    opacity: 1;
}

// While uploading, cover the avatar with a scrim and the centred spinner,
// regardless of hover state.
.nevs-photo-upload.uploading .nevs-photo-upload-overlay {
    opacity: 1;
    background: rgba(0, 0, 0, 0.45);
    align-items: center;
    padding-bottom: 0;
}

.nevs-photo-upload-button {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: $white;
    color: $ink-strong;
    font-size: 14px;
    cursor: pointer;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25);
    transition: background 0.12s ease, color 0.12s ease;
}

.nevs-photo-upload-button:hover {
    background: $primary;
    color: $white;
}

.nevs-photo-upload-button-delete:hover {
    background: $danger;
    color: $white;
}

.nevs-photo-upload-spinner {
    display: flex;
    align-items: center;
    justify-content: center;
    color: $white;
    font-size: 22px;
}
</style>
