<script setup>
import { defineProps, defineEmits, computed, onMounted } from 'vue';
import ToggleSwitch from '../components/ToggleSwitch.vue';

const props = defineProps({
    modelValue: Object
});

const emit = defineEmits(['update:modelValue', 'generate-policies']);

const settings = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
});

</script>

<template>
    <div>
        <div class="grid grid-cols-1 gap-2 md:grid-cols-3">
            <div class="flex md:col-span-1">
                <ToggleSwitch v-model="settings.enabled" />
                <div class="pl-4">
                    <h3 class="text-lg font-semibold">Content-Security-Policy</h3>
                    <p class="text-xs text-gray-600">Prevent XSS attacks by defining which dynamic resources are allowed to load.</p>
                </div>
            </div>

            <div class="pb-2 space-y-2 rounded-lg md:col-span-2" v-if="settings.enabled">
                <div>
                    <label for="csp-policy" class="block text-sm font-medium">Policy Directives</label>
                    <textarea id="csp-policy" v-model="settings.policy" rows="8" class="w-full mt-2 input-text"></textarea>
                </div>

                <label class="flex items-center mt-2 space-x-2">
                    <span class="mr-2">Report Only</span>
                    <ToggleSwitch v-model="settings.reportOnly" />
                </label>
            </div>
        </div>
        
        <div class="grid grid-cols-1 gap-2 mt-6 md:grid-cols-3">
            <div class="flex md:col-span-1">
                <ToggleSwitch v-model="settings.reportingEnabled" />
                <div class="pl-4">
                    <h3 class="text-lg font-semibold">Enable Content-Security-Policy Reporting</h3>
                    <p class="text-xs text-gray-600">Integrate your website with a reporting platform for sending reports and generating policies.</p>
                </div>
            </div>

            <div class="md:col-span-2" v-if="settings.reportingEnabled">
                <div class="pb-2 space-y-2 rounded-lg">
                    <label for="content_security_policy_reporting_platform" class="block text-sm font-medium">Reporting Platform</label>
                    <select id="content_security_policy_reporting_platform" v-model="settings.reportingPlatform.reportingPlatformName" class="w-full mt-2 form-select">
                        <option value="null">Select an option</option>
                        <option value="sentry">Sentry</option>
                        <option value="raygun" disabled>RayGun</option>
                        <option value="cside" disabled>c/Side</option>
                    </select>
                </div>

                <div v-if="settings.reportingPlatform.reportingPlatformName !== null">
                    <label for="content_security_policy_reporting_url" class="block mt-2 text-sm font-medium">Reporting URL</label>
                    <input id="content_security_policy_reporting_url" type="text" v-model="settings.reportingPlatform.reportingUrl" class="w-full mt-2 input-text">
                </div>

                <div v-if="settings.reportingPlatform.reportingPlatformName !== null">
                    <label for="content_security_policy_reporting_api_key" class="block mt-2 text-sm font-medium">Reporting API Key</label>
                    <input id="content_security_policy_reporting_api_key" type="text" v-model="settings.reportingPlatform.reportingApiKey" class="w-full mt-2 input-text">
                </div>

                <div v-if="settings.reportingPlatform.reportingPlatformName === 'sentry'">
                    <label for="content_security_policy_reporting_organization_id" class="block mt-2 text-sm font-medium">Reporting Organization ID</label>
                    <input id="content_security_policy_reporting_organization_id" type="text" v-model="settings.reportingPlatform.reportingOrganization" class="w-full mt-2 input-text">
                </div>

                <div v-if="settings.reportingPlatform.reportingPlatformName === 'sentry'">
                    <label for="content_security_policy_reporting_project_id" class="block mt-2 text-sm font-medium">Reporting Project ID</label>
                    <input id="content_security_policy_reporting_project_id" type="text" v-model="settings.reportingPlatform.reportingProject" class="w-full mt-2 input-text">
                </div>

                <div v-if="settings.reportingPlatform.reportingPlatformName == 'sentry'">
                    <button @click="$emit('generate-policies')" class="px-4 py-2 mt-4 text-sm font-semibold text-white bg-blue-500 rounded-lg hover:bg-blue-600" :disabled="!settings.reportingPlatform.reportingProject || !settings.reportingPlatform.reportingOrganization || !settings.reportingPlatform.reportingApiKey">Generate Policies</button>
                </div>
            </div>
        </div>
    </div>
</template>
