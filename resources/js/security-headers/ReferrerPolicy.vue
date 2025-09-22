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
                <h3 class="text-lg font-semibold">Referrer-Policy</h3>
                <p class="text-xs text-gray-600">Control how much referrer information is included with requests.</p>
            </div>
        </div>
        <div class="pb-2 space-y-2 rounded-lg md:col-span-2" v-if="settings.enabled">
            <label for="referrer-policy-value" class="block text-sm font-medium">Value</label>
            <select id="referrer-policy-value" v-model="settings.value" class="w-full mt-2 form-select">
                <option value="null">Select an option</option>
                <option value="no-referrer">No Referrer</option>
                <option value="no-referrer-when-downgrade">No Referrer When Downgrade</option>
                <option value="origin">Origin</option>
                <option value="origin-when-cross-origin">Origin When Cross Origin</option>
                <option value="same-origin">Same Origin</option>
                <option value="strict-origin">Strict Origin</option>
                <option value="strict-origin-when-cross-origin">Strict Origin When Cross Origin</option>
                <option value="unsafe-url">Unsafe Url</option>
            </select>
        </div>
    </div>
</template>
