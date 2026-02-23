<template>
    <div :style="wrapperStyle" class="nevs-field">
        <div class="nevs-select nevs-actions nevs-field-content" @click="toggleDropdown">
            <span v-if="!showDropdown">{{ $LANG.Get('labels.chooseAction') }}</span>
            <span v-if="showDropdown">{{ $LANG.Get('labels.cancel') }}</span>
        </div>
        <div class="nevs-dropdown-holder nevs-actions-holder">
            <Transition name="dropdown">
                <div v-show="showDropdown" class="nevs-dropdown-frame">
                    <div class="nevs-dropdown-options">
                        <div v-for="(action, key) in filteredActions" :key="key"
                             class="nevs-dropdown-option" @click="selectAction(action)">
                            {{ action.label }}
                        </div>
                    </div>
                </div>
            </Transition>
        </div>
    </div>
</template>

<script>

export default {
    name: "NevsActions",
    props: {
        width: {
            type: String,
            default: '100%'
        },
        actions: Array,
        context: Object
    },
    data() {
        return {
            showDropdown: false,
            ignoreNextListener: false
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
        filteredActions() {
            let filtered = [];
            for (let action of this.actions) {
                if (action.visible(this.context)) {
                    filtered.push(action);
                }
            }
            return filtered;
        }
    },
    methods: {
        selectAction(action) {
            action.action(this.context);
            this.toggleDropdown();
        },
        toggleDropdown() {
            this.ignoreNextListener = true;
            this.showDropdown = !this.showDropdown;
            if (this.showDropdown) {
                document.addEventListener('click', this.handleClickOutside);
            } else {
                document.removeEventListener('click', this.handleClickOutside);
            }
        },
        handleClickOutside(event) {
            if (this.ignoreNextListener) {
                this.ignoreNextListener = false;
                return;
            }
            if (!this.$el.contains(event.target)) {
                this.showDropdown = false;
                document.removeEventListener('click', this.handleClickOutside);
            }
        },
    },
    mounted() {

    },
    unmounted() {
        document.removeEventListener('click', this.handleClickOutside);
    }
}
</script>
