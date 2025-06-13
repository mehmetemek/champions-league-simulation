<template>
  <div class="prediction-table-container">
    <table class="table table-striped table-bordered table-custom">
      <thead>
        <tr>
          <th scope="col">Team Name</th>
          <th scope="col">%</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="prediction in predictions" :key="prediction.team_id">
          <td>{{ prediction.team_name }}</td>
          <td><strong>{{ prediction.percentage }}%</strong></td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  name: 'ChampionshipPredictionTable',
  props: {
    predictions: {
      type: Array,
      required: true
    }
  }
};
</script>

<style scoped>
.prediction-table-container table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.9em;
}

.prediction-table-container tbody tr:hover {
  background-color: #f8f9fa;
}
</style>