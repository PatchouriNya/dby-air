import axios from 'axios'
const instance = axios.create({
    baseUrl:import.meta.env.BASE_URL
});

instance.defaults.timeout = 3000;



export default instance