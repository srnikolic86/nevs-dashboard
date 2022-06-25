export default {
    generateLocalBus: function () {
        return {
            events: [],
            ListenToEvent(name, action) {
                let index = null;
                for(let i=0; i<this.events.length; i++) {
                    if (this.events[i].name === name) {
                        index = i;
                    }
                }
                if (index === null) {
                    index = this.events.length;
                    this.events.push({
                        name: name,
                        actions: []
                    });
                }
                this.events[index].actions.push(action);
            },
            TriggerEvent(name, payload) {
                for (let listenedEvent of this.events) {
                    if (listenedEvent.name === name) {
                        for (let action of listenedEvent.actions) {
                            action(payload);
                        }
                    }
                }
            }
        }

    }
}