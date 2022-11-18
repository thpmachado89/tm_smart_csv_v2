<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/inertia-vue3';
import Pusher from 'pusher-js';

const props = defineProps({
    token: String,
});

console.log(usePage().props);

const user = usePage().props.value.auth.user;

const form = useForm({
    file: '',
    token: props.token
});

console.log(user);

const submit = (e) => {

    e.preventDefault();

    let extension = e.target[0].files[0].type;

    if(extension == "text/csv"){

        const formData = new FormData();
        const fileData = e.target[0].files[0];
        formData.append('file', fileData);
        formData.append('user_id', user.id);
        const headers = { 'Content-Type': 'multipart/form-data', 'X-Inertia': true, 'X-Inertia-Version': '440d7fed956b4807267155458b09f46d' };
        axios.post(
            route('upload'), 
            formData, 
            { 
                headers,
                withCredentials: true 
            }).then((res) => {
            
                let inputFile = document.querySelector("#file");
                inputFile.value = "";

                if(res.data.success){
                    const alertSuccess = document.createElement("div");
                    alertSuccess.id = "success-alert";
                    alertSuccess.classList.add("bg-yellow-100");
                    alertSuccess.classList.add("border");
                    alertSuccess.classList.add("bg-yellow-100");
                    alertSuccess.classList.add("text-yellow-700");
                    alertSuccess.classList.add("px-4");
                    alertSuccess.classList.add("py-3");
                    alertSuccess.classList.add("rounded");
                    alertSuccess.classList.add("relative");
                    alertSuccess.classList.add("mb-5");
                    alertSuccess.role = "alert";
                    alertSuccess.innerHTML = `
                        <strong class="font-bold">Arquivo enviado com sucesso! </strong>
                        <span class="block sm:inline">Aguarde o processamento dos dados.</span>
                        <span onclick="this.parentElement.style.display = 'none';" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                            <svg class="fill-current h-6 w-6 text-yellow-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                        </span>
                    `;
                    document.querySelector("#alerts").appendChild(alertSuccess);

                    var pusher = new Pusher("7b3702a5d9470d4f46ca", {
                        cluster: "us2",
                    });
                    var channel = pusher.subscribe("file-process." + res.data.upload_id);
                    channel.bind("process-notification", (data) => {
                        console.log(data);
                        let result = JSON.parse(data.message);
                        let linkErrors = "";
                        if(result.errors > 0){
                            linkErrors = `<p>Encontramos erros no processamento. Para acessar clique <b><a target='_blank' href='./errors/${result.upload_id}'>aqui</a></b>.</p>`;
                        }
                        const alertProcessed = document.createElement("div");
                        alertProcessed.id = "success-alert";
                        alertProcessed.classList.add("bg-green-100");
                        alertProcessed.classList.add("border");
                        alertProcessed.classList.add("bg-green-100");
                        alertProcessed.classList.add("text-green-700");
                        alertProcessed.classList.add("px-4");
                        alertProcessed.classList.add("py-3");
                        alertProcessed.classList.add("rounded");
                        alertProcessed.classList.add("relative");
                        alertProcessed.classList.add("mb-5");
                        alertProcessed.role = "alert";
                        alertProcessed.innerHTML = `
                            <strong class="font-bold">Arquivo processado com sucesso! </strong>
                            <span class="block sm:inline">Todas as informações foram processadas (${result.count}).</span>
                            ${linkErrors}
                            <span onclick="this.parentElement.style.display = 'none';" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                                <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                            </span>
                        `;
                        document.querySelector("#alerts").appendChild(alertProcessed);
                    });
                }
        });

    } else {

        let error_alert = document.querySelector("#error-alert");
        error_alert.style.display = "block";

        let inputFile = document.querySelector("#file");
        inputFile.value = "";

    }
    
};


</script>

<template>
    <Head title="Upload" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Upload
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div id="alerts"></div>
                <div id="error-alert" style="display:none;" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-5" role="alert">
                    <strong class="font-bold">Extensão não permitida! </strong>
                    <span class="block sm:inline">Por favor selecione um arquivo com a extensão permitida. (.csv)</span>
                    <span onclick="this.parentElement.style.display = 'none';" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                        <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                    </span>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8">
                    <form @submit.prevent="submit" enctype="multipart/form-data">
                        <div>
                            <InputLabel for="name" value="Arquivo CSV" />
                            <TextInput id="file" type="file" class="mt-1 block w-full" required autofocus />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Enviar
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
