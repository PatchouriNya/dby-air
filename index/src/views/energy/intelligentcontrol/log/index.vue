<template>
  <div class="shaixuan" @keyup.enter="search">
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
        <span>时间</span>
        <el-date-picker
            v-model="filters.start_date"
            type="date"
            placeholder="起始日期"
            :shortcuts="shortcuts"
            :size="'default'"
            value-format="YYYY-MM-DD"
        />
        <span style="margin: 0 10px">到</span>
        <el-date-picker
            style="margin-left: 5px"
            v-model="filters.end_date"
            type="date"
            placeholder="截止日期"
            :shortcuts="shortcuts"
            :size="'default'"
            value-format="YYYY-MM-DD"
        />
      </div>
      <div style="margin-left: 20px">
        <el-button @click="reset">重置</el-button>
        <el-button type="primary" @click="search">搜索</el-button>
        <el-button type="danger" @click="showDelete" v-if="mainFlag === 1 || isSystem">删除</el-button>
      </div>
    </div>
  </div>
  <el-table :data="tableData" border style="width: 100%"
            @selection-change="handleSelectionChange"
            :row-key="getRowKeys"
  >
    <!--    <el-table-column type="selection" :reserve-selection="true" width="55"/>
        <el-table-column type="index" label="序号" width="80" align="center"/>-->
    <el-table-column prop="client_detail.clientname" label="客户" sortable width="200px"/>
    <el-table-column prop="account" label="账号" sortable width="200px"/>
    <el-table-column prop="account_detail.nickname" label="昵称" sortable width="200px"/>
    <el-table-column prop="content" label="操作内容" sortable/>
    <el-table-column prop="ip" label="IP" sortable width="200px"/>
    <el-table-column prop="created_at" label="时间" sortable width="230px"/>
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


  <!--删除-->
  <el-dialog v-model="deleteVisible" title="警告！" width="500" :close-on-click-modal="false">
    <span style="color: red">你正在进行删除该检索条件下一共 <span style="font-size: 24px">{{ total }}</span> 条数据 的操作，确认吗？</span>
    <template #footer>
      <div class="dialog-footer">
        <el-button @click="showDelete">取消</el-button>
        <el-button type="primary" @click="sureDelete">
          确认
        </el-button>
      </div>
    </template>
  </el-dialog>
</template>

<script setup>
import {logCreateApi, logDeleteApi, logListApi} from '@/api/log.js'
import {reactive, ref, watch} from 'vue'
import {ElLoading, ElMessage} from 'element-plus'
import {account} from '@/api/account.js'

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
let lastPage = 0;

const shortcuts = [
  {
    text: '今天',
    value: new Date()
  },
  {
    text: '昨天',
    value: () => {
      const date = new Date()
      date.setTime(date.getTime() - 3600 * 1000 * 24)
      return date
    }
  },
  {
    text: '一周前',
    value: () => {
      const date = new Date()
      date.setTime(date.getTime() - 3600 * 1000 * 24 * 7)
      return date
    }
  }
]

watch(filters.value, (val) => {
  if (val.start_date && val.end_date && val.start_date > val.end_date) {
    filters.value.start_date = ''
    filters.value.end_date = ''
    ElMessage.error('起始日期不能大于截止日期')
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
  const res = await logListApi(2, currentPage.value, pageSize.value, filters.value)
  total.value = res.data.total
  tableData.value = res.data.data
  lastPage = res.data.last_page
}
getLogList()
const reset = () => {
  filters.value = {
    client: '',
    account: '',
    content: '',
    ip: '',
    start_date: '',
    end_date: ''
  }
  getLogList()
}
const search = () => {
  getLogList()
}

// 删除
const deleteVisible = ref(false)
const log = reactive({
  id: localStorage.getItem("token"),
  type: 2,
  content: '删除了空调操作记录'
})
const showDelete = () => {
  deleteVisible.value = !deleteVisible.value
}
const sureDelete = async () => {
  const loading = ElLoading.service({
    lock: true,
    text: '删除中……',
    background: 'rgba(0, 0, 0, 0.7)'
  })
  let allIds = [];
  for (let page = 1; page <= lastPage; page++) {
    const res = await logListApi(2, page, pageSize.value, filters.value);
    const pageIds = res.data.data.map(item => item.id);
    allIds = allIds.concat(pageIds);
  }
  // 在这里可以进行删除操作或其他操作
  for (const id of allIds) {
    try {
      await logDeleteApi(id);
    } catch (error) {
      console.error(`Failed to delete log with id ${id}:`, error);
    }
  }
  loading.close()
  ElMessage.success('删除该条件下的所有日志成功')
  await logCreateApi(log)
  await getLogList()

  showDelete()
}

// 控制下主管和系统账户
let isSystem = localStorage.getItem('token_')

const mainFlag = ref()

async function getAccountData() {
  let res = await account()
  mainFlag.value = res.data.main
}

getAccountData()
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