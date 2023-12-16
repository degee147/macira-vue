<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Pagination from '@/components/Pagination.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';


let loading = false;
let items = ref([]);
let current_page = ref(1);
let last_page = ref(1);
let per_page = ref(1);
let total = ref(7);
let to = ref(7);
let from = ref(7);
let links = ref([]);


const loadData = () => {
    loading = true;
    axios.get(route('admin_api_data_load'))
        .then(response => {
            fetchItems();
            alert(response.data.message);
        }).catch(error => {
            console.error('Error fetching items', error);
            loading = false;
        }).finally(() => {
            loading = false; // Set loading to false, whether request succeeds or fails
        });
}

const handleLinkClick = (link) => {
    if (link.url) {
        fetchItems(link.url);
    }
}
const fetchItems = (url = null) => {
    // Make an API request to fetch paginated items
    let link = route('admin_data_entries');
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

<template>
    <AdminLayout title="APIData">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Consuming API Data
                </h2>
                <PrimaryButton type="submit" v-on:click="loadData" :disabled="loading"
                    class="mt-4 p-2 bg-blue-500 text-white rounded-md ml-auto">
                    {{ loading ? 'Loading...' : 'Load Data' }}
                </PrimaryButton>
            </div>
        </template>


        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div>
                        <div>
                            <div class="relative overflow-x-auto shadow-md sm:rounded-lg p-4">

                                <pagination :from="from" :to="to" :total="total" :current_page="current_page" :links="links"
                                    @link-clicked="handleLinkClick" />
                                <br>
                                <table style="width:100%;">
                                    <thead
                                        class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                        <tr>

                                            <th scope="col" class="px-6 py-3">Id</th>
                                            <th scope="col" class="px-6 py-3">Api</th>
                                            <th scope="col" class="px-6 py-3">Category</th>
                                            <th scope="col" class="px-6 py-3">Description</th>
                                            <th scope="col" class="px-6 py-3">Auth</th>
                                            <th scope="col" class="px-6 py-3">Https</th>
                                            <th scope="col" class="px-6 py-3">Cors</th>
                                            <th scope="col" class="px-6 py-3">Link</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <div v-if="items.length === 0"
                                            class="text-center py-4 text-gray-500 dark:text-gray-400">

                                            <td class="px-6 py-4">No items available.</td>
                                        </div>
                                        <tr v-for="item in items"
                                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                            <td scope="row"
                                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                {{ item.id }}</td>
                                            <td class="px-6 py-4">{{ item.api }}</td>
                                            <td class="px-6 py-4">{{ item.category }}</td>
                                            <td class="px-6 py-4">{{ item.description }}</td>
                                            <td class="px-6 py-4">{{ item.auth }}</td>
                                            <td class="px-6 py-4">{{ item.https }}</td>
                                            <td class="px-6 py-4">{{ item.cors }}</td>
                                            <td class="px-6 py-4">{{ item.link }}</td>
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
