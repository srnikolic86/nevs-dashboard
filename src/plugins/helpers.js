export default {
    generateHelpers: function () {
        return {
            CheckEmail(email) {
                var re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                return re.test(email);
            },
            GetYesNoAllOptions(vm) {
                return [
                    {
                        label: vm.$LANG.Get('options.yes'),
                        value: 1
                    },
                    {
                        label: vm.$LANG.Get('options.no'),
                        value: 0
                    },
                    {
                        label: vm.$LANG.Get('options.all'),
                        value: -1
                    }
                ]
            }

        }

    }
}