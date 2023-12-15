<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Link, router, useForm } from '@inertiajs/vue3';

const logout = () => {
    router.post(route('logout'));
};

</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Hi.. {{ $page.props.auth.user.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto p-6 lg:p-8">
                <section class="bg-white dark:bg-gray-900">
                    <div class="py-8 px-4 mx-auto max-w-screen-xl text-center lg:py-16">
                        <h1
                            class="mb-4 text-4xl font-extrabold tracking-tight leading-none text-gray-900 md:text-5xl lg:text-6xl dark:text-white">
                            Quick Links</h1>
                        <!-- <p class="mb-8 text-lg font-normal text-gray-500 lg:text-xl sm:px-16 lg:px-48 dark:text-gray-400"> -->
                        <!-- A Laravel multi auth (admin, users), with CSV import and remote api integration. Frontend -->
                        <!-- implemented using inertia with vue js</p> -->
                        <div class="flex flex-col space-y-4 sm:flex-row sm:justify-center sm:space-y-0">
                            <a :href="route('profile.show')"
                                class="inline-flex justify-center items-center py-3 px-5 text-base font-medium text-center text-white rounded-lg bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-900">
                                Profile

                            </a>


                            <SecondaryButton class="ml-4 mt-2 me-2" type="button" @click.prevent="logout">
                                Logout
                            </SecondaryButton>

                        </div>
                    </div>
                </section>

            </div>
        </div>
    </AppLayout>
</template>
