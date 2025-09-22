<script setup>
import { onMounted, computed, ref } from 'vue';
import XFrameOptions from './security-headers/XFrameOptions.vue';
import XContentTypeOptions from './security-headers/XContentTypeOptions.vue';
import StrictTransportSecurity from './security-headers/StrictTransportSecurity.vue';
import ReferrerPolicy from './security-headers/ReferrerPolicy.vue';
import ContentSecurityPolicy from './security-headers/ContentSecurityPolicy.vue';
import PermissionsPolicy from './security-headers/PermissionsPolicy.vue';
import { useSecurityHeaders } from './composables/useSecurityHeaders';

const { settings, saveSettings, generatePolicies } = useSecurityHeaders();

const defaultSettings = settings.value;

const grade = ref ({});

const gradeClass = computed(() => {
    if (!grade.value || !grade.value.grade) return '';

    return 'grade_' + grade.value.grade
        .toLowerCase()
        .replace('+', '_plus')
        .replace('-', '_minus');
});

onMounted(() => {
    let loadedSettings = {}; 

    const appElement = document.getElementById('security-headers-app');

    if (appElement && appElement.dataset.settings) {
        try {
            const parsedSettings = JSON.parse(appElement.dataset.settings);
            
            if (parsedSettings && typeof parsedSettings === 'object') {
                loadedSettings = parsedSettings;
            }
        } catch (e) {
            console.error('Failed to parse security settings:', e);
        }
    }

    if (appElement && appElement.dataset.grade) {
        try {
            const parsedGrade = JSON.parse(appElement.dataset.grade);

            if ( parsedGrade && typeof parsedGrade === 'object' ) {
                grade.value = parsedGrade;
            }
        } catch (e) {
            console.error('Failed to parse security grade:', e);
        }
    }

    settings.value = {
        ...defaultSettings,
        ...loadedSettings,
        xFrameOptions: { ...defaultSettings.xFrameOptions, ...loadedSettings.xFrameOptions },
        xContentTypeOptions: { ...defaultSettings.xContentTypeOptions, ...loadedSettings.xContentTypeOptions },
        strictTransportSecurity: { ...defaultSettings.strictTransportSecurity, ...loadedSettings.strictTransportSecurity },
        referrerPolicy: { ...defaultSettings.referrerPolicy, ...loadedSettings.referrerPolicy },
        contentSecurityPolicy: { ...defaultSettings.contentSecurityPolicy, ...loadedSettings.contentSecurityPolicy },
        permissionsPolicy: { ...defaultSettings.permissionsPolicy, ...loadedSettings.permissionsPolicy },
    };
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
            <div class="grade">
                <a data-tooltip="View Report Details" :href="grade.details_url" target="_blank">
                    <span class="inline-block px-4 py-2 text-sm font-semibold text-white rounded-lg grade" :class="gradeClass">{{ grade.grade }}</span>
                </a>
                <div>
                    <p>HTTP Score: {{ grade.score }} / 100</p>
                    <span class="powered_by">by <a href="https://observatory-api.mdn.mozilla.net/" target="_blank">MDN Observatory</a></span>
                </div>
            </div>

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

