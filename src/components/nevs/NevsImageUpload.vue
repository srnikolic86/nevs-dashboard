<template>
    <div :class="{'nevs-image-upload': true, 'has-image': hasImage, 'uploading': uploadInProgress}"
         :style="sizeStyle">
        <input ref="fileInput" :accept="accepted" class="nevs-image-upload-input" type="file"
               @change="fileChange"/>

        <img v-show="hasImage" :src="hasImage ? fileData.link : undefined" alt=""
             class="nevs-image-upload-image"/>
        <span v-show="!hasImage" class="nevs-image-upload-placeholder">
            <i class="fa-solid fa-image"></i>
        </span>

        <div v-if="!readonly" class="nevs-image-upload-overlay">
            <span v-show="uploadInProgress" class="nevs-image-upload-spinner">
                <i class="fa-solid fa-spinner fa-spin"></i>
            </span>
            <span v-show="!uploadInProgress"
                  :title="hasImage ? $LANG.Get('tooltips.changePhoto') : $LANG.Get('tooltips.uploadPhoto')"
                  class="nevs-image-upload-button" @click="uploadClick">
                <i class="fa-solid fa-upload"></i>
            </span>
            <span v-show="!uploadInProgress && hasImage" :title="$LANG.Get('tooltips.delete')"
                  class="nevs-image-upload-button nevs-image-upload-button-delete" @click="deleteClick">
                <i class="fa-solid fa-trash"></i>
            </span>
        </div>
    </div>
</template>

<script>
/**
 * One image against a record, as the standard upload object {name, link, id} - the empty state being
 * id 0, which a server typically stores as null.
 *
 * The square counterpart of NevsPhotoUpload: that one frames a face and crops to fill its circle, this
 * one frames a thing and shows it whole.
 */
export default {
    name: "NevsImageUpload",
    props: {
        size: {
            type: String,
            default: '190px'
        },
        accept: {
            type: String,
            default: 'image/*'
        },
        // Shows the picture and nothing to change it with: no upload button, no delete button, and the
        // file dialog cannot be reached at all.
        readonly: {
            type: Boolean,
            default: false
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
        // The file is uploaded the moment it is picked; what the caller saves is the reference to it.
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

.nevs-image-upload {
    position: relative;
    flex: 0 0 auto;
    border-radius: 6px;
    overflow: hidden;
    background: $sand-100;
    border: 1px solid $sand-300;
    display: flex;
    align-items: center;
    justify-content: center;
}

.nevs-image-upload-input {
    display: none;
}

// `contain`, not `cover`: a thing photographed against a background is shown whole rather than cropped
// to fill the square.
.nevs-image-upload-image {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.nevs-image-upload-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 46px;
    color: $ink-muted;
}

.nevs-image-upload-overlay {
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
.nevs-image-upload:not(.has-image) .nevs-image-upload-overlay {
    background: transparent;
    opacity: 1;
    align-items: flex-end;
    padding-bottom: 10px;
}

.nevs-image-upload:hover .nevs-image-upload-overlay {
    opacity: 1;
}

// While uploading, cover the square with a scrim and the centred spinner, regardless of hover state.
.nevs-image-upload.uploading .nevs-image-upload-overlay {
    opacity: 1;
    background: rgba(0, 0, 0, 0.45);
    align-items: center;
    padding-bottom: 0;
}

.nevs-image-upload-button {
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

.nevs-image-upload-button:hover {
    background: $primary;
    color: $white;
}

.nevs-image-upload-button-delete:hover {
    background: $danger;
    color: $white;
}

.nevs-image-upload-spinner {
    display: flex;
    align-items: center;
    justify-content: center;
    color: $white;
    font-size: 22px;
}
</style>
