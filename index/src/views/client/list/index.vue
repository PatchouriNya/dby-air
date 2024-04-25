<template>
  <el-button v-if="flag && mainFlag === 1" type="primary" style="margin-bottom: 10px"
             @click="showClientCreate(false)">
    新增客户
  </el-button>
  <el-table
      :data="tableData"
      style="width: 100%; margin-bottom: 20px"
      row-key="id"
      border
      empty-text="该客户为实际单位或数据为空，如需修改，请在右上角修改详细信息"
      :row-class-name="tableRowClass"
      highlight-current-row
  >
    <el-table-column prop="clientname" label="名称" :width="300" sortable/>
    <el-table-column prop="province" label="省份" sortable/>
    <el-table-column prop="city" label="城市" sortable/>
    <el-table-column prop="district" label="市区" sortable/>
    <el-table-column fixed="right" label="操作">
      <template #default="row">
        <el-tooltip v-if="row.row.type !== 1 && mainFlag === 1" content="添加子客户" placement="top">
          <el-button link type="primary" size="default" @click="showClientCreate(row)">
            <el-icon>
              <Plus/>
            </el-icon>
          </el-button>

        </el-tooltip>
        <el-button v-else link type="primary" size="default" style="cursor: auto">
          <el-icon>

          </el-icon>
        </el-button>
        <el-tooltip content="编辑" placement="top">
          <el-button v-if="mainFlag === 1" link type="primary" size="default" @click="showClientEdit(row.row)">
            <el-icon>
              <Edit/>
            </el-icon>
          </el-button>
        </el-tooltip>
        <el-tooltip v-if="!row.row.children && mainFlag === 1" content="删除" placement="top">
          <el-button link type="primary" size="default" @click="showClientDelete(row.row)">
            <el-icon>
              <Delete/>
            </el-icon>
          </el-button>
        </el-tooltip>
      </template>
    </el-table-column>
  </el-table>

  <el-dialog v-model="clientCreateVisible" title="新增客户" width="500" :close-on-click-modal="false">
    <el-form :model="clientCreateForm" :label-position='"top"'>
      <el-form-item label="类型">
        <el-switch v-model="clientCreateForm.type" active-text="实际单位" inactive-text="目录单位" :active-value="1"
                   :inactive-value="0"/>
      </el-form-item>
      <el-form-item label="客户名称">
        <el-input v-model="clientCreateForm.clientname" autocomplete="off"/>
      </el-form-item>
      <el-form-item label="省份">
        <el-input v-model="clientCreateForm.province" autocomplete="off"/>
      </el-form-item>
      <el-form-item label="城市">
        <el-input v-model="clientCreateForm.city" autocomplete="off"/>
      </el-form-item>
      <el-form-item label="区县">
        <el-input v-model="clientCreateForm.district" autocomplete="off"/>
      </el-form-item>
      <el-form-item label="pid" v-show="false">
        <el-input v-model="clientCreateForm.pid" autocomplete="off" disabled/>
      </el-form-item>
      <el-form-item label="简介">
        <el-input v-model="clientCreateForm.info" autocomplete="on" type="textarea"/>
      </el-form-item>

    </el-form>
    <template #footer>
      <div class="dialog-footer">
        <el-button @click="cancelClientCreate">取消</el-button>
        <el-button type="primary" @click="sureClientCreate">
          确认
        </el-button>
      </div>
    </template>
  </el-dialog>

  <el-dialog v-model="clientEditVisible" title="编辑客户" width="500" :close-on-click-modal="false">
    <el-form :model="clientEditForm" :label-position='"top"'>
      <el-form-item label="类型">
        <el-switch v-model="clientEditForm.type" active-text="实际单位" inactive-text="目录单位" :active-value="1"
                   :inactive-value="0" disabled/>
      </el-form-item>
      <el-form-item label="所属单位">
        <el-select style="margin-top: 15px"
                   v-model="selectValue"
                   placeholder="请选择"
        >
          <el-tree
              :data="treeData"
              :props="defaultProps"
              accordion
              @node-click="handleNodeClick"
          />
          <el-option v-show="false" :value="1"/>

        </el-select>
      </el-form-item>
      <el-form-item label="客户名称">
        <el-input v-model="clientEditForm.clientname" autocomplete="off"/>
      </el-form-item>
      <el-form-item label="省份">
        <el-input v-model="clientEditForm.province" autocomplete="off"/>
      </el-form-item>
      <el-form-item label="城市">
        <el-input v-model="clientEditForm.city" autocomplete="off"/>
      </el-form-item>
      <el-form-item label="区县">
        <el-input v-model="clientEditForm.district" autocomplete="off"/>
      </el-form-item>
      <el-form-item label="简介">
        <el-input v-model="clientEditForm.info" autocomplete="on" type="textarea"/>
      </el-form-item>
      <el-form-item label="pid" v-show="false">
        <el-input v-model="clientCreateForm.pid" autocomplete="off" disabled/>
      </el-form-item>

    </el-form>
    <template #footer>
      <div class="dialog-footer">
        <el-button @click="cancelClientEdit">取消</el-button>
        <el-button type="primary" @click="sureClientEdit">
          确认
        </el-button>
      </div>
    </template>
  </el-dialog>

  <el-dialog v-model="clientDeleteVisible" title="警告！" width="500" :close-on-click-modal="false">
    <span style="color: red">你正在进行删除客户（{{ clientDelName }}）的操作，<br>此操作会导致该客户的所有账号、空调等数据一同销毁！<br>确认吗？</span>
    <template #footer>
      <div class="dialog-footer">
        <el-button @click="showClientDelete">取消</el-button>
        <el-button type="primary" @click="sureClientDelete">
          确认
        </el-button>
      </div>
    </template>
  </el-dialog>
</template>


<script setup>
import useTable from '@/hooks/views/client/list/useTable.js'
import {useClientWithoutHeadStore} from '@/store/clientWithoutHead.js'
import {storeToRefs} from 'pinia'
import useClientCreate from '@/hooks/views/client/list/useClientCreate.js'
import useClientDelete from '@/hooks/views/client/list/useClientDelete.js'
import useClientEdit from '@/hooks/views/client/list/useClientEdit.js'
import {ref} from 'vue'
import {account} from '@/api/account.js'

const clientWithoutHeadStore = useClientWithoutHeadStore()
const {tableData, flag} = storeToRefs(clientWithoutHeadStore)

function tableRowClass({row}) {
  if (row.type === 0)
    return 'row-color-blue'
}

// 是否为主管
const mainFlag = ref()

// 初始化
getAccountData()

async function getAccountData() {
  let res = await account()
  mainFlag.value = res.data.main
}

// 添加客户
const {
  clientCreateForm,
  clientCreateVisible,
  showClientCreate,
  sureClientCreate,
  cancelClientCreate
} = useClientCreate()

// 删除客户
const {clientDeleteVisible, showClientDelete, sureClientDelete, clientDelName} = useClientDelete()

// 编辑客户
const {
  clientEditForm,
  clientEditVisible,
  showClientEdit,
  sureClientEdit,
  cancelClientEdit, treeData, selectValue
} = useClientEdit()
// 表格显示
useTable()

const options = [
  {
    value: 'Option1',
    label: 'Option1'
  }
]
// 树结构
const defaultProps = {
  children: 'childs_select',
  label: 'clientname'
}


const handleNodeClick = (treeData) => {
  selectValue.value = treeData.clientname
  clientEditForm.pid = treeData.id
}

</script>

<style>
.row-color-blue {
  color: darkblue;
}
</style>