<template>
  <div class="container py-4">
    <div class="row justify-content-center">
      <div class="col-md-10">
        <h1 class="text-center display-4 mb-5">Simulation</h1>
        <div v-if="error" class="alert alert-danger mt-3">{{ error }}</div>
        <div v-if="!isFixtureGenerated && !loading" class="alert alert-warning">
          Please <Link href="/" class="font-weight-bold text-decoration-underline text-primary">generate fixtures</Link> first to start the simulation.
        </div>
        <div class="row mt-4">
          <div class="col-md-6">
            <h3 class="mb-3">League Table</h3>
            <div class="table-responsive">
              <table class="table table-striped table-bordered">
                <thead class="table-dark">
                  <tr>
                    <th scope="col">Team Name</th>
                    <th scope="col">P</th>
                    <th scope="col">W</th>
                    <th scope="col">D</th>
                    <th scope="col">L</th>
                    <th scope="col">GD</th>
                    <th scope="col">PTS</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="teamStats in leagueTable" :key="teamStats.team_id">
                    <td>{{ teamStats.team_name }}</td>
                    <td>{{ teamStats.played }}</td>
                    <td>{{ teamStats.wins }}</td>
                    <td>{{ teamStats.draws }}</td>
                    <td>{{ teamStats.losses }}</td>
                    <td>{{ teamStats.goal_difference }}</td>
                    <td><strong>{{ teamStats.points }}</strong></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="col-md-6">
            <h3 class="mb-3">Week {{ currentWeek }} Match Results</h3>
            <div v-if="currentWeekMatches.length === 0 && currentWeek === 0" class="alert alert-info">
              No matches have been played yet.
            </div>
            <div v-else-if="currentWeekMatches.length === 0 && currentWeek > 0" class="alert alert-info">
              No match results found for this week.
            </div>
            <div v-else>
              <div class="card mb-2 border-0 shadow-sm" v-for="match in currentWeekMatches" :key="match.id">
                <div class="card-body d-flex justify-content-between align-items-center bg-light">
                  <span class="text-end col-5">{{ match.home_team_name }}</span>
                  <span class="px-3 py-1 bg-white rounded shadow-sm border">
                    <strong>{{ match.home_score }} - {{ match.away_score }}</strong>
                  </span>
                  <span class="text-start col-5">{{ match.away_team_name }}</span>
                </div>
              </div>
            </div>
            <h3 class="mb-3 mt-4">Championship Predictions</h3>
            <div class="table-responsive">
              <table class="table table-striped table-bordered">
                <thead class="table-dark">
                  <tr>
                    <th scope="col">Team Name</th>
                    <th scope="col">%</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="prediction in championshipPredictions" :key="prediction.team_id">
                    <td>{{ prediction.team_name }}</td>
                    <td><strong>{{ prediction.percentage }}%</strong></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="d-flex justify-content-center gap-3 mt-4">
          <button @click="playAllWeeks" :disabled="loading || isLeagueFinished || !isFixtureGenerated" class="btn btn-info">
            Play All Weeks
          </button>
          <button @click="playNextWeek" :disabled="loading || isLeagueFinished || !isFixtureGenerated" class="btn btn-primary">
            Play Next Week
          </button>
          <button @click="resetData" :disabled="loading" class="btn btn-danger">
            Reset Data
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const currentWeek = ref(0);
const playedMatches = ref([]);
const leagueTable = ref([]);
const championshipPredictions = ref([]);
const fixtures = ref([]);
const loading = ref(false);
const error = ref(null);

const isFixtureGenerated = computed(() => fixtures.value.length > 0);
const isLeagueFinished = computed(() => fixtures.value.length > 0 && fixtures.value.every(f => f.is_played));

const currentWeekMatches = computed(() => {
  if (!playedMatches.value || currentWeek.value === 0) return [];
  const fixturesOfCurrentWeek = fixtures.value.filter(f => f.week === currentWeek.value);
  const fixtureIdsOfCurrentWeek = fixturesOfCurrentWeek.map(f => f.id);
  return playedMatches.value.filter(match => fixtureIdsOfCurrentWeek.includes(match.fixture_id));
});

const fetchCurrentSimulationState = async (week = 0) => {
  loading.value = true;
  error.value = null;
  try {
    const response = await window.axios.get(`/api/simulation/current-state/${week}`);
    currentWeek.value = response.data.current_week;
    playedMatches.value = response.data.played_matches;
    leagueTable.value = response.data.league_table;
    championshipPredictions.value = response.data.championship_predictions;
    fixtures.value = response.data.all_fixtures;
  } catch (err) {
      error.value = err?.response?.data?.message || 'Failed to load simulation state.';
    } finally {
    loading.value = false;
  }
};

const playNextWeek = async () => {
  loading.value = true;
  error.value = null;
  try {
    const nextWeek = currentWeek.value + 1;
    const response = await window.axios.post(`/api/simulation/play-week/${nextWeek}`);
    currentWeek.value = response.data.current_week;
    playedMatches.value = response.data.played_matches;
    leagueTable.value = response.data.league_table;
    championshipPredictions.value = response.data.championship_predictions;
    await fetchCurrentSimulationState(currentWeek.value);
  } catch (err) {
    error.value = err?.response?.data?.message || 'Error simulating week.';
  } finally {
    loading.value = false;
  }
};

const playAllWeeks = async () => {
  loading.value = true;
  error.value = null;
  try {
    const response = await window.axios.post('/api/simulation/play-all-weeks');
    currentWeek.value = response.data.current_week;
    playedMatches.value = response.data.played_matches;
    leagueTable.value = response.data.league_table;
    championshipPredictions.value = response.data.championship_predictions;
    await fetchCurrentSimulationState(currentWeek.value);
  } catch (err) {
      error.value = err?.response?.data?.message || 'Error playing all weeks.';
  } finally {
    loading.value = false;
  }
};

const resetData = async () => {
  loading.value = true;
  error.value = null;
  try {
    await window.axios.post('/api/simulation/reset-data');
    currentWeek.value = 0;
    playedMatches.value = [];
    leagueTable.value = [];
    championshipPredictions.value = [];
    fixtures.value = [];
    await fetchCurrentSimulationState();
  } catch (err) {
    error.value = err?.response?.data?.message || 'Error resetting data.';
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchCurrentSimulationState();
});
</script>