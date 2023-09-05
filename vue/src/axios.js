import axios from "axios";
import { useUserStore } from './store/index';

const axiosClient = axios.create({
    baseURL: 'http://127.0.0.1:8000/api'
});

axiosClient.interceptors.request.use(config => {
    const userStore  = useUserStore();

    config.headers.Authorization = `Bearer ${userStore.user.token}`;
    return config;
})

export default axiosClient;
