// axios
import axios from 'axios'
import store from './store/index'
import { API_URL } from "./config";

const domain = ""

axios.create({
    domain
});

axios.defaults.baseURL = API_URL

axios.interceptors.request.use(
  async config => {
    config.headers.Authorization = "Bearer "+localStorage.getItem('token_admin')
    return config
  },
  error => {
    return Promise.reject(error)
  }
);

axios.interceptors.response.use(
    (response) => {
        return response;
    },
    (error) => {
        // const { config, response: { status } } = error
        const { response } = error
        // if (status === 401) {
        if (response && response.status === 401 && localStorage.getItem('token_admin')) {
                // store.dispatch("authAdmin/fetchAccessToken")
                //     .then((response) => {
                //         isAlreadyFetchingAccessToken = false
                //         onAccessTokenFetched(response.data.data.access_token)
                //     })
        }
        return Promise.reject(error)
    })

export default axios;