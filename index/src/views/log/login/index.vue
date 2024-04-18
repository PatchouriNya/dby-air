<template>
  <div class="shaixuan">
    <div>
      <span>客户</span>
      <el-input
          v-model="filters.client"
          style="width: 170px"
          placeholder="请输入客户名称"
          clearable
      />
      <div style="margin-left: 10px">
        <span>账号</span>
        <el-input
            v-model="filters.account"
            style="width: 170px"
            placeholder="请输入账号"
            clearable
        />
      </div>
      <div style="margin-left: 10px">
        <span>操作内容</span>
        <el-input
            v-model="filters.content"
            style="width: 170px"
            placeholder="请输入操作内容"
            clearable
        />
      </div>
      <div style="margin-left: 10px">
        <span>IP</span>
        <el-input
            v-model="filters.ip"
            style="width: 170px"
            placeholder="请输入IP"
            clearable
        />
      </div>
      <div style="margin-left: 10px">
        <el-date-picker
            v-model="date"
            type="daterange"
            range-separator="到"
            start-placeholder="开始"
            end-placeholder="截至"
            format="YYYY-MM-DD"
            value-format="YYYY-MM-DD"
            :size="'default'"
        />
      </div>
      <div style="margin-left: 20px">
        <el-button @click="reset">重置</el-button>
        <el-button type="primary" @click="search">搜索</el-button>
        <el-button type="danger" @click="deleteRow">删除</el-button>
      </div>
    </div>
  </div>
  <el-table :data="tableData" border style="width: 100%"
            @selection-change="handleSelectionChange"
            :row-key="getRowKeys"
  >
    <el-table-column type="selection" :reserve-selection="true" width="55"/>
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
import {ref, watch} from 'vue'

const currentPage = ref(1)
const pageSize = ref(10)
const total = ref(0)
const filters = ref({
  client: '',
  account: '',
  content: '',
  ip: '',
  start_date: '',
  end_date: ''
  // 添加更多筛选条件...
})

const date = ref()
watch(date, (newValue, oldValue) => {
  if (newValue) {
    filters.value.start_date = newValue[0]
    filters.value.end_date = newValue[1]
  } else {
    filters.value.start_date = ''
    filters.value.end_date = ''
  }
})

const handleSizeChange = (val) => {
  getLogList()
}
const handleCurrentChange = (val) => {
  getLogList()
}

const getRowKeys = (row) => {
  return row.id
}
const handleSelectionChange = (val) => {
  console.log(val)
}

let tableData = ref([])
const getLogList = async () => {
  const res = await logListApi(1, currentPage.value, pageSize.value, filters.value)
  total.value = res.data.total
  tableData.value = res.data.data
}
getLogList()
const reset = () => {
  filters.value = {
    client: '',
    account: '',
    content: '',
    ip: ''
  }
  date.value = ''
  getLogList()
}
const search = () => {
  getLogList()
}
const deleteRow = () => {

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