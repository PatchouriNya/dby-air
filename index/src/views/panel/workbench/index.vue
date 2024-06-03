<template>
  <el-row>
    <el-col :span="4">
      <div style="width: 100%">
        <el-skeleton :loading="loading" :rows="8">
          <el-tree
              :data="treeData"
              :props="defaultProps"
              :node-key="defaultProps.label"
              :expand-on-click-node="true"
              default-expand-all
              style="background-color: #f5f5f5 ;width:100%"
              @node-click="clickNode"
          />
        </el-skeleton>
      </div>
    </el-col>
    <el-col :span="20">
      <div class="right-content">
        <el-alert
            :title="'欢迎回来！' + title"
            type="success"
            show-icon
            center
            closable
            v-if="showData"
            style="margin-bottom: 20px"
        />
        <el-card class="data-card" v-if="showData" shadow="hover">
          <div class="data-items">
            <div class="data-item">
              <el-icon style="font-size: 30px; margin-right: 10px;">
                <Document/>
              </el-icon>
              <div class="data-content">
                <p class="data-label">内机数量</p>
                <p class="data-value">{{ showData.airQuantity }}</p>
              </div>
            </div>
            <div class="data-item">
              <el-icon style="font-size: 30px; margin-right: 10px;">
                <SetUp/>
              </el-icon>
              <div class="data-content">
                <p class="data-label">总功率</p>
                <p class="data-value">{{ showData.airConditionPower }}KW</p>
              </div>
            </div>
            <div class="data-item">
              <el-icon style="font-size: 30px; margin-right: 10px;">
                <VideoPlay/>
              </el-icon>
              <div class="data-content">
                <p class="data-label">开机数量</p>
                <p class="data-value">{{ showData.airBootQuantity }}</p>
              </div>
            </div>
            <div class="data-item">
              <el-icon style="font-size: 30px; margin-right: 10px;">
                <Sunny/>
              </el-icon>
              <div class="data-content">
                <p class="data-label">平均温度</p>
                <p class="data-value">{{ showData.airStartupTemperature }}℃</p>
              </div>
            </div>
          </div>
        </el-card>
        <el-alert
            v-else
            :title="noData"
            type="info"
            show-icon
            center
            closable
        />
      </div>
    </el-col>
  </el-row>

</template>

<script setup lang="ts">
import {ref, computed, onMounted} from 'vue'
import {clientList} from '@/api/client.js'

const loading = ref(true)
const treeData = ref()
const selectData = ref({})
const title = ref('欢迎回来')
const noData = ref('请选择一个节点')
const defaultProps = {
  children: 'childs',
  label: 'clientname'
}

async function initclientList() {
  loading.value = true
  let res = await clientList()
  loading.value = false
  treeData.value = [res.data]
  clickNode(treeData.value[0])
}

function clickNode(val) {
  selectData.value = val
  title.value = selectData.value.clientname
}


const showData = computed(() => {
  const data = selectData.value.with_overview
  if (!data) {
    return null;
  }
  return {
    airBootQuantity: data.air_boot_quantity || '0',
    airConditionPower: data.air_conditioning_power || '0',
    airQuantity: data.air_quantity || '0',
    airStartupTemperature: data.air_startup_temperature || '0'
  }
})
onMounted(() => {
  initclientList()

})

</script>

<style scoped lang="scss">
.right-content {
  /* 右侧内容的样式 */
  height: 100vh; /* 设置高度为整个视口的高度，可根据需要调整 */
  padding: 20px; /* 右侧内容的内边距 */
  margin-left: 20px;
}

.data-card {
  width: 100%;
}

.data-items {
  display: flex;
}

.data-item {
  display: flex;
  align-items: center;
  flex: 1;
  padding: 10px;
}

.data-content {
  flex: 1;
}

.data-label {
  font-size: 16px;
  color: #333;
  margin-bottom: 5px;
}

.data-value {
  font-size: 20px;
  color: #409EFF;
  margin-top: 5px;
}
</style>
