var domain = window.location.hostname == 'http://localhost' ? 'http://localhost' : window.location.origin
export const API_URL = domain+'/api';
export default API_URL;
