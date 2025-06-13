<template>
  <div class="league-table-container">
    <table class="table table-striped table-bordered table-custom">
      <thead>
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
</template>

<script>
export default {
  name: 'LeagueTable',
  props: {
    leagueTable: {
      type: Array,
      required: true
    }
  }
};
</script>

<style scoped>
.league-table-container table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.9em;
}

.league-table-container tbody tr:hover {
  background-color: #f8f9fa;
}
</style>