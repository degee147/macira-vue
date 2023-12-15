<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Pagination from '@/components/Pagination.vue';


let items = ref([]);
let current_page = ref(1);
let last_page = ref(1);
let per_page = ref(1);
let total = ref(7);
let to = ref(7);
let from = ref(7);
let links = ref([]);


const handleLinkClick = (link) => {
    if (link.url) {
        fetchItems(link.url);
    }
}
const fetchItems = (url = null) => {
    // Make an API request to fetch paginated items
    let link = route('admin_users_data');
    if (url) {
        link = url;
    }
    axios.get(link)
        .then(response => {
            // console.log("response", response);
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
<style scoped>
.parent-container {
    position: relative;
    width: 30px;
    height: 20px;
}

.centered-image {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    max-width: 100%;
    /* Ensure the image doesn't exceed the container's width */
    max-height: 100%;
    /* Ensure the image doesn't exceed the container's height */
}
</style>

<template>
    <AdminLayout title="Admin Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Registered Users
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div>
                        <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                            <div class="relative overflow-x-auto shadow-md sm:rounded-lg p-4">
                                <pagination :from="from" :to="to" :total="total" :current_page="current_page" :links="links"
                                    @link-clicked="handleLinkClick" />
                                <br>
                                <table style="width:100%;">
                                    <thead
                                        class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 full-width">
                                        <tr>

                                            <th scope="col" class="px-6 py-3">Id</th>
                                            <th scope="col" class="px-6 py-3">Photo</th>
                                            <th scope="col" class="px-6 py-3">Name</th>
                                            <th scope="col" class="px-6 py-3">Username</th>
                                            <th scope="col" class="px-6 py-3">Email</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <div v-if="items.length === 0"
                                            class="text-center py-4 text-gray-500 dark:text-gray-400">
                                            No items available.
                                        </div>
                                        <tr v-for="item in items"
                                            class="text-center bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                            <td scope="row"
                                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                {{ item.id }}</td>
                                            <td class="text-center px-6 py-4 parent-container">
                                                <img class="img-fluid h-8 w-8 rounded-full object-cover centered-image"
                                                    :src="item.profile_photo_url" :alt="item.name">
                                            </td>
                                            <td class="text-center px-6 py-4">{{ item.name }}</td>
                                            <td class="text-center px-6 py-4">{{ item.username }}</td>
                                            <td class="text-center px-6 py-4">{{ item.email }}</td>
                                        </tr>
                                    </tbody>
                                </table>

                                <br>
                                <pagination :from="from" :to="to" :total="total" :current_page="current_page" :links="links"
                                    @link-clicked="handleLinkClick" />

                                <br>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</AdminLayout></template>
