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
import {onMounted, ref, watch, watchEffect} from 'vue'
import {useRoute, useRouter} from 'vue-router'
import {ElMessage} from 'element-plus'
import {loginCheckApi} from '@/api/login.js'

const asideWidth = ref('210px')
const router = useRouter()
const route = useRoute()
const time = ref()

watch(
    () => route.fullPath,
    (to, from) => {
      loginCheck()
    }
)
const loginCheck = async () => {
  const id = localStorage.getItem('token')
  const res = await loginCheckApi(id)
  if (res.code === 200) {
    time.value = res.data
    setTimeout(() => {
      localStorage.clear()
      ElMessage.error('登录过期,请重新登录')
      router.push('/login')
    }, time.value * 1000)
    return true
  } else {
    localStorage.clear()
    ElMessage.error('登录过期,请重新登录')
    await router.push('/login')
    return false
  }
}
onMounted(async () => {
  await loginCheck()
})
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
