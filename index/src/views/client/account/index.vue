<template>
  <el-row>
    <el-col :span="4">
      <ClientTree ref="tree" :tree="tree"/>
    </el-col>
    <el-col :span="20">
      <div class="right-content">
        <h2 style="margin-bottom: 20px;text-align: center">{{ title }}</h2>
        <el-button type="primary" style="margin-bottom: 10px" @click="showAccountCreate">新增账号</el-button>
        <el-table :data="tableManageData">
          <el-table-column property="account.account" label="账号"/>
          <el-table-column property="account.nickname" label="昵称"/>
          <el-table-column property="account.email" label="邮箱"/>
          <el-table-column property="account.mobile" label="手机号"/>
          <el-table-column fixed="right" label="操作">
            <template #default="row">
              <el-tooltip content="修改密码" placement="top">
                <el-button link type="primary" size="default" @click="showCpd(row)">
                  <el-icon>
                    <Compass/>
                  </el-icon>
                </el-button>
              </el-tooltip>
              <el-tooltip content="编辑" placement="top">
                <el-button link type="primary" size="default" @click="showInnerEdit(row)">
                  <el-icon>
                    <Edit/>
                  </el-icon>
                </el-button>
              </el-tooltip>
              <el-tooltip content="删除" placement="top">
                <el-button v-if="accountStore.accountData.id !== row.row.id" link type="primary" size="default"
                           @click="showAccountDelete(row)">
                  <el-icon>
                    <Delete/>
                  </el-icon>
                </el-button>
              </el-tooltip>
            </template>
          </el-table-column>
        </el-table>
      </div>
    </el-col>
  </el-row>

  <!--修改密码-->
  <el-dialog v-model="showCpdVisible" title="修改密码" width="500" :close-on-click-modal="false">
    <el-form :model="cpwForm" :label-position="'top'">
      <el-form-item label="新密码">
        <el-input v-model="cpwForm.password" type="password" autocomplete="off"/>
      </el-form-item>
      <el-form-item label="确认密码">
        <el-input v-model="cpwForm.password_confirmation" type="password" autocomplete="off"/>
      </el-form-item>
    </el-form>
    <template #footer>
      <div class="dialog-footer">
        <el-button @click="cancelCpd">取消</el-button>
        <el-button type="primary" @click="sureChange">
          确认
        </el-button>
      </div>
    </template>
  </el-dialog>
  <!--修该信息-->
  <el-dialog v-model="innerEditVisible" title="修改信息" width="500" :close-on-click-modal="false">
    <el-form :model="innerEditForm" :label-position='"top"'>
      <el-form-item label="昵称">
        <el-input v-model="innerEditForm.nickname" autocomplete="off"/>
      </el-form-item>
      <el-form-item label="邮箱">
        <el-input v-model="innerEditForm.email" autocomplete="off"/>
      </el-form-item>
      <el-form-item label="手机号">
        <el-input v-model="innerEditForm.mobile" autocomplete="off"/>
      </el-form-item>
    </el-form>
    <template #footer>
      <div class="dialog-footer">
        <el-button @click="cancelInnerEdit">取消</el-button>
        <el-button type="primary" @click="sureInnerEdit">
          确认
        </el-button>
      </div>
    </template>
  </el-dialog>
  <!--新增账号-->
  <el-dialog v-model="accountCreateVisible" title="新增账号" width="500" :close-on-click-modal="false">
    <el-form :model="accountCreateForm" :label-position='"top"'>
      <el-form-item label="账号">
        <el-input v-model="accountCreateForm.account" autocomplete="off"/>
      </el-form-item>
      <el-form-item label="密码">
        <el-input v-model="accountCreateForm.password" autocomplete="off" type="password"/>
      </el-form-item>
      <el-form-item label="确认密码">
        <el-input v-model="accountCreateForm.password_confirmation" autocomplete="off" type="password"/>
      </el-form-item>
      <el-form-item label="昵称">
        <el-input v-model="accountCreateForm.nickname" autocomplete="off"/>
      </el-form-item>
      <el-form-item label="邮箱">
        <el-input v-model="accountCreateForm.email" autocomplete="off"/>
      </el-form-item>
      <el-form-item label="手机号">
        <el-input v-model="accountCreateForm.mobile" autocomplete="on"/>
      </el-form-item>
    </el-form>
    <template #footer>
      <div class="dialog-footer">
        <el-button @click="showAccountCreate">取消</el-button>
        <el-button type="primary" @click="sureAccountCreate">
          确认
        </el-button>
      </div>
    </template>
  </el-dialog>
  <!--删除账号-->
  <el-dialog v-model="accountDeleteVisible" title="警告！" width="500" :close-on-click-modal="false">
    <span style="color: red">你正在进行删除（{{ accountDelName }}）账户的操作，确认吗？</span>
    <template #footer>
      <div class="dialog-footer">
        <el-button @click="showAccountDelete">取消</el-button>
        <el-button type="primary" @click="sureAccountDelete">
          确认
        </el-button>
      </div>
    </template>
  </el-dialog>
</template>

<script setup>
import ClientTree from "@/components/ClientTree.vue"
import useAccountManage from '@/hooks/views/client/account/useAccountManage.js'
import useChangePwd from '@/hooks/views/client/account/useChangePwd.js'
import useInnerEdit from '@/hooks/views/client/account/useInnerEdit.js'
import eventBus from '@/listen/event-bus.js'
import {onMounted, ref} from 'vue'
import useAccountCreate from '@/hooks/views/client/account/useAccountCreate.js'
import useAccountDelete from "@/hooks/views/client/account/useAccountDelete.js";
import {useClientStore} from "@/store/client.js";
import {storeToRefs} from 'pinia'
import {useAccountStore} from '@/store/account.js'

const tree = ref()
const title = ref()
const client_id = ref()
// 从pinia读数据
const clientStore = useClientStore()
const {tableManageData} = storeToRefs(clientStore)
const accountStore = useAccountStore()

/*// 账号管理(目前失去作用 使用pinia代替)
const {accountList} = useAccountManage()*/

// 账号管理下的修改密码
const {showCpdVisible, cancelCpd, cpwForm, showCpd, sureChange} = useChangePwd()

// 修改账号昵称
const {showInnerEdit, cancelInnerEdit, innerEditVisible, innerEditForm, sureInnerEdit} = useInnerEdit()

// 新增账号
const {accountCreateForm, accountCreateVisible, showAccountCreate, sureAccountCreate} = useAccountCreate()

// 删除账号
const {accountDeleteVisible, showAccountDelete, sureAccountDelete, accountDelName} = useAccountDelete()

eventBus.on('node-clicked', (val) => {
  title.value = val.clientname
  client_id.value = val.id
  // 账号管理
  clientStore.accountList(val.id)
})


onMounted(() => {
  eventBus.on('defaultNode', (val) => {
    eventBus.emit('node-clicked', val)
    clientStore.clientId = val.id
  })

})

</script>

<style scoped lang="scss">

</style>