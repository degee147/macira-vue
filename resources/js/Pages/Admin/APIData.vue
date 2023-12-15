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

<template>
    <AdminLayout title="APIData">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Consuming API Data
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div>
                        <div class="p-6 lg:p-8 bg-white border-b border-gray-200">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
