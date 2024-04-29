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
            <el-button v-if="isSystem || mainFlag === 1" type="primary"
                       style="margin-right: 20px" @click="addVisible = true">
              新增组
            </el-button>
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
          <el-table-column fixed="right" label="操作">
            <template #default="row">
              <el-tooltip content="设置策略" placement="top"
                          v-if="isSystem || (client_id === localClient && mainFlag === 1)">
                <el-button link type="primary" size="default">
                  <el-icon>
                    <Compass/>
                  </el-icon>
                </el-button>
              </el-tooltip>
              <el-tooltip content="编辑" placement="top"
                          v-if="isSystem || (client_id === localClient && mainFlag === 1)">
                <el-button link type="primary" size="default" @click="showEditGroup(row.row)">
                  <el-icon>
                    <Edit/>
                  </el-icon>
                </el-button>
              </el-tooltip>
              <el-tooltip content="删除" placement="top"
                          v-if="isSystem || (client_id === localClient && mainFlag === 1)">
                <el-button
                    link
                    type="primary" size="default"
                    @click="showDeleteGroup(row.row)">
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
      </div>

    </el-col>
  </el-row>
  <!--新增-->
  <el-dialog v-model="addVisible" title="新增组" width="500" :close-on-click-modal="false">
    <el-form :model="addForm" :label-position='"top"'>
      <el-form-item label="组名">
        <el-input v-model="addForm.name" autocomplete="off"/>
      </el-form-item>
      <el-form-item label="简介">
        <el-input v-model="addForm.info" autocomplete="off"/>
      </el-form-item>
    </el-form>
    <template #footer>
      <div class="dialog-footer">
        <el-button @click="addVisible = false">取消</el-button>
        <el-button type="primary" @click="sureAddGroup">
          确认
        </el-button>
      </div>
    </template>
  </el-dialog>

  <!--编辑-->
  <el-dialog v-model="editVisible" title="编辑组" width="500" :close-on-click-modal="false">
    <el-form :model="editForm" :label-position='"top"'>
      <el-form-item label="组名">
        <el-input v-model="editForm.name" autocomplete="off"/>
      </el-form-item>
      <el-form-item label="简介">
        <el-input v-model="editForm.info" autocomplete="off"/>
      </el-form-item>
    </el-form>
    <template #footer>
      <div class="dialog-footer">
        <el-button @click="editVisible = false">取消</el-button>
        <el-button type="primary" @click="sureEditGroup">
          确认
        </el-button>
      </div>
    </template>
  </el-dialog>

  <!--删除-->
  <el-dialog v-model="deleteVisible" title="警告！" width="500" :close-on-click-modal="false">
    <span style="color: red">你正在进行删除组 <span style="font-size: 24px">{{
        deleteName
      }}</span> 的操作，确认吗？</span>
    <template #footer>
      <div class="dialog-footer">
        <el-button @click="deleteVisible = false">取消</el-button>
        <el-button type="primary" @click="sureDeleteGroup">
          确认
        </el-button>
      </div>
    </template>
  </el-dialog>
</template>

<script setup>
import ClientTree from '@/components/ClientTree.vue'
import {onMounted, ref} from 'vue'
import eventBus from '@/listen/event-bus.js'
import useAuthControl from '@/hooks/useAuthControl.js'
import {useGroupAdd, useGroupDelete, useGroupEdit, useGroupList} from '@/hooks/views/energy/useAirGroupList.js'

eventBus.off('defaultNode')
eventBus.off('node-clicked')
const tree = ref()
// 权限控制
const localClient = parseInt(localStorage.getItem('client_id'))
const {isSystem, mainFlag} = useAuthControl()

// 表格展示
const {
  tableData,
  currentPage,
  total,
  pageSize,
  title,
  name,
  client_id,
  handleSizeChange,
  handleCurrentChange,
  reset,
  search
} = useGroupList()

// 添加组
const {addForm, addVisible, sureAddGroup} = useGroupAdd()

// 编辑组
const {editForm, editVisible, showEditGroup, sureEditGroup} = useGroupEdit()

// 删除组
const {deleteVisible, deleteName, showDeleteGroup, sureDeleteGroup} = useGroupDelete()
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
