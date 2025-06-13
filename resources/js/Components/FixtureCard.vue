<template>
  <div class="card fixture-card">
    <div class="card-header card-header-dark">
      <strong>Week {{ week }}</strong>
    </div>
    <ul class="list-group list-group-flush">
      <li class="list-group-item" v-for="fixture in fixtures" :key="fixture.id">
        {{ fixture.home_team_name }} - {{ fixture.away_team_name }}
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  name: 'FixtureCard',
  props: {
    week: {
      type: [String, Number],
      required: true
    },
    fixtures: {
      type: Array,
      required: true
    }
  }
};
</script>

<style scoped>
.fixture-card .card-header {
  background-color: #343a40;
  color: white;
  font-weight: bold;
}
.fixture-card .list-group-item {
  padding: 0.75rem 1.25rem;
  border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}
.fixture-card .list-group-item:last-child {
  border-bottom: none;
}
</style>