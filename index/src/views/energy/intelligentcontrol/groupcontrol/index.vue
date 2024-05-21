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
          <el-table-column prop="with_strategy.name" label="策略" width="180">
            <template #default="scope">
              <span v-if="scope.row.strategy_id && scope.row.strategy_id[0]"><el-tag>正在执行</el-tag></span>
              <span v-else><el-tag type="danger">暂无策略</el-tag></span>
            </template>
          </el-table-column>
          <el-table-column fixed="right" label="操作">
            <template #default="row">
              <el-tooltip content="分组空调控制" placement="top"
                          v-if="isSystem || (client_id === localClient && mainFlag === 1)">
                <el-button link type="primary" size="default" @click="showGroupControl(row.row)">
                  <el-icon>
                    <Compass/>
                  </el-icon>
                </el-button>
              </el-tooltip>
            </template>
          </el-table-column>
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

  <!--空调策略控制面板-->
  <el-dialog title="空调分组控制" v-model="groupControlVisible" :close-on-click-modal="false" :width="744">

    <div class="part2">
      <el-card class="card-box">
        <template #header>
          <div>
            <span style="font-size: 16px">控制面板</span>
          </div>
        </template>
        <el-row>
          <el-col :span="12">
            <el-tag type="primary" effect="dark" size="large">分组控制</el-tag>

          </el-col>

          <el-col :span="12">
            <el-button-group>
              <el-switch v-model="controlForm.power_state" active-value="开机" inactive-value="关机"
                         active-text="开机" inactive-text="关机"/>
            </el-button-group>
          </el-col>

        </el-row>

        <el-row>
          <el-col :span="8">
            <el-card shadow="never">
              <template #header>
                <h3>
                  <Icon icon="material-symbols:thermometer-gain-outline-rounded"/>
                  <span>设置温度/区间</span></h3>
              </template>
              <el-select v-model="controlForm.set_temperature" placeholder="选择温度">
                <el-option label="16℃" value="16℃"/>
                <el-option label="17℃" value="17℃"/>
                <el-option label="18℃" value="18℃"/>
                <el-option label="19℃" value="19℃"/>
                <el-option label="20℃" value="20℃"/>
                <el-option label="21℃" value="21℃"/>
                <el-option label="22℃" value="22℃"/>
                <el-option label="23℃" value="23℃"/>
                <el-option label="24℃" value="24℃"/>
                <el-option label="25℃" value="25℃"/>
                <el-option label="26℃" value="26℃"/>
                <el-option label="27℃" value="27℃"/>
                <el-option label="28℃" value="28℃"/>
                <el-option label="29℃" value="29℃"/>
                <el-option label="30℃" value="30℃"/>
                <el-option label="31℃" value="31℃"/>
                <el-option label="32℃" value="32℃"/>
              </el-select>

            </el-card>
          </el-col>

          <el-col :span="13" :push="2">
            <el-card shadow="never">
              <template #header>
                <h3>
                  <Icon icon="ri:function-fill"/>
                  <span>模式</span></h3>
              </template>
              <el-radio-group v-model="controlForm.operation_mode">
                <el-radio-button label="送风" value="送风">
                  <template #default>
                    <Icon icon="mdi:fan" class="btnIcon"/>
                    <span class="btnWord">送风</span>
                  </template>
                </el-radio-button>
                <el-radio-button label="制冷" value="制冷">
                  <template #default>
                    <Icon icon="fluent:weather-snowflake-32-filled" class="btnIcon"/>
                    <span class="btnWord">制冷</span>
                  </template>
                </el-radio-button>
                <el-radio-button label="制热" value="制热">
                  <template #default>
                    <Icon icon="solar:sun-2-bold-duotone" class="btnIcon"/>
                    <span class="btnWord">制热</span>
                  </template>
                </el-radio-button>
                <el-radio-button label="除湿" value="除湿">
                  <template #default>
                    <Icon icon="fa6-solid:droplet-slash" class="btnIcon"/>
                    <span class="btnWord">除湿</span>
                  </template>
                </el-radio-button>
              </el-radio-group>
            </el-card>
          </el-col>
        </el-row>

        <el-row>

          <el-col :span="8" class="card-item">
            <el-card shadow="never">
              <template #header>
                <h3>
                  <Icon icon="streamline:hotel-air-conditioner"/>
                  <span>风向</span>
                </h3>
              </template>
              <el-radio-group v-model="controlForm.wind_mode">
                <el-radio-button label="走风" value="走风">
                  <template #default>
                    <Icon icon="mdi:wind-power" class="btnIcon"/>
                    <span class="btnWord">走风</span>
                  </template>
                </el-radio-button>
                <el-radio-button label="扫风" value="扫风">
                  <template #default>
                    <Icon icon="carbon:wind-stream" class="btnIcon"/>
                    <span class="btnWord">扫风</span>
                  </template>
                </el-radio-button>
              </el-radio-group>
            </el-card>
          </el-col>

          <el-col :span="13" :push="2" class="card-item">
            <el-card shadow="never">
              <template #header>
                <h3>
                  <Icon icon="bx:wind"/>
                  <span>风速</span>
                </h3>
              </template>
              <el-radio-group v-model="controlForm.wind_speed">
                <el-radio-button label="自动" value="自动">
                  <template #default>
                    <Icon icon="mdi:refresh-auto" class="btnIcon"/>
                    <span class="btnWord">自动</span>
                  </template>
                </el-radio-button>
                <el-radio-button label="低风" value="低风">
                  <template #default>
                    <Icon icon="mdi:fan-speed-1" class="btnIcon"/>
                    <span class="btnWord">低风</span>
                  </template>
                </el-radio-button>
                <el-radio-button label="中风" value="中风">
                  <template #default>
                    <Icon icon="mdi:fan-speed-2" class="btnIcon"/>
                    <span class="btnWord">中风</span>
                  </template>
                </el-radio-button>
                <el-radio-button label="高风" value="高风">
                  <template #default>
                    <Icon icon="mdi:fan-speed-3" class="btnIcon"/>
                    <span class="btnWord">高风</span>
                  </template>
                </el-radio-button>
              </el-radio-group>
            </el-card>
          </el-col>
        </el-row>
      </el-card>
    </div>
    <template #footer>
        <span class="dialog-footer">
          <el-button @click="groupControlVisible = false">取 消</el-button>
          <el-button type="primary" @click="sureGroupControl">发送指令</el-button>

        </span>
    </template>
  </el-dialog>
</template>

<script setup>
import ClientTree from '@/components/ClientTree.vue'
import {onMounted, ref} from 'vue'
import eventBus from '@/listen/event-bus.js'
import useAuthControl from '@/hooks/useAuthControl.js'
import {useGroupList, userGroupControl} from '@/hooks/views/energy/useAirGroupList.js'
import {Icon} from '@iconify/vue'

eventBus.off('defaultNode')
eventBus.off('node-clicked')
const tree = ref()
const client_id = ref()

// 权限控制
const {isSystem, mainFlag} = useAuthControl()
const localClient = parseInt(localStorage.getItem('client_id'))

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
} = useGroupList()

const {groupControlVisible, controlForm, showGroupControl, sureGroupControl} = userGroupControl()

onMounted(async () => {
  eventBus.on('defaultNode', (val) => {
    eventBus.emit('node-clicked', val)
  })
})
</script>

<style scoped>
.right-content {
  margin-left: 20px;
}

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

.card-box .el-col :deep(.el-card__header) {
  background-color: #FAFAFA;
  color: #62AFEF;
}

h3 {
  display: flex;
  align-items: center;
}

h3 span {
  margin-left: 8px;
}


.part2 {
  margin-top: 15px;
}


.part2 .el-row {
  margin-bottom: 20px;
  padding-left: 15px;
}


.btnIcon {
  vertical-align: middle;
  margin-right: 4px;
}

.btnWord {
  vertical-align: middle;
}
</style>
