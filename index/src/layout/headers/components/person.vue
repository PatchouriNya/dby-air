<template>
  <div class="person">
    <el-dropdown>
    <span class="el-dropdown-link">
      {{ accountData && accountData.nickname }}
      <el-icon class="el-icon--right">
      </el-icon>
    </span>
      <template #dropdown>
        <el-dropdown-menu>
          <el-dropdown-item @click="logout">退出系统</el-dropdown-item>
          <el-dropdown-item @click="returnIndex">返回主页</el-dropdown-item>
          <el-dropdown-item @click="showCpd">修改密码</el-dropdown-item>
          <el-dropdown-item @click="showClientEdit">客户信息</el-dropdown-item>
        </el-dropdown-menu>
      </template>
    </el-dropdown>
  </div>

  <!--  修改密码-->
  <el-dialog v-model="showCpdVisible" title="修改密码" width="500">
    <el-form :model="cpwForm" :label-position="'top'">
      <el-form-item label="旧密码">
        <el-input v-model="cpwForm.ori_password" type="password" autocomplete="off"/>
      </el-form-item>
      <el-form-item label="新密码">
        <el-input v-model="cpwForm.password" type="password" autocomplete="off"/>
      </el-form-item>
      <el-form-item label="确认密码">
        <el-input v-model="cpwForm.password_confirmation" type="password" autocomplete="off"/>
      </el-form-item>
    </el-form>
    <template #footer>
      <div class="dialog-footer">
        <el-button @click="showCpd">取消</el-button>
        <el-button type="primary" @click="sureChange">
          确认
        </el-button>
      </div>
    </template>
  </el-dialog>

  <!--  客户信息-->
  <el-dialog v-model="clientEditVisible" title="当前客户" width="500" :close-on-click-modal="false">
    <el-form :model="clientEditForm" :label-position='"top"'>
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
        <el-input v-model="clientEditForm.info" autocomplete="on" type="textarea" :rows="8"/>
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
</template>

<script setup>
import useChangePwd from '@/hooks/layout/headers/components/person/useChangePwd'
import useLogOut from '@/hooks/layout/headers/components/person/useLogOut'
import useReturnIndex from '@/hooks/layout/headers/components/person/useReturnIndex'
import {useAccountStore} from '@/store/account.js'
import {storeToRefs} from 'pinia'
import useClientEdit from '@/hooks/layout/headers/components/person/useClientEdit.js'

// 修改密码
const {showCpdVisible, cpwForm, showCpd, sureChange} = useChangePwd()

// 登出
const {logout} = useLogOut()

// 返回主页
const {returnIndex} = useReturnIndex()

const accountStore = useAccountStore()

// 编辑客户
const {
  clientEditForm,
  clientEditVisible,
  showClientEdit,
  sureClientEdit,
  cancelClientEdit
} = useClientEdit()

const {accountData} = storeToRefs(accountStore)
</script>


<style scoped lang="scss">
.example-showcase .el-dropdown-link {
  cursor: pointer;
  color: var(--el-color-primary);
  display: flex;
  align-items: center;
}

.person {
  position: absolute;
  right: 0;
}

// 去黑框
.el-dropdown-link:focus {
  outline: none;
}
</style>