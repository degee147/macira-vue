<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import axios from 'axios';

const form = useForm({
    message: '',
});

const submit = () => {

    const formData = new FormData();
    formData.append('message', form.message);
    axios.post(route('send_mail_post'), formData, {
        headers: {
            'Content-Type': 'multipart/form-data',
        },
    }).then(response => {
        alert(response.data.message);
        router.replace(route('admin_dashboard'));
    }).catch(error => {
        // Handle errors if necessary
        console.error(error);
        alert("Submission failed");
    });
};
</script>

<template>
    <AdminLayout title="Send Mail">

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Send Email
            </h2>
        </template>
        <div class="flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">

            <div class="w-full sm:max-w-3xl mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                <form @submit.prevent="submit">
                    <div>
                        <InputLabel for="message" value="Message" />
                        <!-- <TextInput id="message" v-model="form.message" type="text" class="mt-1 block w-full" required autofocus -->
                        <!-- autocomplete="message" /> -->
                        <textarea class="mt-1 block w-full" v-model="form.message" name="message" id="message" cols="30"
                            rows="5" required autofocus></textarea>
                        <InputError class="mt-2" :message="form.errors.message" />
                    </div>

                    <div class="flex items-center justify-end mt-4">

                        <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Send
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>


    </AdminLayout>
</template>
