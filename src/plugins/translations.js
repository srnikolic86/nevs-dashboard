import hr from "@/languages/hr.json";
import en from "@/languages/en.json";

export default {
    generateTranslations: function (store) {
        return {
            languages: {
                hr: hr,
                en: en
            },
            store: store,
            Get(key) {
                if (this.languages[this.store.state.locale] === undefined) return key;
                let keys = key.split('.');
                let value = this.languages[this.store.state.locale];
                for (let keyPart of keys) {
                    if (value[keyPart] === undefined) return key;
                    value = value[keyPart];
                }
                return value;
            }
        }
    }
}