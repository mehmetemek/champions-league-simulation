<template>
  <li class="list-group-item d-flex justify-content-between align-items-center">
    {{ team.name }}
    </li>
</template>

<script>
export default {
  name: 'TeamListItem',
  props: {
    team: {
      type: Object,
      required: true
    }
  }
};
</script>

<style scoped>
.list-group-item {
  padding: 0.75rem 1.25rem;
  border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}
.list-group-item:last-child {
  border-bottom: none;
}
</style>