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
                <h3 class="text-lg font-semibold">Permissions-Policy</h3>
                <p class="text-xs text-gray-600">Control which browser features can be used on the site.</p>
            </div>
        </div>
        <div class="pb-2 space-y-2 rounded-lg md:col-span-2" v-if="settings.enabled">
            <label for="permissions-policy" class="block text-sm font-medium">Policy Directives</label>
            <textarea id="permissions-policy" v-model="settings.policy" rows="8" class="w-full mt-2 input-text"></textarea>
        </div>
    </div>
</template>
