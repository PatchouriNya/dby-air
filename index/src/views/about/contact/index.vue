<template>
  <el-main style="margin-top: -20px">
    <el-card class="full-card" shadow="hover">
      <h1 style="text-align: center;font-size: 70px;">东佰源空调集控系统</h1>
    </el-card>
    <el-card class="company-card" shadow="hover">
      <el-text class="company-info" style="white-space: pre-line;">
        {{ accountStore.systemClient && accountStore.systemClient.info }}
      </el-text>
    </el-card>
  </el-main>
</template>

<script setup>
import {useAccountStore} from '@/store/account.js'
import {ref} from 'vue'

const accountStore = useAccountStore()
</script>

<style scoped>


.full-card {
  height: 400px;
  width: 100%;
  flex: 1; /* 占据剩余空间 */
  margin-bottom: 20px; /* 设置下边距，可根据需要调整 */
  display: flex;
  flex-direction: column; /* 列排列 */
  justify-content: center; /* 垂直居中 */
}

.company-card {
  height: 400px;
  width: 100%; /* 设置卡片宽度，可以根据需要调整 */
  display: flex;
  flex-direction: column; /* 列排列 */
  justify-content: center; /* 垂直居中 */
  text-align: center;
}

.company-info {
  font-size: 20px;
}
</style>
