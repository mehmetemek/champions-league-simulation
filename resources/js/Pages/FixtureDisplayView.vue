<template>
  <Head title="Fixtures" />
  <div class="container py-4">
    <div class="row justify-content-center">
      <div class="col-md-10">
        <h1 class="text-center display-4 mb-5">Generated Fixtures</h1>
        <div v-if="error" class="alert alert-danger mt-3">{{ error }}</div>
        <div v-if="Object.keys(fixturesByWeek).length === 0 && !loading" class="alert alert-warning">
          No fixtures have been generated yet. Please go to <Link href="/" class="font-weight-bold text-primary text-decoration-underline">Team Selection</Link> page to create them.
        </div>
        <div class="row">
          <div class="col-md-3 mb-4" v-for="(fixtures, week) in fixturesByWeek" :key="week">
            <div class="card shadow-sm">
              <div class="card-header bg-dark text-white">
                <strong>Week {{ week }}</strong>
              </div>
              <ul class="list-group list-group-flush">
                <li class="list-group-item" v-for="fixture in fixtures" :key="fixture.id">
                  {{ fixture.home_team_name }} - {{ fixture.away_team_name }}
                </li>
              </ul>
            </div>
          </div>
        </div>
        <button @click="startSimulation" :disabled="loading || Object.keys(fixturesByWeek).length === 0" class="btn btn-success mt-4">
          Start Simulation
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { router, Link, Head } from '@inertiajs/vue3';

const fixtures = ref([]);
const loading = ref(false);
const error = ref(null);

const fetchFixtures = async () => {
  loading.value = true;
  error.value = null;
  try {
    const response = await window.axios.get('/api/fixtures');
    fixtures.value = response.data.data;
  } catch (err) {
    error.value = 'Failed to load fixtures.';
  } finally {
    loading.value = false;
  }
};

const fixturesByWeek = computed(() => {
  return fixtures.value.reduce((acc, fixture) => {
    if (!acc[fixture.week]) {
      acc[fixture.week] = [];
    }
    acc[fixture.week].push(fixture);
    return acc;
  }, {});
});

const startSimulation = () => {
  router.visit('/simulation');
};

onMounted(() => {
  fetchFixtures();
});
</script>