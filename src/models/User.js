class User {
    id = null;
    first_name = '';
    last_name = '';
    email = '';
    active = true;
    permissions = [];
    password = '';

    constructor(data, validation = false) {
        for (const [key, value] of Object.entries(data)) {
            this[key] = value;
        }

        if (validation) {
            for (const [key] of Object.entries(this)) {
                this[key] = '';
            }
        }
    }

    Fill(data) {
        for (const [key] of Object.entries(this)) {
            if (data[key] !== undefined) {
                this[key] = data[key];
            }
        }
    }
}

export default User;