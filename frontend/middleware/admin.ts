export default defineNuxtRouteMiddleware(() => {

    const role=localStorage.getItem('user_role')
    if (!role?.includes("ROLE_ADMIN")) {
        return navigateTo('/')
    }
})