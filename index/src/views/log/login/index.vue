<template>
  <el-table :data="tableData" border style="width: 100%">
    <el-table-column type="index" label="序号" width="80" align="center"/>
    <el-table-column prop="client_detail.clientname" label="客户" sortable/>
    <el-table-column prop="account" label="账号" sortable/>
    <el-table-column prop="account_detail.nickname" label="昵称" sortable/>
    <el-table-column prop="content" label="操作内容" sortable/>
    <el-table-column prop="ip" label="IP" sortable/>
    <el-table-column prop="created_at" label="时间" sortable/>
  </el-table>
  <el-pagination class="fenye"
                 v-model:current-page="currentPage"
                 v-model:page-size="pageSize"
                 :page-sizes="[10,15, 20, 25, 30]"
                 background
                 layout="total, sizes, prev, pager, next, jumper"
                 :total="total"
                 @size-change="handleSizeChange"
                 @current-change="handleCurrentChange"
  />
</template>

<script setup>
import {getLoginListApi} from '@/api/log.js'
import {reactive, ref} from 'vue'

const currentPage = ref(1)
const pageSize = ref(10)
const total = ref(0)
const handleSizeChange = (val) => {

}
const handleCurrentChange = (val) => {

}
let tableData = reactive([])
const getLoginList = async () => {
  const res = await getLoginListApi()
  Object.assign(tableData, res.data.data)
}
getLoginList()


</script>

<style scoped>
.fenye {
  margin-top: 20px;
}
</style>