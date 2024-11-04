<script setup>
import moment from 'moment';

function formatDate(date) {
    if (date == null) {
        return null;
    }
    return moment(date.created_at).format('MMMM DD, YYYY, h:mm:ss a');
}

const props = defineProps({
    data: {
        type: Object,
        required: true,
        default: () => ({ history: [] }) // Fallback to an empty array if history is undefined
    },
});

// Log the data to check for debugging
console.log("Original Data:", props.data.history);

// Computed property to sort history data by `latest_logout` in descending order
const sortedHistory = computed(() => {
    // Ensure data.history is an array before sorting
    return Array.isArray(props.data.history) 
        ? [...props.data.history].sort((a, b) => new Date(b.latest_logout) - new Date(a.latest_logout))
        : [];
});

// Log sorted data to check if it has values
console.log("Sorted History:", sortedHistory.value);
</script>

<template>
    <div>
        <div class="flex w-full justify-between">
            <h2 class="font-bold text-lg text-typography leading-tight">
                History
            </h2>
            <Link
                :href="route('admin.history.index')"
                as="a"
                class="px-4 py-2 rounded hover:text-blue"
            >
                See all
            </Link>
        </div>
        
        <div class="overflow-x-auto bg-white pt-2">
            <table class="w-full text-sm text-left">
                <thead class="text-md text-gray-700 uppercase">
                    <tr>
                        <th>Name</th>
                        <th>Last Seen</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Use sortedHistory here -->
                    <template v-for="(item, index) in sortedHistory" :key="index">
                        <tr class="border-y text-sm text-gray-900">
                            <td class="!py-2">{{ item.fname }}</td>
                            <td class="!py-2">{{ formatDate(item.latest_logout) }}</td>
                        </tr>
                    </template>
                </tbody>
            </table>
            <!-- Display message if no data is available -->
            <div
                v-if="sortedHistory.length < 1"
                class="w-full bg-gray-100 text-center text-sm p-5"
            >
                No data available
            </div>
        </div>
    </div>
</template>
