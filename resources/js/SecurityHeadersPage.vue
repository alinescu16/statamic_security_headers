<script setup>
import { onMounted } from 'vue';
import XFrameOptions from './security-headers/XFrameOptions.vue';
import XContentTypeOptions from './security-headers/XContentTypeOptions.vue';
import StrictTransportSecurity from './security-headers/StrictTransportSecurity.vue';
import ReferrerPolicy from './security-headers/ReferrerPolicy.vue';
import ContentSecurityPolicy from './security-headers/ContentSecurityPolicy.vue';
import PermissionsPolicy from './security-headers/PermissionsPolicy.vue';
import { useSecurityHeaders } from './composables/useSecurityHeaders';

const { settings, saveSettings, generatePolicies } = useSecurityHeaders();

onMounted(() => {
    const appElement = document.getElementById('security-headers-app');
    if (appElement && appElement.dataset.settings) {
        const loadedSettings = JSON.parse(appElement.dataset.settings);
        
        if (Object.keys(loadedSettings).length > 0) {
            settings.value = loadedSettings;
        }
    }
});
</script>

<style></style>

<template>
    <div>
        <header class="mb-3">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl">Security Headers</h1>
                <button @click="saveSettings" class="px-4 py-2 text-sm font-semibold text-white bg-blue-500 rounded-lg hover:bg-blue-600">Save Changes</button>
            </div>
        </header>

        <div class="p-4 card">
            <div class="space-y-6">
                <XFrameOptions v-model="settings.xFrameOptions" />
                <XContentTypeOptions v-model="settings.xContentTypeOptions" />
                <StrictTransportSecurity v-model="settings.strictTransportSecurity" />
                <ReferrerPolicy v-model="settings.referrerPolicy" />
                <ContentSecurityPolicy v-model="settings.contentSecurityPolicy" @generate-policies="generatePolicies" />
                <PermissionsPolicy v-model="settings.permissionsPolicy" />
            </div>
        </div>
    </div>
</template>

