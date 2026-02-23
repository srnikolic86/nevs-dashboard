import ModuleHome from "@/components/modules/ModuleHome.vue";
import ModuleUsers from "@/components/modules/ModuleUsers.vue";
import EntityUser from "@/components/entities/EntityUser.vue";

export default [
    { path: '/', component: ModuleHome },
    { path: '/users', component: ModuleUsers },
    { path: '/users/:id', component: EntityUser },
]
