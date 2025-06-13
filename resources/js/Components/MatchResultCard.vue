<template>
  <div class="card match-result-card mb-2">
    <div class="card-body d-flex justify-content-between align-items-center">
      <span class="team-name">{{ match.home_team_name }}</span>
      <span class="score-display">
        <strong>{{ match.home_score }} - {{ match.away_score }}</strong>
      </span>
      <span class="team-name">{{ match.away_team_name }}</span>
    </div>
  </div>
</template>

<script>
export default {
  name: 'MatchResultCard',
  props: {
    match: {
      type: Object,
      required: true
    }
  }
};
</script>

<style scoped>
.match-result-card {
  border: 1px solid #dee2e6;
  border-radius: 0.25rem;
}
.match-result-card .card-body {
  padding: 0.75rem 1.25rem;
}
.score-display {
  min-width: 60px;
  text-align: center;
}
.team-name {
  flex-grow: 1;
  text-align: left;
}
.team-name:last-child {
  text-align: right;
}
</style>