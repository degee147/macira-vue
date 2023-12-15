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
let current_page = ref(1);
let last_page = ref(1);
let per_page = ref(1);
let total = ref(7);
let to = ref(7);
let from = ref(7);
let links = ref([]);

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

const handleLinkClick = (link) => {
    console.log("link", link);
    if (link.url) {
        fetchItems(link.url);
    }
}
const fetchItems = (url = null) => {
    // Make an API request to fetch paginated items
    let link = route('admin_csv_data');
    if (url) {
        link = url;
    }
    axios.get(link)
        .then(response => {
            console.log("response", response);
            items.value = response.data.data;
            current_page = response.data.current_page;
            last_page = response.data.last_page;
            per_page = response.data.per_page;
            total = response.data.total;
            links = response.data.links;
            from = response.data.from;
            to = response.data.to;



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

                                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                                    <!-- <div class="pb-4 bg-white dark:bg-gray-900">
                                        <label for="table-search" class="sr-only">Search</label>
                                        <div class="relative mt-1">
                                            <div
                                                class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none">
                                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                                    <path stroke="currentColor" stroke-linecap="round"
                                                        stroke-linejoin="round" stroke-width="2"
                                                        d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                                </svg>
                                            </div>
                                            <input type="text" id="table-search"
                                                class="block pt-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                                placeholder="Search for items">
                                        </div>
                                    </div> -->
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

                                    <br>
                                    <nav class="flex items-center flex-column flex-wrap md:flex-row justify-between pt-4"
                                        aria-label="Table navigation">
                                        <span
                                            class="text-sm font-normal text-gray-500 dark:text-gray-400 mb-4 md:mb-0 block w-full md:inline md:w-auto">Showing
                                            <span class="font-semibold text-gray-900 dark:text-white">{{ from }}-{{ to
                                            }}</span> of
                                            <span class="font-semibold text-gray-900 dark:text-white">{{ total
                                            }}</span></span>
                                        <ul class="inline-flex -space-x-px rtl:space-x-reverse text-sm h-8">
                                            <li v-for="link in links">

                                                <a v-if="parseInt(link.label) === current_page" :href="link.url"
                                                    @click.prevent="handleLinkClick(link)" aria-current="page"
                                                    class="flex items-center justify-center px-3 h-8 text-blue-600 border border-gray-300 bg-blue-50 hover:bg-blue-100 hover:text-blue-700 dark:border-gray-700 dark:bg-gray-700 dark:text-white">
                                                    {{ link.label.replace(/&[^;]+;/g, '') }}</a>

                                                <a v-else href="#" @click.prevent="handleLinkClick(link)"
                                                    class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">{{
                                                        link.label.replace(/&[^;]+;/g, '') }}</a>


                                            </li>

                                        </ul>
                                    </nav>
                                    <br>


                                </div>
                                <!-- <template> -->
                                <!-- <div class="pagination-container">
                                    <h1>Hello</h1>
                                    <button v-if="current_page > 1" @click="changePage(current_page - 1)"
                                        class="pagination-button">
                                        Previous
                                    </button>

                                    <button v-for="pageNumber in pages" :key="pageNumber" @click="changePage(pageNumber)"
                                        :class="{ 'pagination-button-active': pageNumber === current_page, 'pagination-button': pageNumber !== current_page }">
                                        {{ pageNumber }}
                                    </button>

                                    <button v-if="current_page < totalPages" @click="changePage(current_page + 1)"
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
    </AdminLayout></template>
