<template>
  <div class="container py-4">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <h1 class="text-center display-4 mb-5">Tournament Teams</h1>
        <div v-if="error" class="alert alert-danger mt-3">{{ error }}</div>

        <div class="card mb-4">
          <div class="card-header bg-dark text-white">
            <strong>Team Name</strong>
          </div>
          <ul class="list-group list-group-flush">
            <li class="list-group-item" v-for="team in teams" :key="team.id">
              {{ team.name }}
            </li>
          </ul>
        </div>

        <button @click="generateFixtures" :disabled="loading || teams.length === 0" class="btn btn-primary">
          Generate Fixtures
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';

const teams = ref([]);
const loading = ref(false);
const error = ref(null);

const fetchTeams = async () => {
  loading.value = true;
  error.value = null;
  try {
    const response = await window.axios.get('/api/teams');
    teams.value = response.data.data;
  } catch (err) {
    error.value = 'Unable to load teams.';
  } finally {
    loading.value = false;
  }
};

const generateFixtures = async () => {
  loading.value = true;
  error.value = null;
  try {
    await window.axios.post('/api/fixtures/generate');
    router.visit('/fixtures');
  } catch (err) {
    error.value = err.response ? err.response.data.message : 'Unable to generate fixtures.';
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchTeams();
});
</script>