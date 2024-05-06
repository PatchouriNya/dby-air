<template>
  <div class="shaixuan">

    <div class="searchName">
      <el-button v-if="isSystem || mainFlag === 1" type="primary"
                 style="margin-right: 20px" @click="showAdd">
        新增策略
      </el-button>
      <span>策略名</span>
      <el-input
          v-model="name"
          style="width: 240px"
          placeholder="请输入策略名称"
          clearable
      />
      <div class="buttonSide" style="margin-left: 20px">
        <el-button @click="reset">重置</el-button>
        <el-button type="primary" @click="search">搜索</el-button>
      </div>
    </div>

  </div>
  <el-table :data="tableData" border style="width: 100%">
    <el-table-column label="序号" type="index" width="120"></el-table-column>
    <el-table-column label="策略名称" prop="name" width="180"></el-table-column>
    <el-table-column label="策略简介" prop="info"></el-table-column>
    <el-table-column fixed="right" width="500" label="操作">
      <template #default="row">
        <el-tooltip content="更新策略" placement="top"
                    v-if="isSystem || mainFlag === 1">
          <el-button link type="primary" size="default" @click="showEdit(row.row)">
            <el-icon>
              <Compass/>
            </el-icon>
          </el-button>
        </el-tooltip>
        <el-tooltip content="删除" placement="top"
                    v-if="isSystem || mainFlag === 1">
          <el-button
              link
              type="primary" size="default"
              @click="showDelete(row.row)">
            <el-icon>
              <Delete/>
            </el-icon>
          </el-button>
        </el-tooltip>
      </template>
    </el-table-column>

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
  <!--空调策略控制面板-->
  <el-dialog title="空调控制策略" v-model="formVisible" :close-on-click-modal="false" :width="744">
    <div style="display: flex">
      <el-form-item label="策略名称">
        <el-input style="width: 200px" v-model="controlForm.name" autocomplete="off"/>
      </el-form-item>
      <el-form-item style="margin-left: 20px" label="策略描述">
        <el-input style="width: 200px" v-model="controlForm.info" autocomplete="off"/>
      </el-form-item>
    </div>


    <div class="part2">
      <el-card class="card-box">
        <template #header>
          <div>
            <span style="font-size: 16px">控制面板</span>
          </div>
        </template>
        <el-row>
          <el-col :span="12">
            <el-tag v-if="opFlag === 1" type="primary" effect="dark" size="large">创建策略</el-tag>
            <el-tag v-else type="primary" effect="dark" size="large">更新策略</el-tag>

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
                  <span>温度</span></h3>
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
          <el-button @click="formVisible = false">取 消</el-button>
          <el-button v-if="opFlag === 1" type="primary" @click="sureAdd">创建策略</el-button>
          <el-button v-else type="primary" @click="sureEdit">更新策略</el-button>

        </span>
    </template>
  </el-dialog>

  <!--删除-->
  <el-dialog v-model="deleteVisible" title="警告！" width="500" :close-on-click-modal="false">
    <span style="color: red">你正在进行删除策略 <span style="font-size: 24px">{{
        deleteName
      }}</span> 的操作，确认吗？</span>
    <template #footer>
      <div class="dialog-footer">
        <el-button @click="deleteVisible = false">取消</el-button>
        <el-button type="primary" @click="sureDelete">
          确认
        </el-button>
      </div>
    </template>
  </el-dialog>
</template>

<script setup>
// 权限控制
import useAuthControl from '@/hooks/useAuthControl.js'
import {
  useStrategyAdd,
  useStrategyDelete,
  useStrategyEdit,
  useStrategyList
} from '@/hooks/views/energy/strategy/useStrategy.js'
import {Icon} from '@iconify/vue'

const localClient = parseInt(localStorage.getItem('client_id'))
const {isSystem, mainFlag} = useAuthControl()

// 策略列表
const {
  tableData,
  currentPage,
  total,
  pageSize,
  name,
  handleSizeChange,
  handleCurrentChange,
  reset,
  search
} = useStrategyList()
// 新增策略
const {formVisible, controlForm, opFlag, showAdd, sureAdd} = useStrategyAdd()

// 修改策略
const {showEdit, sureEdit} = useStrategyEdit()

// 删除策略
const {deleteVisible, deleteName, showDelete, sureDelete} = useStrategyDelete()
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
