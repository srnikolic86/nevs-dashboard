import Settings from "@/config.json";

// Switching dark mode on and off for the whole page, and remembering it on this device. The page itself is
// restyled by scss/nevs/dark-mode.scss off the class below; the switch lives in NevsTopBar.

// Class on <html> that switches the whole page over.
const DARK_MODE_CLASS = 'nevs-dark';
// localStorage key with the last choice made on this device ('1' or '0'). The per-user choice is kept in
// user_data; this copy is what the login screen goes by, before anyone is logged in.
const STORAGE_KEY = 'nevs-dark-mode';

export default {
    // ALLOW_DARK_MODE in config.json turns dark mode off for the whole app. Only an explicit false does, so a
    // config written before the setting existed keeps it.
    Allowed() {
        return Settings.ALLOW_DARK_MODE !== false;
    },
    Apply(on) {
        document.documentElement.classList.toggle(DARK_MODE_CLASS, this.Allowed() && on);
    },
    // null when this device has no choice on record. Storage can be unavailable (private windows, blocked site
    // data), in which case there is simply nothing remembered.
    Stored() {
        try {
            let value = window.localStorage.getItem(STORAGE_KEY);
            return value === null ? null : value === '1';
        } catch {
            return null;
        }
    },
    Store(on) {
        try {
            window.localStorage.setItem(STORAGE_KEY, on ? '1' : '0');
        } catch {
            // Not remembered on this device; the user_data copy still is.
        }
    },
    // Called once at startup, before the app mounts, so the login screen and the first paint after it are
    // already in the right mode.
    ApplyStored() {
        this.Apply(this.Stored() === true);
    }
}
