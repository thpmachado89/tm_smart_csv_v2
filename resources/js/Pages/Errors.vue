<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/inertia-vue3';


axios
    .get(`http://localhost/errors/show/${usePage().props.value.id}`)
    .then(response => {
        console.log(response.data);
        if(response.data.errors.length > 0){
            for (let i = 0; i < response.data.errors.length; i++) {
                let lineTable = document.createElement("td");
                lineTable.innerHTML = response.data.errors[i].line;
                let messageTable = document.createElement("td");
                messageTable.innerHTML = response.data.errors[i].message;
                let trTable = document.createElement("tr");
                trTable.appendChild(lineTable);
                trTable.appendChild(messageTable);
                document.querySelector("tbody").appendChild(trTable);
            }
        }
    });
</script>

<template>
    <Head title="Errors" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Erros
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <table class="table-auto w-full text-center">
                            <thead>
                                <tr>
                                <th>Linha</th>
                                <th>Erro</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
