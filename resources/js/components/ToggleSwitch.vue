<script setup>
import { defineProps, defineEmits, computed } from 'vue';

const props = defineProps({
    modelValue: Boolean
});

const emit = defineEmits(['update:modelValue']);

const isEnabled = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
});

const toggle = () => {
    isEnabled.value = !isEnabled.value;
};
</script>

<template>
    <label class="flex items-center space-x-2">
        <div class="toggle-fieldtype-wrapper">
            <button type="button" @click="toggle" :aria-pressed="isEnabled" aria-label="Toggle Button" class="toggle-container" :class="isEnabled ? 'on' : ''">
                <div class="toggle-slider">
                    <div tabindex="0" class="toggle-knob"></div>
                </div>
            </button>
        </div>
        <input type="checkbox" v-model="isEnabled" class="hidden form-checkbox" />
    </label>
</template>
