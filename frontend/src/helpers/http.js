import axios from "axios"

const http = () => {
    let options = {
        baseURL: `${import.meta.env.VITE_API_KEY_URL}`,
        headers: {}
    }

    if (localStorage.getItem('token')) {
        options.headers.Authorization = `Bearer ${localStorage.getItem('token')}`
    }

    return axios.create(options)
}

export default http