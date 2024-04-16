<template>
  <div style="font-size: 25px">
    东佰源空调集控系统 <span style="font-size: 20px;margin-left: 10px;margin-right: 10px">欢迎，{{
      accountData && accountData.nickname
    }}</span>
    <span style="font-size: 20px">({{ path }})</span>
  </div>

  <!--  <el-breadcrumb separator=">">
      <el-breadcrumb-item :to="{ path: '/' }">首页</el-breadcrumb-item>
      <el-breadcrumb-item>{{ route.path }}</el-breadcrumb-item>
    </el-breadcrumb>-->

</template>

<script setup>
import {ArrowRight} from '@element-plus/icons-vue'
import useMenuRoute from '@/hooks/layout/useMenuRoute.js'
import {useAccountStore} from '@/store/account.js'
import {storeToRefs} from 'pinia'

const accountStore = useAccountStore()

const {accountData} = storeToRefs(accountStore)
const {path} = useMenuRoute()


</script>

<style lang="scss" scoped>
.no-redirect {
  color: #97a8be;
  cursor: text;
}

.redirect {
  color: #666;
  font-weight: 600;
  cursor: pointer;

  &:hover {
    color: $menuBg;
  }
}
</style>
