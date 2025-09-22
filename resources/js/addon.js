import { createApp } from 'vue';

import SecurityHeadersPage from './SecurityHeadersPage.vue';

document.addEventListener('DOMContentLoaded', () => {
    const app = createApp(SecurityHeadersPage);
    
    app.mount('#security-headers-app');
});