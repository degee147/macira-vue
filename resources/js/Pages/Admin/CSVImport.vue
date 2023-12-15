<script setup>
import { ref, onMounted } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import FileInput from '@/Components/FileInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { TailwindPagination } from 'laravel-vue-pagination';
import axios from 'axios';

const form = useForm({
    csv: '',
});

const dataUrl = ref('');
let items = ref([]);
let currentPage = 1;
const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = () => {
            const data = reader.result;
            dataUrl.value = data;
            // console.log(dataUrl);
            // You can handle the file or dataUrl as needed
            // $emit('update:modelValue', dataUrl);
        };
        reader.readAsDataURL(file);
    }
};


const submit = () => {
    const input = document.getElementById('csv');
    if (dataUrl.value) {
        const formData = new FormData();
        formData.append('csv', dataUrl.value);

        axios.post(route('admin_csv_upload'), formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        }).then(response => {
            dataUrl.value = '';
            input.value = '';
            fetchItems();
            alert(response.data.message);
        }).catch(error => {
            // Handle errors if necessary
            console.error(error);
            alert("Submission failed");
        });
    } else {
        alert("Please provide a file to upload");
    }
};

const fetchItems = (page = 1) => {
    // Make an API request to fetch paginated items
    axios.get(route('admin_csv_data') + `?page=${page}`)
        .then(response => {
            items.value = response.data.data;
            console.log("items", items);
            currentPage = response.data.current_page;
        })
        .catch(error => {
            console.error('Error fetching items', error);
        });
};

// Call fetchItems when the component is mounted
onMounted(() => {
    fetchItems();
});

</script>

<template>
    <AdminLayout title="Admin Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                CSV Import
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                    <div>
                        <div class="p-6 lg:p-8 bg-white border-b border-gray-200">

                            <h1 class="mt-8 text-2xl font-medium text-gray-900">
                                Select a CSV file
                            </h1>

                            <div>

                                <form @submit.prevent="submit">
                                    <div>
                                        <input id="csv" v-on:change="handleFileChange" type="file"
                                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <InputError class="mt-2" :message="form.errors.csv" />

                                        <PrimaryButton type="submit" class="mt-4 p-2 bg-blue-500 text-white rounded-md">
                                            Upload CSV</PrimaryButton>

                                        <!-- <Link :href="route('admin_csv_sample')"
                                            class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">

                                        </Link> -->

                                        <a :href="route('admin_csv_sample')"
                                            class="mt-4 ml-4 p-2 bg-blue-500 text-white rounded-md"> Download Sample </a>


                                    </div>

                                    <!-- <p v-if="dataUrl">Data URL: {{ dataUrl }}</p> -->

                                </form>
                            </div>

                            <h1 class="mt-8 text-2xl font-medium text-gray-900">
                                Items
                            </h1>
                            <br>
                            <!-- <template> -->
                            <div>
                                <div class="relative overflow-x-auto">
                                    <table>
                                        <thead
                                            class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                            <tr>

                                                <th scope="col" class="px-6 py-3">Id</th>
                                                <th scope="col" class="px-6 py-3">Name</th>
                                                <th scope="col" class="px-6 py-3">Email</th>
                                                <th scope="col" class="px-6 py-3">Phone</th>
                                                <th scope="col" class="px-6 py-3">Address</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="item in items"
                                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                                <td scope="row"
                                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                    {{ item.id }}</td>
                                                <td class="px-6 py-4">{{ item.name }}</td>
                                                <td class="px-6 py-4">{{ item.email }}</td>
                                                <td class="px-6 py-4">{{ item.phone }}</td>
                                                <td class="px-6 py-4">{{ item.address }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- <template> -->
                                <!-- <div class="pagination-container">
                                    <h1>Hello</h1>
                                    <button v-if="currentPage > 1" @click="changePage(currentPage - 1)"
                                        class="pagination-button">
                                        Previous
                                    </button>

                                    <button v-for="pageNumber in pages" :key="pageNumber" @click="changePage(pageNumber)"
                                        :class="{ 'pagination-button-active': pageNumber === currentPage, 'pagination-button': pageNumber !== currentPage }">
                                        {{ pageNumber }}
                                    </button>

                                    <button v-if="currentPage < totalPages" @click="changePage(currentPage + 1)"
                                        class="pagination-button">
                                        Next
                                    </button>
                                </div> -->



                                <!-- </template> -->
                                <!-- Pagination -->
                                <!-- <LaravelVuePagination :data="items" @pagination-change-page="fetchData"> -->
                                <!-- </LaravelVuePagination> -->
                                <!-- <template> -->
                                <!-- <TailwindPagination :data="items" @pagination-change-page="fetchItems" /> -->
                                <!-- </template> -->
                                <!-- <table> -->
                                <!-- Display your table headers here -->

                                <!-- <tbody> -->
                                <!-- <tr v-for="item in items" :key="item.id"> -->
                                <!-- Display your table rows here -->
                                <!-- </tr> -->
                                <!-- </tbody> -->
                                <!-- </table> -->

                                <!-- <pagination :data="items" @pagination-change-page="fetchItems"></pagination> -->
                            </div>
                            <!-- </template> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
