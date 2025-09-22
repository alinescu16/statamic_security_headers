<script setup>
import { defineProps, defineEmits, computed } from 'vue';
import ToggleSwitch from '../components/ToggleSwitch.vue';

const props = defineProps({
    modelValue: Object
});

const emit = defineEmits(['update:modelValue']);

const settings = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
});
</script>

<template>
    <div class="grid grid-cols-1 gap-2 md:grid-cols-3">
        <div class="flex md:col-span-1">
             <ToggleSwitch v-model="settings.enabled" />
            <div class="pl-4">
                <h3 class="text-lg font-semibold">Strict-Transport-Security (HSTS)</h3>
                <p class="text-xs text-gray-600">Enforces the use of HTTPS, preventing protocol downgrade attacks.</p>
            </div>
        </div>
        <div class="pb-2 space-y-4 rounded-lg md:col-span-2" v-if="settings.enabled">
            <div>
                <label for="strict_transport_security_policy_max_age" class="block text-sm font-medium">Max Age (years)</label>
                <input id="strict_transport_security_policy_max_age" type="text" v-model.number="settings.maxAge" class="w-full mt-2 input-text">
            </div>

            <label class="flex items-center mt-2 space-x-2">
                <span class="mr-2">Include Subdomains</span>
                 <ToggleSwitch v-model="settings.includeSubDomains" />
            </label>
            
            <label class="flex items-center mt-2 space-x-2">
                <span class="mr-2">Preload</span>
                 <ToggleSwitch v-model="settings.preload" />
            </label>
        </div>
    </div>
</template>
