<template>
    <div :style="wrapperStyle" class="nevs-field">
        <div class="nevs-select nevs-actions nevs-field-content" ref="fieldContent" @click="toggleDropdown">
            <span v-if="!showDropdown">{{ $LANG.Get('labels.chooseAction') }}</span>
            <span v-if="showDropdown">{{ $LANG.Get('labels.cancel') }}</span>
        </div>
        <!-- The dropdown is teleported to <body> and positioned via fixed coordinates so it is never clipped
             by a scrollable ancestor (e.g. a capped modal). -->
        <Teleport to="body">
            <Transition name="dropdown">
                <div v-show="showDropdown" ref="dropdown"
                     class="nevs-dropdown-frame nevs-dropdown-frame-floating nevs-actions-frame" :style="dropdownStyle">
                    <div class="nevs-dropdown-options" :style="optionsStyle">
                        <div v-for="(action, key) in filteredActions" :key="key"
                             class="nevs-dropdown-option" @click="selectAction(action)">
                            {{ action.label }}
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
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
            ignoreNextListener: false,
            dropdownStyle: {},
            optionsStyle: {}
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
                this.updateDropdownPosition();
                document.addEventListener('click', this.handleClickOutside);
                // Keep the teleported dropdown aligned with the field while it is open.
                window.addEventListener('scroll', this.updateDropdownPosition, true);
                window.addEventListener('resize', this.updateDropdownPosition);
            } else {
                this.detachListeners();
            }
        },
        detachListeners() {
            document.removeEventListener('click', this.handleClickOutside);
            window.removeEventListener('scroll', this.updateDropdownPosition, true);
            window.removeEventListener('resize', this.updateDropdownPosition);
        },
        updateDropdownPosition() {
            if (!this.$refs.fieldContent) return;
            let rect = this.$refs.fieldContent.getBoundingClientRect();

            // The dropdown is fixed-positioned (teleported to <body>), so it can't grow the page scroll.
            // Keep it inside the viewport: open upward when there isn't enough room below, and cap the
            // options list to the space actually available so the whole dropdown always fits on screen.
            const margin = 8;
            const spaceBelow = window.innerHeight - rect.bottom - margin;
            const spaceAbove = rect.top - margin;
            const openUp = spaceBelow < 200 && spaceAbove > spaceBelow;
            const available = openUp ? spaceAbove : spaceBelow;

            let style = {
                position: 'fixed',
                left: rect.left + 'px',
                width: rect.width + 'px',
                zIndex: 200
            };
            if (openUp) {
                // Reset the base rule's `top: 0`, otherwise the frame stretches from the top of the
                // screen down to the field and fills the whole height.
                style.top = 'auto';
                style.bottom = (window.innerHeight - rect.top) + 'px';
            } else {
                style.top = rect.bottom + 'px';
                style.bottom = 'auto';
            }
            this.dropdownStyle = style;

            let optionsMax = Math.max(available, 80);
            if (optionsMax > 300) optionsMax = 300;
            this.optionsStyle = {maxHeight: optionsMax + 'px'};
        },
        handleClickOutside(event) {
            if (this.ignoreNextListener) {
                this.ignoreNextListener = false;
                return;
            }
            // The dropdown is teleported to <body>, so "outside" must also exclude the dropdown itself.
            let insideField = this.$el.contains(event.target);
            let insideDropdown = this.$refs.dropdown && this.$refs.dropdown.contains(event.target);
            if (!insideField && !insideDropdown) {
                this.showDropdown = false;
                this.detachListeners();
            }
        },
    },
    mounted() {

    },
    unmounted() {
        this.detachListeners();
    }
}
</script>
