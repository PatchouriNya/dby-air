<template>
  <div class="shaixuan">
    <div>
      <span>客户</span>
      <el-input
          v-model="filters.client"
          style="width: 240px"
          placeholder="请输入客户名称"
          clearable
      />
      <div style="margin-left: 10px">
        <span>账号</span>
        <el-input
            v-model="filters.account"
            style="width: 240px"
            placeholder="请输入账号"
            clearable
        />
      </div>
      <div style="margin-left: 10px">
        <span>操作内容</span>
        <el-input
            v-model="filters.content"
            style="width: 240px"
            placeholder="请输入操作内容"
            clearable
        />
      </div>
      <div style="margin-left: 10px">
        <span>IP</span>
        <el-input
            v-model="filters.ip"
            style="width: 240px"
            placeholder="请输入IP"
            clearable
        />
      </div>
      <div style="margin-left: 20px">
        <el-button @click="reset">重置</el-button>
        <el-button type="primary" @click="search">搜索</el-button>
      </div>
    </div>
  </div>
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
  pageSize.value = val
  getLogList()
}
const handleCurrentChange = (val) => {
  currentPage.value = val
  getLogList()
}
let tableData = reactive([])
const getLogList = async () => {
  const res = await logListApi(1, currentPage.value, pageSize.value, filters.value)
  total.value = res.data.total
  Object.assign(tableData, res.data.data)
}
getLogList()


const reset = () => {
  filters.value = {
    client: '',
    account: '',
    content: '',
    ip: ''
  }
  getLogList()
}
const search = () => {
  getLogList()
}
</script>

<script>
export default {
  name: "index"
}

</script>

<style scoped>
.fenye {
  margin-top: 20px;
}

.shaixuan {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 20px;
}

.shaixuan div {
  display: flex;
  align-items: center;
}

.shaixuan div span {
  margin-right: 10px;
}

.searchTemp {
  margin-left: 10px; /* 添加间距 */
}

.searchOnline {
  margin-left: 20px; /* 调整间距 */
}
</style>