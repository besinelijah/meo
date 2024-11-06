<script setup>
import { ref, computed, onMounted } from "vue";
import moment from "moment";
import { useForm } from "@inertiajs/inertia-vue3";
import Pagination from "@/Components/Pagination.vue";
import axios from "axios";

const props = defineProps({
    queue: [Object, Array],
    type: [Object, String],
});

function getType(type) {
    var text = "";
    switch (type) {
        case "1":
            text = "Business Permit (New)";
            break;
        case "2":
            text = "Building Permit";
            break;
        default:
            text = "Business Permit (Renewal)";
            break;
    }
    return text;
}
function formatDate(date) {
    return moment(date).format("MMMM DD, YYYY h:mm a");
}

// Reactive variables
const openMenuIndex = ref(null);
const toggleMenu = (index) => {
    openMenuIndex.value = openMenuIndex.value === index ? null : index;
};

// Form data for changing status
const formData = useForm({
    id: null,
    status: null,
    type: null,
    isNew: null,
    clientId: null,
    remarks: null,
    data: null,
});

// Function to change status
function changeStatus(id, status, type, isSubmit = true, remarks = null, data) {
    formData.id = id;
    formData.status = status;
    formData.type = type;
    formData.remarks = remarks;
    formData.data = data;

    if (isSubmit) {
        submitForm();
    }
}

// Function to submit the form
function submitForm() {
    formData.post(route("admin.approval.changestatus"));
}

// Function to get a record
function getRecord(id, type, clientId) {
    localStorage.setItem("localID", id);
    localStorage.setItem("localtype", type);
    localStorage.setItem("clientID", clientId);
    if (
        localStorage.getItem("localID") ||
        localStorage.getItem("localtype") ||
        localStorage.getItem("clientID")
    ) {
        formData.id = localStorage.getItem("localID");
        formData.type = localStorage.getItem("localtype");
        formData.clientId = localStorage.getItem("clientID");
    } else {
        formData.id = id;
        formData.type = type;
        formData.clientId = clientId;
    }

    formData.post("/admin/approval/getRecord");
}

// Toggle reject modal
const showRejectModal = ref(false);
function toggleRejectModal() {
    showRejectModal.value = !showRejectModal.value;
}
const searchQuery = ref(""); // Search query

// Get the name of the person who checked the record
function checkedBy(checkedBy) {
    if (checkedBy != null) {
        return checkedBy.lname + ", " + checkedBy.fname + " " + checkedBy.mname;
    }
    return "";
}
const inputText = ref("");
const hasContent = ref(true);
const searchData = ref([]);

const handleInput = async (e) => {
    const response = await axios.get("approval/search", {
        params: {
            search: inputText.value,
        },
    });
    console.log(response.data);
    hasContent.value = response.data.length > 0;
    searchData.value = response.data;
    if (inputText.value.length < 1) {
        hasContent.value = 0;
    }
};

const sortColumn = ref("application_date");
const sortOrder = ref("asc");
const sortedQueue = computed(() => {
    const data = [...filteredQueue.value];
    return data.sort((a, b) => {
        const aValue = a[sortColumn.value];
        const bValue = b[sortColumn.value];
        if (aValue === null || aValue === undefined) return 1;
        if (bValue === null || bValue === undefined) return -1;

        return sortOrder.value === "asc"
            ? aValue > bValue
                ? 1
                : -1
            : aValue < bValue
            ? 1
            : -1;
    });
});
const updateSort = (column) => {
    sortOrder.value =
        sortColumn.value === column && sortOrder.value === "asc"
            ? "desc"
            : "asc";
    sortColumn.value = column;
};
onMounted(() => {
    if (performance.navigation.type === performance.navigation.TYPE_RELOAD) {
        if (
            localStorage.getItem("localID") ||
            localStorage.getItem("localtype") ||
            localStorage.getItem("clientID")
        ) {
            formData.id = localStorage.getItem("localID");
            formData.type = localStorage.getItem("localtype");
            formData.clientId = localStorage.getItem("clientID");
            formData.post("/admin/approval/getRecord");
        }
    }
    hasContent.value = inputText.value.length > 0;
});
</script>

<template>
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-center">{{ type }}</h1>
    </div>

    <div class="card !p-2 mt-2">
        <div>
            <div>
                <table class="w-full text-sm text-left">
                    <thead class="text-md text-gray-700 uppercase">
                        <tr>
                            <th>Type of Permit</th>
                            <th>Name of Owner</th>
                            <th>Project Title</th>
                            <th>Application Date</th>
                            <th>Remarks</th>
                            <th>Status</th>
                            <th>View</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template v-for="(item, index) in queue" :key="index">
                            <tr>
                                <td>{{ getType(item.type) }}</td>
                                <td>
                                    {{ item.client.lname }},
                                    {{ item.client.fname }}
                                    {{ item.client.mname }}
                                </td>
                                <td>{{ item.project_title }}</td>
                                <td>{{ formatDate(item.created_at) }}</td>
                                <td>{{ item.remarks }}</td>
                                <td class="!py-2 whitespace-nowrap">
								<span class="p-2 rounded" :class="{
									'bg-green-100': item.status == 'Approved',
									'bg-red-100': item.status == 'Rejected',
									'bg-yellow-100': item.status == 'Pending',
								}">
									<i class="mr-2" :class="{
										'fas fa-check-circle text-green-500': item.status == 'Approved',
										'fas fa-times-circle text-red-500': item.status == 'Rejected',
										'fas fa-hourglass-half text-yellow-500': item.status == 'Pending',
									}"></i>
									{{ item.status }}
								</span>
							    </td>
                                <td class="!py-2">
                                    <button
                                        @click="
                                            getRecord(
                                                item.id,
                                                item.type,
                                                item.client_id
                                            )
                                        "
                                        class="p-2 text-gray-600 hover:text-gray-900 relative"
                                    >
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr class="border-y text-sm text-gray-900"></tr>
                        </template>
                    </tbody>
                </table>

                <!-- No data available message -->
                <div
                    v-if="queue.length <= 0"
                    class="w-full bg-gray-100 text-center text-sm p-5"
                >
                    No data available
                </div>
                <!-- Pagination -->
            </div>
        </div>
    </div>
</template>
