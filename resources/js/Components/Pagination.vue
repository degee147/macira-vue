<template>
    <nav class="flex items-center flex-column flex-wrap md:flex-row justify-between pt-4" aria-label="Table navigation">
        <span v-if="to > 0"
            class="text-sm font-normal text-gray-500 dark:text-gray-400 mb-4 md:mb-0 block w-full md:inline md:w-auto">
            Showing
            <span class="font-semibold text-gray-900 dark:text-white">{{ from }}-{{ to }}</span> of
            <span class="font-semibold text-gray-900 dark:text-white">{{ total }}</span>
        </span>
        <ul class="inline-flex -space-x-px rtl:space-x-reverse text-sm h-8">
            <li v-for="link in links" :key="link.label">
                <a :href="parseInt(link.label) === current_page ? link.url : '#'" @click.prevent="handleLinkClick(link)"
                    :aria-current="parseInt(link.label) === current_page ? 'page' : null" :class="{
                        'flex items-center justify-center px-3 h-8 text-blue-600 border border-gray-300 bg-blue-50 hover:bg-blue-100 hover:text-blue-700 dark:border-gray-700 dark:bg-gray-700 dark:text-white':
                            parseInt(link.label) === current_page,
                        'flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white':
                            parseInt(link.label) !== current_page,
                    }">
                    {{ link.label.replace(/&[^;]+;/g, '') }}
                </a>
            </li>
        </ul>
    </nav>
</template>

<script>
export default {
    props: {
        from: Number,
        to: Number,
        total: Number,
        current_page: Number,
        links: Array,
    },
    methods: {
        handleLinkClick(link) {
            // Implement your click handler logic here
            // console.log('Link clicked:', link);
            this.$emit('link-clicked', link);
        },
    },
};
</script>

<style scoped>
/* Add any additional styles specific to this component */
</style>
