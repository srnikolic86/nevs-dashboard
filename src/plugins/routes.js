import ModuleHome from "@/components/modules/ModuleHome";
import ModuleUsers from "@/components/modules/ModuleUsers";
import EntityUser from "@/components/entities/EntityUser";

export default [
    { path: '/', component: ModuleHome },
    { path: '/users', component: ModuleUsers },
    { path: '/users/:id', component: EntityUser },
]