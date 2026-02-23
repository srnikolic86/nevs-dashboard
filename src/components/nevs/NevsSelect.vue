<template>
    <div :style="wrapperStyle" class="nevs-field">
        <span v-if="label!== '' || reserveHeights" class="nevs-field-label">{{ label }}</span>
        <div class="nevs-select nevs-field-content" @click="dropdownClick">
            {{ selected.label }}
            <span v-if="selected.label === ''">&nbsp;</span>
            <span class="nevs-dropdown-icon"><i class="fa-solid fa-angle-down"></i></span>
            <div class="nevs-clear-float"></div>
        </div>
        <span v-if="(hint!== '' || reserveHeights) && showHint" class="nevs-field-hint">{{ hint }}</span>
        <div class="nevs-dropdown-holder">
            <Transition name="dropdown">
                <div v-show="showDropdown" class="nevs-dropdown-frame">
                    <div class="nevs-dropdown-search-container">
                        <input @focusout="toggleDropdown" ref="searchField" v-model="search"
                               class="nevs-dropdown-search"
                               type="text"/>
                    </div>
                    <div class="nevs-dropdown-options">
                        <div v-for="(option, key) in filteredOptions" :key="key"
                             class="nevs-dropdown-option" @click="selectOption(option)">
                            {{ option.label }}
                        </div>
                    </div>
                </div>
            </Transition>
        </div>
        <span v-if="(error!== '' || reserveHeights) && (!showHint || hint==='')" class="nevs-field-error">{{
                error
            }}</span>
    </div>
</template>

<script>

export default {
    name: "NevsSelect",
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
        options: Array,
        ajax: String,
        minimumSearchLength: Number,
        protected: [Number, String],
        nullable: Boolean,
        modelValue: [Number, String]
    },
    emits: [
        'update:modelValue'
    ],
    data() {
        return {
            showHint: false,
            selected: {
                label: '',
                value: null
            },
            allOptions: [],
            showDropdown: false,
            search: '',
            nullOption: {
                label: '---',
                value: null
            },
            crossTabReloadHandle: null
        }
    },
    computed: {
        wrapperStyle() {
            let style = {};
            if (this.width) {
                style.width = this.width;
            }
            return style;
        },
        filteredOptions() {
            if (this.minimumSearchLength) {
                if (this.search.length < this.minimumSearchLength) {
                    return [];
                }
            }

            let filtered = [];
            if (this.nullable) {
                filtered.push(this.nullOption);
            }
            for (let option of this.allOptions) {
                if (this.$HELPERS.ToCroatianLower(option.label).search(this.$HELPERS.ToCroatianLower(this.search)) !== -1) {
                    filtered.push(option);
                }
            }
            return filtered;
        }
    },
    watch: {
        modelValue() {
            if (this.modelValue !== this.selected.value) {
                this.setValue(this.modelValue);
            }
        }
    },
    methods: {
        setValue(value) {
            if (value === null && this.nullable) {
                this.selected = this.nullOption;
            } else {
                for (let option of this.allOptions) {
                    if (option.value === value) {
                        this.selected = option;
                    }
                }
            }
        },
        selectOption(option) {
            this.selected = option;
            this.$emit('update:modelValue', this.selected.value);
        },
        dropdownClick() {
            if (!this.readonly) {
                this.toggleDropdown();
                if (this.showDropdown) {
                    this.$nextTick(() => {
                        this.$refs.searchField.focus();
                    });
                }
            }
        },
        toggleDropdown() {
            this.showDropdown = !this.showDropdown;
            this.showHint = !this.showHint;
            if (this.showDropdown) {
                this.search = '';
            }
        },
        loadAPIOptions() {
            let vm = this;
            this.$API.APICall('get', this.ajax, {protected: this.protected}, (data, success) => {
                if (success) {
                    vm.allOptions = data;
                    vm.$nextTick(() => {
                        vm.setValue(vm.modelValue);
                    });
                }
            });
        }
    },
    mounted() {
        if (!this.ajax) {
            this.allOptions = this.options;
            this.setValue(this.modelValue);
        } else {
            this.loadAPIOptions();
        }
        let vm = this;
        this.crossTabReloadHandle = this.$CROSS_TAB_BUS.ListenToEvent('reload-data', () => {
            vm.loadAPIOptions();
        });
    },
    unmounted() {
        if (this.crossTabReloadHandle !== null) {
            this.$CROSS_TAB_BUS.UnbindEvent(this.crossTabReloadHandle);
        }
    }
}
</script>
