<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref, onMounted, watch } from 'vue';
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

const search = ref('');
watch(search, (newValue, oldValue) => {
    searchItems();
});


const searchItems = () => {
    current_page = 1;
    last_page = 1;
    fetchItems();
};


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
    const searchQuery = search.value ? `&search=${search.value}` : '';
    axios.get(`${link}?${searchQuery}`)
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
    <AdminLayout title="Registered Users">
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
                            <div class="flex items-center flex-column flex-wrap md:flex-row justify-between pt-4">
                                <a :href="route('send_mail')"
                                    class="inline-flex justify-center items-center ml-4 py-3 px-5 text-base font-medium text-center text-white rounded-lg bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-900">
                                    Email Active Users
                                </a>
                                <div class="flex justify-end">
                                    <div class="pb-4 bg-white dark:bg-gray-900">
                                        <!-- <form @submit.prevent="searchItems"> -->
                                        <div class="flex items-center">
                                            <label for="table-search" class="sr-only">Search</label>
                                            <div class="relative mt-1 flex items-center">
                                                <div
                                                    class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none">
                                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                                        <path stroke="currentColor" stroke-linecap="round"
                                                            stroke-linejoin="round" stroke-width="2"
                                                            d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                                    </svg>
                                                </div>
                                                <input v-model="search" type="text" id="table-search"
                                                    class="block pt-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                                    placeholder="Search for users">
                                            </div>
                                            <!-- <PrimaryButton type="submit"
                                                    class="ml-2 p-2 bg-blue-500 text-white rounded-md">
                                                    Search
                                                </PrimaryButton> -->
                                        </div>
                                        <!-- </form> -->
                                    </div>
                                </div>
                            </div>

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
                                            <th scope="col" class="px-6 py-3">Active</th>

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
                                            <td class="text-center px-6 py-4">{{ item.is_active ? 'YES' : 'NO' }}</td>
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
    </AdminLayout>
</template>
