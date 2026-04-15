// plugins/axios.ts
import axios from 'axios'
import { defineNuxtPlugin } from '#app'

export default defineNuxtPlugin(() => {
    const api = axios.create({
        baseURL: 'http://localhost:8000/api', // backend Symfony
    })

    // Interceptor pour ajouter token JWT automatiquement
    api.interceptors.request.use(config => {
        const token = localStorage.getItem('token')
        if (token && config.headers) {
            config.headers.Authorization = `Bearer ${token}`
        }
        return config
    })

    return {
        provide: {
            api
        }
    }
})
