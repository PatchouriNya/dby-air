<template>
  <el-row>
    <el-col :span="4">
      <ClientTree ref="tree" :tree="tree" style="width: 100%"/>
    </el-col>
    <el-col :span="20">
      <div class="right-content">
        <h2 style="margin-bottom: 20px;text-align: center">{{ title }}</h2>

        <div class="shaixuan">

          <div class="searchName">
            <span>组名</span>
            <el-input
                v-model="name"
                style="width: 240px"
                placeholder="请输入组名称"
                clearable
            />
            <div class="buttonSide" style="margin-left: 20px">
              <el-button @click="reset">重置</el-button>
              <el-button type="primary" @click="search">搜索</el-button>
            </div>
          </div>

        </div>
        <el-table :data="tableData" border style="width: 100%">
          <el-table-column type="index" label="序号" width="180"/>
          <el-table-column prop="name" label="组名" width="180"/>
          <el-table-column prop="strategy_id" label="策略" width="180"/>
          <el-table-column prop="info" label="组信息"/>
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
      </div>

    </el-col>
  </el-row>
</template>

<script setup>
import ClientTree from '@/components/ClientTree.vue'
import {onMounted, ref} from 'vue'
import eventBus from '@/listen/event-bus.js'
import useAuthControl from '@/hooks/useAuthControl.js'
import useAirGroupList from '@/hooks/views/energy/useAirGroupList.js'

eventBus.off('defaultNode')
eventBus.off('node-clicked')
const tree = ref()
const client_id = ref()

// 权限控制
const {isSystem, mainFlag} = useAuthControl()

// 表格展示
const {
  tableData,
  currentPage,
  total,
  pageSize,
  title,
  name,
  handleSizeChange,
  handleCurrentChange,
  reset,
  search
} = useAirGroupList()

onMounted(async () => {
  eventBus.on('defaultNode', (val) => {
    eventBus.emit('node-clicked', val)
  })
})
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

.searchName,
.buttonSide {
  display: flex;
  align-items: center;
}

.searchName span,
.searchTemp span,
.searchOnline span {
  margin-right: 10px;
}
</style>
