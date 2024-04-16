<template>
  <div>
    <div class="clientItem" :style="{ marginLeft: level * 20 + 'px' }" @click="toggleExpand">
      <p @click="selectClient">{{ client.clientname }}</p>
    </div>
    <transition name="collapse">
      <div v-if="expanded">
        <div v-if="client.childs && client.childs.length">
          <ClientItem v-for="child in client.childs" :key="child.id" :client="child" :level="level + 1"
                      @select-client="updateSelectedClient"/>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import {ref, defineEmits, getCurrentInstance} from 'vue';

const props = defineProps({
  client: {
    type: Object,
    required: true
  },
  level: {
    type: Number,
    default: 0
  }
});

const emits = defineEmits(['select-client']);

const expanded = ref(false);

function toggleExpand() {
  expanded.value = !expanded.value;
}

function selectClient() {
  if (props.client.with_overview) {
    emits('select-client', props.client);
  }
}

function updateSelectedClient(client) {
  emits('select-client', client);
}
</script>

<style scoped>
.clientItem {
  padding-left: 10px;
  border-left: 2px solid #ccc;
  margin-top: 5px;
  cursor: pointer;
}

.collapse-enter-active, .collapse-leave-active {
  transition: max-height 0.3s ease;
}

.collapse-enter, .collapse-leave-to {
  max-height: 0;
  overflow: hidden;
}
</style>
