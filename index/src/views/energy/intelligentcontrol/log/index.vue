<template>
  <el-table :data="tableData" border style="width: 100%">
    <el-table-column type="index" label="序号" width="80" align="center"/>
    <el-table-column prop="client_detail.clientname" label="客户" width="200" sortable/>
    <el-table-column prop="account_detail.account" label="账号" width="150" sortable/>
    <el-table-column prop="account_detail.nickname" label="昵称" width="200" sortable/>
    <el-table-column prop="content" label="操作内容" sortable/>
    <el-table-column prop="ip" label="IP" width="200" sortable/>
    <el-table-column prop="created_at" label="时间" width="200" sortable/>
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
import {logListApi} from '@/api/log.js'
import {reactive, ref} from 'vue'

const currentPage = ref(1)
const pageSize = ref(10)
const total = ref(0)
const filters = ref({
  client: '',
  account: '',
  content: '',
  ip: ''
  // 添加更多筛选条件...
})
const handleSizeChange = (val) => {
  getLogList()
}
const handleCurrentChange = (val) => {
  getLogList()
}
let tableData = reactive([])
const getLogList = async () => {
  const res = await logListApi(2, currentPage.value, pageSize.value, filters.value)
  total.value = res.data.total
  Object.assign(tableData, res.data.data)
}
getLogList()


</script>

<style scoped>
.fenye {
  margin-top: 20px;
}
</style>