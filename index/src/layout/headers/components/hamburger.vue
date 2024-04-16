<template>
  <div class="hamburger-container" @click="toggleClick" id="hamburger">
    <el-icon v-model="isCollapse" style="font-size: larger;margin-top: 5px">
      <Fold/>
    </el-icon>
  </div>
</template>

<script setup>
import {useStore} from 'vuex'
import {computed} from 'vue'

let isCollapse;

function toggleClick() {
  isCollapse = !isCollapse
}
</script>

<style lang="scss" scoped>
.hamburger-container {
  margin-right: 16px;
  box-sizing: border-box;
  cursor: pointer;
}
</style>
