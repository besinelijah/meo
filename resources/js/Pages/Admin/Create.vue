<script setup>
import { ref } from 'vue';
import { useForm } from "@inertiajs/inertia-vue3";
import TextInput from '../Components/TextInput.vue';
import { useToast } from 'vue-toastification';
import axios from 'axios';

const isOpen = ref(false);
const selectedOption = ref("Select");
const options = ["Male", "Female"];
const isOpenBrgy = ref(false);
const selectedOptionBrgy = ref("Select");
const passwordError = ref("");
const toast = useToast();
const optionsBrgy = [
    "Angas", "Bactas", "Binatagan", "Caayunan", "Guinatungan",
    "Hinampacan", "Langa", "Laniton", "Lidong", "Mampili",
    "Mandazo", "Mangcamagong", "Manmuntay", "Mantugawe", "Matnog",
    "Mocong", "Oliva", "Pagsangahan", "Pinagwarasan", "Plaridel",
    "Poblacion 1", "Poblacion 2", "San Felipe", "San Jose", 
    "San Pascual", "Taba-taba", "Tacad", "Taisan", "Tuaca",
];

const formData = useForm({
    fname: null,
    mname: null,
    lname: null,
    sex: null,
    street: null,
    brgy: null,
    municipality: "Basud",
    province: "Camarines Norte",
    contact_number: null,
    email: null,
    password: null,
    password_confirmation: null,
    type: 1,
});

function toggleDropdownSex() {
    isOpen.value = !isOpen.value; // Toggle dropdown visibility
}
function selectOption(option) {
    selectedOption.value = option; // Update selected option
    formData.sex = option; // Update formData
    isOpen.value = false; // Close dropdown after selection
}
function toggleDropdownBrgy() {
    isOpenBrgy.value = !isOpenBrgy.value; // Toggle dropdown visibility
}

function selectOptionBrgy(optionBrgy) {
    selectedOptionBrgy.value = optionBrgy; // Update selected option
    formData.brgy = optionBrgy; // Update formData
    isOpenBrgy.value = false; // Close dropdown after selection
}
const submit =  () => {
    // Validate password before submitting
    if (!isPasswordValid(formData.password)) {
        passwordError.value = "Password must be at least 8 characters long, contain upper and lower case letters, a number, and a special character.";
        return;
    }
 

    passwordError.value = ""; // Reset error if password is valid

        // await axios.get(route('adminreg'),formData).then((res)=>{
        //     console.log(res)
        // })
    formData.post('registeradmin', {
        onSuccess:(e)=>{
    console.log(e)
            // window.location.href=route("admin.showAdmin");
            toast.success("Account created Successfully!");
        },
        onError: (e) => {
            console.log(e)
            formData.reset("password", "password_confirmation");
        },
    });
};
function isPasswordValid(password) {
    // Regular expression for password validation
    const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
    return passwordPattern.test(password);
}

</script>
<template>
    <div class="container">
        <h1>Create Admin</h1>
        <div class="adminContainer">
            <form class="forms" @submit.prevent="submit">
                <div class="row">
                        <TextInput class="input" name="First Name" v-model:modelValue="formData.fname" :message="formData.errors.fname" />
                        <TextInput class="input" name="Middle Name" v-model:modelValue="formData.mname" :message="formData.errors.mname" />
                        <TextInput class="input" name="Last Name" v-model:modelValue="formData.lname" :message="formData.errors.lname" />
                </div>
                <div class="row">
                    <div @click="toggleDropdownSex" class="input">
                            Sex
                            <div v-if="!isOpen" class="slectBox">{{ selectedOption }}</div>
                            <ul v-if="isOpen" class="dropdown-menu" @click="toggleDropdownSex">
                                <li v-for="(option, index) in options" :key="index" @click="selectOption(option)" class="dropdown-item">{{ option }}</li>
                            </ul>
                            <span v-if="formData.errors.sex" class="text-red-500 text-sm">{{ formData.errors.sex }}</span>
                        </div>
                    <TextInput class="input"  v-model:modelValue="formData.municipality" :message="formData.errors.municipality" :readonly="true" name="Municipality"/>
                    <TextInput class="input"  v-model:modelValue="formData.province" :message="formData.errors.province" :readonly="true" name="Province"/>
                </div>      
                <div class="row">
                    <div @click="toggleDropdownBrgy" class="input" >
                            Barangay
                            <div v-if="!isOpenBrgy" class="slectBox">{{ selectedOptionBrgy }}</div>
                            <ul v-if="isOpenBrgy" class="dropdown-menus" @click="toggleDropdownBrgy">
                                <li v-for="(option, index) in optionsBrgy" :key="index" @click="selectOptionBrgy(option)" class="dropdown-item">{{ option }}</li>
                            </ul>
                            <span v-if="formData.errors.brgy" class="text-red-500 text-sm">{{ formData.errors.brgy }}</span>
                        </div>
                    <TextInput class="input" v-model:modelValue="formData.street" :message="formData.errors.street" name="Street"/>
                    <TextInput class="input" v-model:modelValue="formData.contact_number" :message="formData.errors.contact_number" placeholder="09123456789" name="Mobile Number"/>
                </div>      
                <div class="row">
                    <TextInput class="input" v-model:modelValue="formData.email" :message="formData.errors.email" name="Email"/>
                    <TextInput class="input" v-model:modelValue="formData.password" :message="formData.errors.password" type="password" name="Password"/>
                    <TextInput class="input" v-model:modelValue="formData.password_confirmation"  type="password" name="Confirm Password"/>
                    
                </div>  
                <p v-if="passwordError" class="text-red-500 text-xs italic">{{ passwordError }}</p>

                <div class="action">
                    <button type="submit" class="submit">
                        <i class="fas fa-save"></i>
                        Submit</button>
                </div>
            </form>
        </div>
    </div>
</template>
<style scoped>
.input{
    width: 100%;
}
.container{
        width: 100%;
        height:100%;
        padding-left:20px;
    }
    h1{
        padding-left:20px;
        font-family: sans-serif;
        font-size: 2em;
        font-weight: bold;
        color: rgb(29, 26, 26);
    }
    .adminContainer{
        width: 100%;
        height:90%;
        background-color: white;
        border-radius: 10px;
        margin-top: 20px;
        padding:20px;
    }
    .action{
        width:100%;
        display:flex;
        justify-content: flex-end;
        padding-top: 20px;
    }
    .submit{
        background:#1DA247;
        color:white;
        font-size: 20px;
        padding:10px 30px;
        border-radius: 10px;
    }
    .row{
        width: 100%;
        display:flex;
        justify-content: space-evenly;
        gap:20px;
        /* justify-content: space-evenly */
    }
    .slectBox {
    border: 1px solid #cbd5e1;
    padding: 5px 0px;
    padding-left: 10px;
    border-radius: 4px;
    box-shadow: 0px 0px 1px rgba(0, 0, 0, 0.1); /* Subtle shadow */
}
.dropdown-menu {
    position: absolute;
    background: white;
    border:1px solid  rgba(0, 0, 0, 0.1);
}
.forms{
    display:flex;
    flex-direction: column;
    justify-content: space-evenly;
    gap:10px;
}
.dropdown-menu li {
    width: 100%;
    padding: 10px 80px;
    cursor: pointer;
}
.dropdown-menus {
    position: absolute;
    background: white;
    height: 200px;
    overflow-x: hidden;
    border:1px solid  rgba(0, 0, 0, 0.1);
}

.dropdown-menus li {
    width: 100%;
    padding: 10px 80px;
    cursor: pointer;
}
</style>
