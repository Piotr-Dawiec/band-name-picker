<template>
    <Layout>
        <div class="bg-white p-6 rounded-lg shadow">
            <!-- Sekcja Głosowania -->
            <h1 class="text-2xl font-semibold mb-4">Głosuj na nazwę zespołu</h1>
            <!-- Wyświetlanie Komunikatów -->
            <div v-if="successMessage" class="mt-4 text-green-600">
                {{ successMessage }}
            </div>
            <div v-if="errorMessage" class="mt-4 text-red-600">
                {{ errorMessage }}
            </div>
            <div v-if="name" class="mb-6">
                <h2 class="text-5xl font-bold mb-2">{{ name.name }}</h2>
                <div>
                    <button 
                        @click="vote(true)" 
                        :disabled="isSubmitting" 
                        class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded mr-2"
                        :class="{ 'opacity-50 cursor-not-allowed': isSubmitting }"
                    >
                        Tak
                    </button>
                    <button 
                        @click="vote(false)" 
                        :disabled="isSubmitting" 
                        class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded"
                        :class="{ 'opacity-50 cursor-not-allowed': isSubmitting }"
                    >
                        Nie
                    </button>
                </div>
            </div>
            <div v-else>
                <p class="text-gray-700">Brak dostępnych nazw do głosowania.</p>
            </div>

            <!-- Przyciski do Pokazywania Paneli -->
            <div class="mt-6 flex space-x-4">
                <button 
                    @click="toggleAddPanel" 
                    class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded"
                >
                    {{ showAddPanel ? 'Ukryj panel dodawania nazw' : 'Pokaż panel dodawania nazw' }}
                </button>
            </div>

            <!-- Panel Dodawania Nowych Propozycji -->
            <div v-if="showAddPanel" class="mt-4">
                <h2 class="text-2xl font-semibold mb-4">Dodaj nowe propozycje nazw</h2>
                <textarea 
                    v-model="newNames" 
                    rows="5" 
                    class="w-full p-2 border border-gray-300 rounded mb-4 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                    placeholder="Jedna nazwa na linię"
                    :disabled="isSubmitting"
                ></textarea>
                <button 
                    @click="addNames" 
                    :disabled="isSubmitting" 
                    class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded"
                    :class="{ 'opacity-50 cursor-not-allowed': isSubmitting }"
                >
                    Zapisz nazwy
                </button>
            </div>

            <!-- Przyciski do Pokazywania Paneli -->
            <div class="mt-6 flex space-x-4">
                <button 
                    @click="toggleStats" 
                    class="bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-4 rounded"
                >
                    {{ showStats ? 'Ukryj statystyki' : 'Pokaż statystyki' }}
                </button>
            </div>
            <!-- Sekcja Statystyk -->
            <div v-if="showStats" class="mt-4">
                <h2 class="text-2xl font-semibold mb-4">Statystyki nazw zespołu</h2>
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="py-2 px-4 border-b">Nazwa</th>
                            <th class="py-2 px-4 border-b">Głosy na tak</th>
                            <th class="py-2 px-4 border-b">Głosy na nie</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="stat in nameStatistics" :key="stat.id" class="text-center">
                            <td class="py-2 px-4 border-b">{{ stat.name }}</td>
                            <td class="py-2 px-4 border-b">{{ stat.yes_votes }}</td>
                            <td class="py-2 px-4 border-b">{{ stat.no_votes }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </Layout>
</template>

<script>
import Layout from '@/Shared/Layout.vue';
import { router } from '@inertiajs/vue3'


export default {
    props: {
        name: Object,
        bandMember: Object,
        nameStatistics: Array, // Dodane statystyki nazw
    },
    components: {
        Layout,
    },
    data() {
        return {
            newNames: '',
            successMessage: this.$page.props.flash?.success || null,
            errorMessage: this.$page.props.flash?.error || null,
            isSubmitting: false, // Kontrola stanu przycisków
            showStats: false, // Kontrola widoczności statystyk
            showAddPanel: false, // Kontrola widoczności panelu dodawania nazw
        };
    },
    methods: {
        vote(choice) {
            this.isSubmitting = true; // Disable buttons during submission
            router.post(
                route('voting.vote', { uuid: this.bandMember.uuid }), // Generate the URL
                {
                    name_id: this.name.id,
                    vote: choice,
                },
                {
                    onFinish: () => {
                        this.isSubmitting = false; // Re-enable buttons
                    },
                    onError: (errors) => {
                        console.error('Error during voting:', errors);
                        this.isSubmitting = false;
                    },
                    onSuccess: (page) => {
                        console.log('Vote successful!', page);
                        this.successMessage = 'Twój głos został zapisany.';
                    },
                }
            );
        },
        addNames() {
            if (this.newNames.trim() !== '') {
                this.isSubmitting = true; // Blokowanie przycisków
                const namesArray = this.newNames
                    .split('\n')
                    .map((name) => name.trim())
                    .filter((name) => name !== '');
                router.post(
                    route('voting.addNames', { uuid: this.bandMember.uuid }), // Generate the URL
                    { names: namesArray },
                    {
                        onFinish: () => {
                            this.isSubmitting = false; // Odblokowanie przycisków po zakończeniu
                        },
                    }
                );
                this.newNames = '';
            }
        },
        toggleStats() {
            this.showStats = !this.showStats; // Przełącz widoczność statystyk
        },
        toggleAddPanel() {
            this.showAddPanel = !this.showAddPanel; // Przełącz widoczność panelu dodawania nazw
        },
    },
};
</script>

<style scoped>
/* Styl dla zablokowanych przycisków */
.opacity-50 {
    opacity: 0.5;
}
.cursor-not-allowed {
    cursor: not-allowed;
}
</style>
