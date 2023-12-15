<script setup>
import { onMounted, ref } from 'vue';

defineProps({
    modelValue: String,
});

defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });

const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = () => {
            const dataUrl = reader.result;
            // console.log(dataUrl);
            // You can handle the file or dataUrl as needed
            // $emit('update:modelValue', dataUrl);
        };
        reader.readAsDataURL(file);
    }
};
</script>

<template>
    <input
        ref="input"
        type="file"
        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
        @change="handleFileChange"
    >
</template>
