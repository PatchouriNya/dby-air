<template>
  <el-row>
    <el-col :span="4">
      <div style="width: 100%">
        <el-skeleton :loading="loading">
          <el-tree
              :data="treeData"
              :props="defaultProps"
              node-key="id"
              :expand-on-click-node="true"
              @current-change="clickNode"
              :current-node-key="default_checked"
              :default-expanded-keys="defaultExpandedKeys"
              auto-expand-parent
              highlight-current
              style="background-color: #f5f5f5;width: 100%"
          />
        </el-skeleton>
      </div>
    </el-col>
    <el-col :span="20">
      <div class="right-content">
        <h2 style="margin-bottom: 20px;text-align: center">{{ selectData.clientname }}</h2>
        <div v-if="!selectData.clientname">
          <h2 style="margin-bottom: 20px;text-align: center" v-if="selectData.type === 0 || !selectData.clientname">
            请选择一个客户</h2>
        </div>
        <div class="shaixuan">
          <div class="searchName">
            <span>办公室</span>
            <el-input
                v-model="filters.designation"
                style="width: 240px"
                placeholder="请输入办公室名称"
                clearable
            />
            <div class="searchTemp" style="margin-left: 10px">
              <span>室温</span>
              <el-input
                  v-model="filters.room_temperature"
                  style="width: 240px"
                  placeholder="请输入室温"
                  clearable
              />
            </div>
            <div class="searchOnline" style="margin-left: 10px">
              <span>开机状态</span>
              <el-select v-model="filters.power_state" placeholder="选择状态" style="width: 160px">
                <el-option
                    v-for="item in options"
                    :key="item.value"
                    :label="item.lable"
                    :value="item.value"
                />
              </el-select>
            </div>
            <div class="buttonSide" style="margin-left: 20px">
              <el-button @click="reset">重置</el-button>
              <el-button type="primary" @click="search">搜索</el-button>
              <el-button type="primary" @click="showSelectColumn">选择列</el-button>
              <el-button type="danger" @click="getAirTrueData">刷新</el-button>
              <el-button type="warning" @click="forceControl">集体强控</el-button>

            </div>
          </div>

        </div>
        <el-table :data="tableData" style="width: 100%"
                  :header-cell-style="{'text-align':'center'}"
                  :cell-style="cellStyle"
                  :empty-text="noDataMeg">
          <el-table-column prop="show_id" label="序号" width="100px"/>
          <el-table-column v-if="showColumn.designation" prop="designation" label="空调位置"/>
          <el-table-column v-if="showColumn.online_state" prop="online_state" label="接入状态" width="130px"/>
          <!--          <el-table-column v-if="showColumn.electrify_state" prop="electrify_state" label="通电状态" width="130px"/>-->
          <el-table-column v-if="showColumn.power_state" prop="power_state" label="开机状态" width="130px"/>
          <el-table-column v-if="showColumn.operation_mode" prop="operation_mode" label="运行模式" width="130px"/>
          <el-table-column v-if="showColumn.wind_speed" prop="wind_speed" label="风速" width="130px"/>
          <el-table-column v-if="showColumn.wind_mode" prop="wind_mode" label="风向" width="130px"/>
          <el-table-column v-if="showColumn.set_temperature" prop="set_temperature" label="设置温度℃" width="130px"/>
          <el-table-column v-if="showColumn.room_temperature" prop="room_temperature" label="室温℃" width="130px"/>
          <el-table-column v-if="showColumn.voltage" prop="voltage" label="电压" width="130px"/>
          <el-table-column v-if="showColumn.electric_current" prop="electric_current" label="电流" width="130px"/>
          <el-table-column v-if="showColumn.power" prop="power" label="功率" width="130px"/>
          <el-table-column v-if="showColumn.electric_quantity" prop="electric_quantity" label="电量读取" width="130px"/>
          <el-table-column v-if="showColumn.responsible_person" prop="responsible_person" label="责任人" width="130px"/>
          <el-table-column v-if="showColumn.standby_mode" prop="standby_mode" label="待机状态" width="130px"/>
          <el-table-column v-if="showColumn.air_brand" prop="air_brand" width="140px" label="品牌"/>

          <el-table-column fixed="right" label="操作" width="100">
            <template #default="row">
              <el-tooltip content="控制" placement="top">
                <el-button link type="primary" size="default" @click="showControl(row)">
                  <el-icon>
                    <Compass/>
                  </el-icon>
                </el-button>
              </el-tooltip>
              <el-tooltip content="修改" placement="top">
                <el-button link type="primary" size="default" @click="showEdit(row)">
                  <el-icon>
                    <Edit/>
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

      </div>
    </el-col>
  </el-row>


  <!--  配置隐藏列-->
  <el-dialog title="字段配置" v-model="columnVisible">
    <transition name="fade">
      <div>
        <div>选择显示字段</div>
        <div>
          <el-checkbox v-model="showColumn.designation">空调位置</el-checkbox>
          <el-checkbox v-model="showColumn.online_state">接入状态</el-checkbox>
          <el-checkbox v-model="showColumn.power_state">开机状态</el-checkbox>
          <el-checkbox v-model="showColumn.operation_mode">运行模式</el-checkbox>
          <el-checkbox v-model="showColumn.wind_speed">风速</el-checkbox>
          <el-checkbox v-model="showColumn.set_temperature">设置温度℃</el-checkbox>
          <el-checkbox v-model="showColumn.room_temperature">室温℃</el-checkbox>
          <el-checkbox v-model="showColumn.air_brand">品牌</el-checkbox>
          <el-checkbox v-model="showColumn.electrify_state">通电状态</el-checkbox>
          <el-checkbox v-model="showColumn.wind_mode">风向</el-checkbox>
          <el-checkbox v-model="showColumn.voltage">电压</el-checkbox>
          <el-checkbox v-model="showColumn.electric_current">电流</el-checkbox>
          <el-checkbox v-model="showColumn.power">功率</el-checkbox>
          <el-checkbox v-model="showColumn.electric_quantity">电量读取</el-checkbox>
          <el-checkbox v-model="showColumn.responsible_person">责任人</el-checkbox>
          <el-checkbox v-model="showColumn.standby_mode">待机状态</el-checkbox>
        </div>
      </div>
    </transition>
    <template #footer>
        <span class="dialog-footer">
          <el-button @click="showSelectColumn">取 消</el-button>
          <el-button type="primary" @click="showSelectColumn">确 定</el-button>
        </span>
    </template>
  </el-dialog>
  <!--空调控制面板-->
  <el-dialog title="空调控制" v-model="controlVisible" :close-on-click-modal="false" :width="744">
    <transition name="fade">
      <div>
        <!-- 第一部分：空调信息 -->
        <div class="part1">
          <el-form label-width="auto" v-model="controlForm">
            <el-row>
              <el-col :span="8">
                <el-form-item label="">
                  <h3>{{ editForm.designation }}</h3>
                </el-form-item>
              </el-col>
              <el-col :span="16" :push="8">

              </el-col>
            </el-row>

            <el-row>
              <el-col :span="8">
                <el-form-item label="室温">

                </el-form-item>
              </el-col>
              <el-col :span="8">
                <el-form-item label="电压">
                  <span>{{ controlForm.power }}V</span>
                </el-form-item>
              </el-col>

              <el-col :span="8">
                <el-form-item label="电量读取">
                  <span>{{ controlForm.electric_quantity }}度</span>
                </el-form-item>
              </el-col>
            </el-row>

            <el-row>
              <el-col :span="8" style="font-weight: bolder">
                <el-form-item :label="controlForm.room_temperature">

                </el-form-item>
              </el-col>

              <el-col :span="8">
                <el-form-item label="电流">
                  <span>{{ controlForm.electric_current }}A</span>
                </el-form-item>
              </el-col>

              <el-col :span="8">
                <el-form-item label="人体感应">
                  <span>未设置</span>
                </el-form-item>
              </el-col>
            </el-row>

            <el-row>
              <el-col :span="8">
                <el-form-item label="责任人">
                  <span>{{ controlForm.responsible_person }}</span>
                </el-form-item>
              </el-col>

              <el-col :span="8">
                <el-form-item label="功率">
                  <span>{{ controlForm.voltage }}W</span>
                </el-form-item>
              </el-col>

              <el-col :span="8">
                <el-form-item label="待机状态">
                  <span>{{ controlForm.standby_mode }}</span>
                </el-form-item>
              </el-col>

            </el-row>
          </el-form>
        </div>

        <!-- 第二部分：控制面板 -->
        <div class="part2">

          <el-card class="card-box">
            <template #header>
              <div>
                <span style="font-size: 16px">控制面板</span>
              </div>
            </template>
            <el-row>
              <el-col :span="12">
                <el-tag type="primary" effect="dark" size="large">远程控制</el-tag>
              </el-col>

              <el-col :span="12">
                <el-button-group>
                  <!--                  <el-form-item label="通电状态" style="float: left;margin-right: 20px">
                                      <el-tag round v-if="controlForm.electrify_state === true" type="primary">通电</el-tag>
                                      <el-tag round v-else type="info">断电</el-tag>
                                    </el-form-item>-->
                  <el-switch
                      v-if="showEleState"
                      v-model="controlForm.electrify_state"
                      @change="electrifyChange"
                      active-text="通电" inactive-text="断电"
                      style="float: left;margin-right: 20px"/>

                  <el-switch v-if="controlForm.electrify_state === false"
                             v-model="controlForm.power_state"
                             active-text="开机" inactive-text="关机" disabled/>
                  <el-switch v-else v-model="controlForm.power_state" active-text="开机" inactive-text="关机"/>
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
      </div>
    </transition>
    <template #footer>
        <span class="dialog-footer">
          <el-button @click="closeControl">取 消</el-button>
          <el-button type="primary" @click="sureControl(colId)">发送指令</el-button>
        </span>
    </template>
  </el-dialog>
  <!--强控面板-->
  <el-dialog title="空调集体控制" v-model="forceControlVisible" :close-on-click-modal="false" :width="744">

    <div class="part2">
      <el-card class="card-box">
        <template #header>
          <div>
            <span style="font-size: 16px">控制面板</span>
          </div>
        </template>
        <el-row>
          <el-col :span="12">
            <el-tag type="primary" effect="dark" size="large">集体控制</el-tag>

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
          <el-button @click="forceControlVisible = false">取 消</el-button>
          <el-button type="primary" @click="sureForceControl">发送指令</el-button>

        </span>
    </template>
  </el-dialog>

  <el-dialog title="编辑" v-model="editVisible" :close-on-click-modal="false" :width="500">
    <transition name="fade">
      <el-form
          label-position="right"
          label-width="auto"
          v-model="editForm"
          style="max-width: 600px"
      >
        <el-form-item label="空调位置">
          <el-input v-model="editForm.designation"/>
        </el-form-item>
        <el-form-item label="空调品牌">
          <el-input v-model="editForm.air_brand"/>
        </el-form-item>
        <el-form-item label="责任人">
          <el-input v-model="editForm.responsible_person"/>
        </el-form-item>
      </el-form>
    </transition>
    <template #footer>
        <span class="dialog-footer">
          <el-button @click="closeEdit">取 消</el-button>
          <el-button type="primary" @click="sureEdit(colId)">确 定</el-button>
        </span>
    </template>
  </el-dialog>
</template>


<script setup>
import {onMounted, reactive, ref, watch} from 'vue'
import {clientList} from '@/api/client.js'
import {getOneAirClient} from '@/api/getAirDetail'
import {updateAir} from '@/api/updateAir'
import {controlAir} from '@/api/controlAir'
import {ElLoading, ElMessage, ElMessageBox} from 'element-plus'
import {logCreateApi} from '@/api/log.js'
import {airDetailApi, getAirTrueDataApi} from '@/api/air.js'
import {Icon} from '@iconify/vue'
import {groupControlApi} from '@/api/group.js'
import {useRoute} from 'vue-router'

const route = useRoute()
let data = route.query.data
const loading = ref(true)
const tableClientId = ref()
// 配置左侧层级
const clientId = ref()
const treeData = ref([])
const selectData = ref({})
const defaultProps = {
  children: 'childs',
  label: 'clientname'
}
// 配置表格
const airData = ref([])
const tableData = ref([])
// 配置表格分页查询
const currentPage = ref(1)
const total = ref(0)
const pageSize = ref(10)
const noDataMeg = ref('暂无数据')
const options = ref([
  {
    lable: '开机',
    value: '开机'
  }, {
    lable: '关机',
    value: '关机'
  }
])
const filters = ref({
  designation: '',
  online_state: '',
  power_state: '',
  operation_mode: '',
  room_temperature: ''
  // 添加更多筛选条件...
})

// 配置表格隐藏列
const columnVisible = ref(false)
const showColumn = ref({
  designation: true,
  air_brand: true,
  online_state: true,
  electrify_state: false,
  power_state: true,
  operation_mode: true,
  wind_speed: true,
  set_temperature: true,
  room_temperature: true,
  voltage: false,
  electric_current: false,
  power: false,
  electric_quantity: false,
  wind_mode: false,
  standby_mode: false,
  responsible_person: false
})

// 配置默认选中节点
const defaultExpandedKeys = ref([])
const default_checked = ref()

// 配置显示和隐藏控制、修改面板
const controlVisible = ref(false)
const editVisible = ref(false)
const colId = ref() // 当前选中的行的数据的id
const editForm = ref({
  designation: '',
  air_brand: '',
  responsible_person: ''
})  //编辑表单

// 控制空调
const controlForm = ref({
  online_state: '',
  electrify_state: '',
  power_state: '',
  operation_mode: '',
  wind_speed: '',
  set_temperature: '',
  room_temperature: '',
  voltage: '',
  electric_current: '',
  power: '',
  electric_quantity: '',
  wind_mode: '',
  standby_mode: '',
  responsible_person: ''

})

const cellStyle = ({row, column, rowIndex, columnIndex}) => {
  // 状态列字体颜色
  // columnIndex 列下标
  // rowIndex 行下标
  // row 行
  // column 列
  if (row.online_state == '在线' && column.property === 'online_state')
    return {color: '#189EFF', textAlign: 'center'}
  if (row.online_state == '故障' && column.property === 'online_state') {
    return {color: '#FB6E6E', textAlign: 'center'}
  }
  if (row.power_state == '开机' && column.property === 'power_state')
    return {color: '#189EFF', textAlign: 'center'}
  if (row.power_state == '关机' && column.property === 'power_state') {
    return {color: '#FB6E6E', textAlign: 'center'}
  }
  if (row.operation_mode == '制冷' && column.property === 'operation_mode')
    return {color: '#189EFF', textAlign: 'center'}
  if (row.operation_mode == '制热' && column.property === 'operation_mode') {
    return {color: '#FB6E6E', textAlign: 'center'}
  }
  return {textAlign: 'center'}

}
const findFirstTypeOneNode = (nodes) => {
  for (const node of nodes) {
    if (node.type === 1) {
      return node
    }

    if (node.childs && node.childs.length > 0) {
      const result = findFirstTypeOneNode(node.childs)
      if (result) {
        tableClientId.value = result.id
        return result
      }
    }
  }

  return null
}


async function initclientList() {
  loading.value = true
  let res = await clientList()
  treeData.value = [res.data]
  loading.value = false
  let defaultChoose = findFirstTypeOneNode(treeData.value)
  if (data) {
    data = JSON.parse(route.query.data)
    defaultChoose = data
  }

  if (defaultChoose) {
    // 找到第一个 type 为 1 的节点
    default_checked.value = defaultChoose.id
    defaultExpandedKeys.value.push(defaultChoose.id)
    clickNode(defaultChoose)
  }

}

async function initAirList(id, filters, pageSize, currentPage) {
  if (clientId.value) {
    let res = await getOneAirClient(id, filters, pageSize, currentPage)
    airData.value = res.data
    tableData.value = airData.value.data
    total.value = airData.value.total
  }

}

function clickNode(val) {
  if (val.type === 1) {
    selectData.value = val
    clientId.value = val.id
    if (val.type === 1) {
      tableClientId.value = val.id
      initAirList(clientId.value, filters.value, pageSize.value, currentPage.value)
      noDataMeg.value = '该客户暂时没有导入数据'
    } else {
      tableData.value = []
      noDataMeg.value = '请选择具体客户单位'
    }
  }
}

function search() {
  initAirList(clientId.value, filters.value, pageSize.value, currentPage.value)
}

function reset() {
  filters.value = {
    designation: '',
    online_state: '',
    power_state: '',
    operation_mode: '',
    room_temperature: ''
  }
  initAirList(clientId.value, filters.value, pageSize.value, currentPage.value)
  showColumn.value = {
    designation: true,
    air_brand: true,
    online_state: true,
    electrify_state: false,
    power_state: true,
    operation_mode: true,
    wind_speed: true,
    set_temperature: true,
    room_temperature: true,
    voltage: false,
    electric_current: false,
    power: false,
    electric_quantity: false,
    wind_mode: false,
    standby_mode: false,
    responsible_person: false
  }
}

function showSelectColumn() {
  columnVisible.value = !columnVisible.value
}

// 是否显示断电开关
const showEleState = ref(true)
const show_id = ref()

const airBelongClientname = ref()

async function showControl(row) {
  let res = await airDetailApi(row.row.id)
  airBelongClientname.value = res.data.clientname
  show_id.value = row.row.show_id
  controlVisible.value = !controlVisible.value
  colId.value = row.row.id
  editForm.value.designation = row.row.designation
  controlForm.value.wind_speed = row.row.wind_speed
  controlForm.value.online_state = row.row.online_state == '在线'
  controlForm.value.power_state = row.row.power_state == '开机'
  if (row.row.electrify_state == null || row.row.electrify_state == '') {
    showEleState.value = false
    controlForm.value.electrify_state = null
  } else {
    controlForm.value.electrify_state = row.row.electrify_state == '通电'
    showEleState.value = true
  }
  controlForm.value.operation_mode = row.row.operation_mode
  controlForm.value.set_temperature = row.row.set_temperature
  controlForm.value.room_temperature = row.row.room_temperature
  controlForm.value.voltage = row.row.voltage
  controlForm.value.electric_current = row.row.electric_current
  controlForm.value.power = row.row.power
  controlForm.value.electric_quantity = row.row.electric_quantity
  controlForm.value.responsible_person = row.row.responsible_person
  controlForm.value.wind_mode = row.row.wind_mode
  controlForm.value.standby_mode = row.row.standby_mode
}

function electrifyChange() {
  controlForm.value.power_state = false
}

function closeControl() {
  controlVisible.value = !controlVisible.value
}


const logForm = reactive({
  id: localStorage.getItem('token'),
  type: 2,
  client_id: '',
  content: ''
})


async function sureControl(id) {

  controlForm.value.power_state = controlForm.value.power_state === true ? '开机' : '关机'
  controlForm.value.online_state = controlForm.value.online_state === true ? '在线' : '离线'
  if (controlForm.value.electrify_state != null)
    controlForm.value.electrify_state = controlForm.value.electrify_state === true ? '通电' : '断电'
  else
    controlForm.value.electrify_state = ''
  logForm.client_id = tableClientId.value
  logForm.content = '操控了' + airBelongClientname.value + '的' + show_id.value + '号机 ' + controlForm.value.electrify_state + ' ' + controlForm.value.power_state + ' ' + controlForm.value.set_temperature + ' ' + controlForm.value.operation_mode + ' ' + controlForm.value.wind_speed + ' ' + controlForm.value.wind_mode
  await logCreateApi(logForm)
  controlVisible.value = !controlVisible.value
  let res = await controlAir(clientId.value, controlForm.value, show_id.value)
  if (res.data.code === 201) {
    ElMessage({
      message: '发送指令成功:)',
      type: 'success'
    })

    await initAirList(clientId.value, filters.value, pageSize.value, currentPage.value)
  } else {
    controlVisible.value = !controlVisible.value
    ElMessage({
      message: res.data.msg,
      type: 'error'
    })
  }
}

async function showEdit(row) {
  let res = await airDetailApi(row.row.id)
  airBelongClientname.value = res.data.clientname
  show_id.value = row.row.show_id
  editVisible.value = !editVisible.value
  colId.value = row.row.id
  editForm.value.designation = row.row.designation
  editForm.value.air_brand = row.row.air_brand
  editForm.value.responsible_person = row.row.responsible_person
}

function closeEdit() {
  editVisible.value = !editVisible.value
}

async function sureEdit(id) {
  let res = await updateAir(id, editForm.value)
  if (res.code === 201) {
    logForm.client_id = tableClientId.value
    logForm.content = '修改了' + airBelongClientname.value + '的' + show_id.value + '号机 ' + '的信息'
    await logCreateApi(logForm)
    ElMessage({
      message: '更新成功:)',
      type: 'success'
    })
    initAirList(clientId.value, filters.value, pageSize.value, currentPage.value)
    editVisible.value = !editVisible.value
  }
}

const handleCurrentChange = () => {
  initAirList(clientId.value, filters.value, pageSize.value, currentPage.value)
}

const handleSizeChange = () => {
  initAirList(clientId.value, filters.value, pageSize.value, currentPage.value)
}

const getAirTrueData = async () => {
  ElMessageBox.confirm(
      '读取真实数据时间较长,要继续吗?',
      '警告',
      {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }
  )
      .then(async () => {
        const loading = ElLoading.service({
          lock: true,
          text: '正在读取真实数据,时间较长,请耐心等待...',
          background: 'rgba(0, 0, 0, 0.7)'
        })
        const res = await getAirTrueDataApi(clientId.value)
        if (res.data.code === 200) {
          logForm.client_id = tableClientId.value
          logForm.content = '读取了' + selectData.value.clientname + '的最新的空调数据'
          await logCreateApi(logForm)
          ElMessage({
            message: res.data.msg,
            type: 'success'
          })
          await initAirList(clientId.value, filters.value, pageSize.value, currentPage.value)
          loading.close()
        } else {
          ElMessage({
            message: res.data.msg,
            type: 'error'
          })
          loading.close()
        }
      })
      .catch(() => {

      })


}

watch(controlForm.value, (val) => {
  if (val.power_state === false)
    controlForm.value.power_state = '关机'
})

// 强控
const forceControlVisible = ref(false)
const forceControl = () => {
  controlForm.value = {client_id: clientId.value, power_state: '关机'}
  forceControlVisible.value = true
}
const sureForceControl = async () => {
  const res = await groupControlApi(0, controlForm.value)
  if (res.code === 201) {
    logForm.client_id = tableClientId.value
    logForm.content = '强控了' + selectData.value.clientname + '下的所有空调' + ' ' + controlForm.value.power_state + ' ' + controlForm.value.set_temperature + ' ' + controlForm.value.operation_mode + ' ' + controlForm.value.wind_speed + ' ' + controlForm.value.wind_mode
    await logCreateApi(logForm)
    forceControlVisible.value = false
    ElMessage.success(res.msg)
    await initAirList(clientId.value, filters.value, pageSize.value, currentPage.value)
  }
}

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
.searchTemp,
.searchOnline,
.buttonSide {
  display: flex;
  align-items: center;
}

.searchName span,
.searchTemp span,
.searchOnline span {
  margin-right: 10px;
}

.searchTemp {
  margin-left: 10px; /* 添加间距 */
}

.searchOnline {
  margin-left: 20px; /* 调整间距 */
}

.buttonSide el-button:first-child {
  margin-right: 10px; /* 添加间距 */
}

.control-panel {
  margin-top: 20px;
}

.card-item {
  margin-bottom: 20px;
}


.control-options {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
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

.part1 .el-row {
  margin-bottom: -20px;
}

.part2 {
  margin-top: 15px;
}


.part2 .el-row {
  margin-bottom: 20px;
  padding-left: 15px;
}

.row-color-red {
  color: red;
}

.btnIcon {
  vertical-align: middle;
  margin-right: 4px;
}

.btnWord {
  vertical-align: middle;
}

</style>
