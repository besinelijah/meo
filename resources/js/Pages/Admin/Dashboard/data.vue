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
                            <th class="cursor-pointer">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                                Type of Permit
                            </th>
                            <th class="cursor-pointer">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                                Name of Owner
                            </th>
                            <th class="cursor-pointer">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                                Project Title
                            </th>
                            <th class="cursor-pointer">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                                Application Date
                            </th>
                            <th>Remarks</th>
                            <th class="cursor-pointer">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                                Status
                            </th>
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
                                <td>{{ formatDate(item.application_date) }}</td>
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
