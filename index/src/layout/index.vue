<template>
  <el-container class="app-wrapper">
    <el-aside :width="asideWidth" class="sidebar-container">
      <Menu/>
    </el-aside>
    <el-container class="container">
      <el-header>
        <Headers/>
      </el-header>
      <el-main style="background-color: #f5f5f5">
        <router-view/>
      </el-main>
    </el-container>
  </el-container>
</template>

<script setup>
import Menu from './Menu/index.vue'
import Headers from './headers/index.vue'
import {ref} from 'vue'

const asideWidth = ref('210px')


</script>

<style lang="scss" scoped>
.app-container {
  position: relative;
  width: 100%;
  height: 100%;
}

.container {
  width: calc(100% - $sideBarWidth);
  height: 100%;

  position: fixed;
  top: 0;
  right: 0;
  z-index: 9;
  transition: all 0.28s;

  &.hidderContainer {
    width: calc(100% - $hideSideBarWidth);
  }
}

:deep(.el-header) {
  padding: 0;
}
</style>
