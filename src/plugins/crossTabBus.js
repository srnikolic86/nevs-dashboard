export default {
    generateCrossTabBus: function () {
        return {
            events: [],
            Init() {
                window.addEventListener('storage', (event) => {
                    if (event.storageArea !== localStorage) return;
                    if (event.newValue === '') return;
                    for (let listenedEvent of this.events) {
                        if (event.key === 'nevs-crosstab-' + listenedEvent.name) {
                            for (let action of listenedEvent.actions) {
                                action(JSON.parse(event.newValue));
                            }
                        }
                    }
                });
            },
            ListenToEvent(name, action) {
                window.localStorage.setItem('nevs-crosstab-' + name, '');
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
                window.localStorage.setItem('nevs-crosstab-' + name, JSON.stringify(payload));
                for (let listenedEvent of this.events) {
                    if (listenedEvent.name === name) {
                        for (let action of listenedEvent.actions) {
                            action(payload);
                        }
                    }
                }
                window.localStorage.setItem('nevs-crosstab-' + name, '');
            }
        }

    }
}