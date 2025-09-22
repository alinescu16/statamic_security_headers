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
                <h3 class="text-lg font-semibold">X-Content-Type-Options</h3>
                <p class="text-xs text-gray-600">Prevents the browser from MIME-sniffing a response away from the declared content-type.</p>
            </div>
        </div>
    </div>
</template>
