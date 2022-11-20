export default {
    generateHelpers: function () {
        return {
            GetCookie(cname) {
                var name = cname + "=";
                var decodedCookie = decodeURIComponent(document.cookie);
                var ca = decodedCookie.split(';');
                for (var i = 0; i < ca.length; i++) {
                    var c = ca[i];
                    while (c.charAt(0) == ' ') {
                        c = c.substring(1);
                    }
                    if (c.indexOf(name) == 0) {
                        return c.substring(name.length, c.length);
                    }
                }
                return "";
            },
            SetCookie(cname, cvalue, exdays) {
                var d = new Date();
                d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
                var expires = "expires=" + d.toUTCString();
                document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
            },
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
            },
            ToCroatianLower(s) {
                let swaps = {
                    'c': ['č', 'Č', 'ć', 'Ć'],
                    'd': ['đ', 'Đ'],
                    's': ['š', 'Š'],
                    'z': ['ž', 'Ž']
                }
                for (let to in swaps) {
                    for (let from of swaps[to]) {
                        s = s.split(from).join(to);
                    }
                }
                s = s.toLowerCase();
                return s;
            }

        }

    }
}